<?php

class Payment_Alipay extends Payment {

    protected function set_params() {
        $this->params = array(
            "service" => "create_direct_pay_by_user",
            "payment_type" => "1",
            "partner" => $this->_config['partner'],
            "_input_charset" => strtolower($this->_config['input_charset']),
            "seller_email" => $this->_config['seller_email'],
            "return_url" => $this->_config['return_url'],
            "notify_url" => $this->_config['notify_url'],
            "out_trade_no" => $this->_config['order_id'],
            "subject" => '交易编号 - ' . $this->_config['order_id'],
            "body" => '',
            "total_fee" => $this->_config['order_amount'] 
        );
    }

    protected function create_sign($params) {
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

    public function is_alipay_sign($params) {
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
            $responseTxt = $this->curl($veryfy_url);
        }
        
        if (preg_match("/true$/i", $responseTxt) && $mysign == $params["sign"]) {
            return true;
        } else {
            //Kohana::$log->add(Log::ERROR, 'alipay 即时到帐支付  验证错误：'.$mysign.'--'.$params["sign"]);
            return false;
        }
    }

    public function get_request_url() {
        $this->set_params();
        $sign = $this->create_sign($this->params);
        $this->params['sign'] = $sign;
        $this->params['sign_type'] = $this->_config['sign_type'];
        return 'https://mapi.alipay.com/gateway.do?' . http_build_query($this->params);
    }

    protected function curl($url) {
        $cacert_url = Kohana::find_file('config', 'cacert', 'pem');
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => $cacert_url,
            CURLOPT_TIMEOUT => 4 
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
	