<?php

class Controller_Sms extends Controller_Ajax {

    public function action_send() {
        $type = Arr::get($_GET, 'type', 'reg');//默认为注册用
        $type = in_array($type, array('reg', 'findpwd')) ? $type : 'reg';
        $phone = Arr::get($_GET, 'phone');
        
        if(empty($phone)) {
            $this->response = array('status'=>'n', 'info'=>'请输入手机号码！');
            return;
        }
        
        if(!preg_match("/1[34578]{1}\d{9}$/",$phone)) {
            $this->response = array('status'=>'n', 'info'=>'手机号码不正确！');
            return;
        }
        
        $m_sms = Model::factory('sms_queue');

        $where = array('ORDER'=>'id DESC', 'phone'=>$phone, 'type'=>$type);
        $lastsms = $m_sms->getRow($where);
        if (!empty($lastsms)) {
            $timeleft = 60 + $lastsms['add_time'] - strtotime('now'); //还剩几秒可以重发
            if ($timeleft > 0) {
                $this->response = array('status'=>'y', 'info'=>'请'.$timeleft.'秒后重发', 'timeleft'=>$timeleft);
                return;
            }
        }
        
        $start_time = strtotime('now')-3600;
        $end_time = strtotime('now');
        $where = array('phone'=>$phone, 'add_time|>'=>$start_time, 'add_time|<='=>$end_time);
        $sms_num = $m_sms->count($where);
        if($sms_num > 4) {//每小时最多发5条
            $this->response = array('status'=>'n', 'info'=>'您发送短信频率太高！请稍后再发');
            return;
        }
        
        $start_time = strtotime('now');
        $end_time = strtotime('+1 day');
        $where = array('phone'=>$phone, 'add_time|>'=>$start_time, 'add_time|<='=>$end_time);
        $sms_num = $m_sms->count($where);
        if($sms_num > 9) {//每天最多发10条
            $this->response = array('status'=>'n', 'info'=>'您今天已超过发送短信限制！请明天再发');
            return;
        }

        $sms_verify = Text::random('numeric', 5);
        $session = Session::instance();
        $session->set('sms_verify', $sms_verify);
        
        $content = Kohana::config('sms.'.$type);
        $content = sprintf($content, $sms_verify);
        $data = array(
            'type' => $type,
            'phone' => $phone,
            'code' => $sms_verify,
            'content'=> $content,
            'add_time' => time(),
        );
        $m_sms->insert($data);
    
        //Sms::send($phone, $content);
        
        $this->response = array('status'=>'y', 'info'=>'验证码已发送，请填写手机验证码！');
    }

}

