<?php
return array(
    'token' => 'wxtest',
    'appid' => 'wxc5b1d86df49a2dc4',
    'appsecret' => '50200b8e4eb49d9171835e6acea44955',
    'encodingAesKey' => '',
    
    'menu' => array(
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
    ),
);