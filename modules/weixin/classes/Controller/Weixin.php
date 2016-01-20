<?php

class Controller_Weixin extends Controller {

    public $auto_render = false;
    
    public function action_server() {
        $config = Kohana::config('weixin');
        $token = $config['token'];
        $appId = $config['appId'];
        $encodingAesKey = $config['encodingAesKey'];
        $wechat = new MyWechat($token, $encodingAesKey, $appId);
        $wechat->run();
    }
    
    public function action_login() {
        $wx_user = Cookie::get('wx_user');
        if (empty($wx_user)) {
            $callback_url = URL::site('weixin/callback', true);
            $redirect_url = URL::site('weixin/index', true);

            $wx = new WeixinOauth();
            $login_url = $wx->get_login_url($callback_url, 'snsapi_userinfo', $redirect_url);
            $this->redirect($login_url);
        }
        var_dump($wx_user);
    }

    public function action_callback() {
        $code = Arr::get($_GET, 'code');
        $state = Arr::get($_GET, 'state');
        
        $wx = new WeixinOauth();
        $ret_json = $wx->get_access_token($code);
        $ret_array = json_decode($ret_json, true);

        $access_token = $ret_array['access_token'];
        $openid = $ret_array['openid'];
        
        $user_info = $wx->get_user_info($access_token, $openid);
        $wx_user = json_decode($user_info, true);
        if (isset($wx_user['openid'])) {
            foreach ($wx_user as $k => $v) {
                if (!in_array($k, array('openid','nickname','headimgurl','sex','country','province','city'))) {
                    unset($wx_user[$k]);
                } 
            }
            $wx_user['last_login'] = strtotime('now');
            $m_wxuser = Model::factory('wx_user');
            $m_wxuser->replace_into($wx_user);
            
            Cookie::set('wx_user', $user_info, 7200);
        }
        $this->redirect($state);
    }

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
    
}
