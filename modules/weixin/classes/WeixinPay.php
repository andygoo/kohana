<?php

class WeixinPay {
    /*
    $params = array(
            "openid" => $follow['openid'], //接受红包的用户, 用户在wxappid下的openid，服务商模式下可填入msgappid下的openid。
            "total_amount" => $amount, //付款金额，单位分
            "total_num" => 1, //红包发放总人数
            "mch_billno" => sprintf("10125753%s%11d", date('Ymd'), $phone),
            "act_name" => "卖车红包", //活动名称: 猜灯谜抢红包活动
            "remark" => "卖车红包", //备注信息: 猜越多得越多，快来抢！
            "wishing" => "请爷收银子", //红包祝福语: 感谢您参加猜灯谜活动，祝您元宵节快乐！
    );*/
    public static function sendRedEnvalope($params) {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $params['mch_id'] = WX_MCH_ID; //微信支付分配的商户号 10000098
        $params['wxappid'] = WX_APP_ID; //微信分配的公众账号ID（企业号corpid即为此appId）wx8888888888888888
        $params['nick_name'] = WX_NAME; //提供方名称 天虹百货
        $params['send_name'] = WX_NAME; //红包发送者名称 天虹百货
        $params['client_ip'] = WX_CLIENT_IP; //调用接口的机器Ip地址
        $params['re_openid'] = $params['openid'];
        unset($params['openid']);
        $params['nonce_str'] = $this->random(16);
        $params['sign'] = $this->sign($params);

        $params_xml = array2xml($params_xml);
        $opts = array(
            CURLOPT_SSLCERT => 'apiclient_cert.pem',
            CURLOPT_SSLKEY => 'apiclient_key.pem',
            CURLOPT_CAINFO => 'rootca.pem',
        );
        $ret_xml = CURL::post($url, $params_xml, $opts);
        $ret_array = xml2array($ret_xml);
        return $ret_array;
    }
    
    /*
        $params = array(
            "openid"=>$follow['openid'], //商户appid下，某用户的openid
            "amount"=>$amount*100, //企业付款金额，单位为分
            "partner_trade_no"=>'10000098201411111234567890',//商户订单号，需保持唯一性
            "desc"=>"好车无忧独家车源红包", //企业付款操作说明信息。必填。理赔
        );
     */
    public function sendPayment($params) {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $params['mchid'] = WX_MCH_ID;  //微信支付分配的商户号 1900000109
        $params['mch_appid'] = WX_APP_ID; //微信分配的公众账号ID（企业号corpid即为此appId） wx8888888888888888
        $params['spbill_create_ip'] = WX_CLIENT_IP; //调用接口的机器Ip地址
        $params['check_name'] = 'NO_CHECK';
        $params['nonce_str'] = $this->random(16);
        $params['sign'] = $this->sign($params);
        
        $params_xml = array2xml($params);
        $opts = array(
            CURLOPT_SSLCERT => 'apiclient_cert.pem',
            CURLOPT_SSLKEY => 'apiclient_key.pem',
            CURLOPT_CAINFO => 'rootca.pem',
        );
        $ret_xml = CURL::post($url, $params_xml, $opts);
        $ret_array = xml2array($ret_xml);
        return $ret_array;
    }

    public function random($length = 8) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool = str_split($pool, 1);
        $max = count($pool) - 1;
    
        $str = '';
        for($i = 0; $i < $length; $i++) {
            $str .= $pool[mt_rand(0, $max)];
        }
        return $str;
    }
    
    public function sign($data) {
        ksort($data);
        $data['key'] = 'haoche51haoche51haoche51';
        return strtoupper(md5(urldecode(http_build_query($data))));
    }
}

function xml2array($xmlstr) {
    $xml = simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);
    return $array;
}

function array2xml($data, $root = 'data', $xml = null) {
    if ($xml == null) {
        $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$root/>");
    }
    foreach($data as $key => $value) {
        if (is_array($value)) {
            if (is_int($key)) {
                $key = 'item';
            }
            $node = $xml->addChild($key);
            array2xml($value, $root, $node);
        } else {
            $value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            $xml->addChild($key, $value);
        }
    }
    return $xml->asXML();
}
