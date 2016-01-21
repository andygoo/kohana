<?php

class Controller_Weixin extends Controller {

    public $auto_render = false;
    
    public function action_create_menu() {
        $menu = array(
            'button' => array(
                array(
                    'name' => '菜单一',
                    'sub_button' => array(
                        array(
                            'name' => '菜单一 -2',
                            'type' => 'click',
                            'key' => 'click_1_2',
                        ),
                        array(
                            'name' => '菜单一 -1',
                            'type' => 'click',
                            'key' => 'click_1_1',
                        ),
                    ),
                ),
                array(
                    'name' => '菜单二',
                    'type' => 'click',
                    'key' => 'click_2_1',
                ),
                array(
                    'name' => '菜单三',
                    'type' => 'view',
                    'url' => 'http://baidu.com',
                ),
            ),
        );
        $wx = new Weixin();
        $ret = $wx->create_menu($menu);
        var_dump($ret);
    }
    
    public function action_send_template_message() {
        $param = array(
            'touser' => 'oUcKEtwIP8_0VlA2VsKd7dATujGQ',
            'template_id' => 'FfJM1NlKYlfy4Fhb1wcDc_li5lcDMGVHhaRF-y2ZmbE',
            //'url' => 'http://baidu.com',
            'data' => array(
                'aaa' => array('value'=>'aaaaa', 'color'=>'#00FF00'),
                'bbb' => array('value'=>'bbbbb', 'color'=>'#FF0000'),
            ),
        );
        $wx = new Weixin();
        $ret = $wx->send_template_message($param);
        var_dump($ret);
    }

    public function action_send_custom_message() {
        /*$param = array(
            'touser' => 'oUcKEtwIP8_0VlA2VsKd7dATujGQ',
            'msgtype' => 'text',
            'text' => array(
                'content' => '呵呵！hello',
            ),
        );*/
        $articles = array();
        $article = array(
            'title' => 'test 好车无忧',
            'description' => 'desc haoche51',
            'url' => 'http://m.haoche51.com/',
            'picurl'=> 'http://image1.haoche51.com/54b2b478ec-b556-4076-97dc-43bd2d2ef449.jpg?imageView2/1/w/280/h/210',
        );
        $articles[] = $article;
        $articles[] = $article;
        
        $param = array(
            'touser' => 'oUcKEtwIP8_0VlA2VsKd7dATujGQ',
            'msgtype' => 'news',
            'news' => array(
                'articles' => $articles,
            ),
        );
        $wx = new Weixin();
        $ret = $wx->send_custom_message($param);
        var_dump($ret);
    }
    
    public function action_create_qrcode() {
        $param = array(
            'expire_seconds' => 600,
            'action_name' => 'QR_SCENE',//QR_LIMIT_SCENE
            'action_info' => array(
                'scene' => array(
                    'scene_id' => '123',
                ),
            ),
        );
        $wx = new Weixin();
        $ret = $wx->create_qrcode($param);
        var_dump($ret);
        
        //$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ret['ticket'];
        //echo $url;
    }

    public function action_get_jsapi_config() {
        $wx = new WeixinJSAPI();
        $ret = $wx->get_jsapi_config();
        //var_dump($ret);
        
        $this->template = View::factory('share');
        $this->template->jsapi_config = $ret;
        echo $this->template;
    }

    public function action_sendredpack() {
        $phone = 13800000000;
        $params = array(
                "openid" => 'W34SD#@#DSDSDSDSAAAAAA', //接受红包的用户, 用户在wxappid下的openid，服务商模式下可填入msgappid下的openid。
                "total_amount" => 100, //付款金额，单位分
                "total_num" => 1, //红包发放总人数
                "mch_billno" => sprintf("10125753%s%11d", date('Ymd'), $phone),
                "act_name" => "卖车红包", //活动名称: 猜灯谜抢红包活动
                "remark" => "卖车红包", //备注信息: 猜越多得越多，快来抢！
                "wishing" => "请爷收银子", //红包祝福语: 感谢您参加猜灯谜活动，祝您元宵节快乐！
        );
        $ret = WeixinPay::sendredpack($params);
        var_dump($ret);
    }
}
