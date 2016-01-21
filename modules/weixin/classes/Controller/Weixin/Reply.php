<?php

class Controller_Weixin_Reply extends Controller {

    public $auto_render = false;
    
    public function action_index() {
        $token = 'wxtest';
        $appid = 'wxc5b1d86df49a2dc4';
        $encodingAesKey = '';
         
        $wechat = new MyWechat($token, $encodingAesKey, $appid);
        $wechat->run();
    }
}
