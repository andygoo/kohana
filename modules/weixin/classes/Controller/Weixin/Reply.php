<?php

class Controller_Weixin_Reply extends Controller {

    public function action_index() {
        $wx_config = Kohana::config('weixin.default');
        $token = $wx_config['token'];
        $appid = $wx_config['appid'];
        $encodingAesKey = $wx_config['encodingAesKey'];

        $wechat = new MyWechat($token, $encodingAesKey, $appid);
        $wechat->run();
    }
} 
