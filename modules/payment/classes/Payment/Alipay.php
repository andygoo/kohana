<?php

class Payment_Alipay extends Payment {

    public function get_pay_url($order_info) {
        $this->params = array(
            "service" => "create_direct_pay_by_user",
            "payment_type" => "1",
            "partner" => $this->_config['partner'],
            "seller_id" => $this->_config['partner'],
            "return_url" => $this->_config['return_url'],
            "notify_url" => $this->_config['notify_url'],
            "_input_charset" => isset($this->_config['charset']) ? strtolower($this->_config['charset']) : 'utf-8',
            
            "out_trade_no" => $order_info['order_id'],
            "subject" => '交易编号 - ' . $order_info['order_id'],
            "total_fee" => $order_info['order_amount'],
            "body" => '',
        );
        $sign = $this->create_sign($this->params);
        $this->params['sign'] = $sign;
        $this->params['sign_type'] = $this->_config['sign_type'];
        return 'https://mapi.alipay.com/gateway.do?' . http_build_query($this->params);
    }
    
    public function create_sign($params) {
        ksort($params);
        $arg = '';
        foreach($params as $key => $val) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $arg .= $key . '=' . $val . '&';
            }
        }
        $arg = substr($arg, 0, -1);
        return md5($arg . $this->_config['key']);
    }

    public function verify_sign($params) {
        if (empty($params)) {
            return false;
        }
        
        $mysign = $this->create_sign($params);
        $responseTxt = 'true';
        if (!empty($params["notify_id"])) {
            $notify_id = $params["notify_id"];
            $transport = strtolower(trim($this->_config['transport']));
            $partner = $this->_config['partner'];
            
            if ($transport == 'https') {
                $veryfy_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
            } else {
                $veryfy_url = 'http://notify.alipay.com/trade/notify_query.do?';
            }
            $veryfy_url .= 'partner=' . $partner . '&notify_id=' . $notify_id;
            $responseTxt = CURL::get($veryfy_url, '', array(CURLOPT_CAINFO => ''));
        }
        
        if (preg_match("/true$/i", $responseTxt) && $mysign == $params["sign"]) {
            return true;
        } else {
            throw new Kohana_Exception('alipay 即时到帐支付  验证错误：' . $mysign.'--'.$params["sign"]);
            return false;
        }
    }

}
	