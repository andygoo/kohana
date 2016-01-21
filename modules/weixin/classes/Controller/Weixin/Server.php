<?php

class Controller_Weixin_Server extends Controller {

    public $auto_render = false;
    
    public function action_index() {
        $config = Kohana::config('weixin');
        $token = $config['token'];
        $appId = $config['appId'];
        $encodingAesKey = $config['encodingAesKey'];
        $wechat = new MyWechat($token, $encodingAesKey, $appId);
        $wechat->run();
    }
    
}
