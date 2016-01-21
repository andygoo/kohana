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
    public static function sendredpack($params) {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $params['mch_id'] = '10000098'; //微信支付分配的商户号 10000098
        $params['wxappid'] = 'wx8888888888888888'; //微信分配的公众账号ID（企业号corpid即为此appId）wx8888888888888888
        $params['nick_name'] = '天虹百货'; //提供方名称 天虹百货
        $params['send_name'] = '天虹百货'; //红包发送者名称 天虹百货
        $params['client_ip'] = '127.0.0.1'; //调用接口的机器Ip地址
        $params['re_openid'] = $params['openid'];
        unset($params['openid']);
        $params['nonce_str'] = self::random(16);
        $params['sign'] = self::sign($params);

        $opts = array(
            CURLOPT_SSLCERT => 'apiclient_cert.pem',
            CURLOPT_SSLKEY => 'apiclient_key.pem',
            CURLOPT_CAINFO => 'rootca.pem',
        );
        $params_xml = self::array2xml($params);
        var_dump($params_xml);exit;
        $ret_xml = CURL::post($url, $params_xml, $opts);
        $ret_array = self::xml2array($ret_xml);
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
    public static function transfers($params) {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $params['mchid'] = WX_MCH_ID;  //微信支付分配的商户号 1900000109
        $params['mch_appid'] = WX_APP_ID; //微信分配的公众账号ID（企业号corpid即为此appId） wx8888888888888888
        $params['spbill_create_ip'] = WX_CLIENT_IP; //调用接口的机器Ip地址
        $params['check_name'] = 'NO_CHECK';
        $params['nonce_str'] = self::random(16);
        $params['sign'] = self::sign($params);
        
        $opts = array(
            CURLOPT_SSLCERT => 'apiclient_cert.pem',
            CURLOPT_SSLKEY => 'apiclient_key.pem',
            CURLOPT_CAINFO => 'rootca.pem',
        );
        $params_xml = self::array2xml($params);
        $ret_xml = CURL::post($url, $params_xml, $opts);
        $ret_array = self::xml2array($ret_xml);
        return $ret_array;
    }

    protected static function sign($data) {
        ksort($data);
        $data['key'] = 'haoche51haoche51haoche51';
        return strtoupper(md5(urldecode(http_build_query($data))));
    }

    protected static function random($length = 8) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool = str_split($pool, 1);
        $max = count($pool) - 1;
    
        $str = '';
        for($i = 0; $i < $length; $i++) {
            $str .= $pool[mt_rand(0, $max)];
        }
        return $str;
    }
    
    protected static function xml2array($xmlstr) {
        $xml = simplexml_load_string($xmlstr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        return $array;
    }
    
    protected static function array2xml($data) {
        $xmlstr = "<xml>\n";
        foreach ($data as $key => $value) {
            $xmlstr .= "<$key>$value</$key>\n";
        }
        $xmlstr .= "</xml>";
        return $xmlstr;
    }

}
