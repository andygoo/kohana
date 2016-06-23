<?php

class Payment_Tenpay extends Payment {

    protected function set_params() {
        $this->params = array(
            "cmdno" => "1", //任务代码
            "date" => date("Ymd"), //日期
            "bargainor_id" => $this->_config['mem_id'], //商户号
            "transaction_id" => $this->_config['mem_id'] . date('YmdHis') . rand(1000, 9999), //财付通交易单号:10位商户号+8位时间（YYYYmmdd)+10位流水号
            "sp_billno" => $this->_config['order_id'], //商家订单号
            "total_fee" => $this->_config['order_amount'], //商品价格，以分为单位
            "fee_type" => "1", //货币类型
            "return_url" => $this->_config['return_url'], //返回url
            "attach" => "", //自定义参数
            "spbill_create_ip" => "", //用户ip
            "desc" => $this->_config['order_desc'], //商品名称
            "bank_type" => "0", //银行编码
            "cs" => "utf-8", //字符集编码
            "sign" => ""  //摘要
        );
    }

    protected function create_sign($params) {
        $cmdno = $params["cmdno"];
        $date = $params["date"];
        $bargainor_id = $params["bargainor_id"];
        $transaction_id = $params["transaction_id"];
        $sp_billno = $params["sp_billno"];
        $total_fee = $params["total_fee"];
        $fee_type = $params["fee_type"];
        $return_url = $params["return_url"];
        $attach = $params["attach"];
        $spbill_create_ip = $params["spbill_create_ip"];
        
        $sign = "cmdno=" . $cmdno . "&" . "date=" . $date . "&" . "bargainor_id=" . $bargainor_id . "&" . "transaction_id=" . $transaction_id . "&" . "sp_billno=" . $sp_billno . "&" . "total_fee=" . $total_fee . "&" . "fee_type=" . $fee_type . "&" . "return_url=" . $return_url . "&" . "attach=" . $attach . "&";
        
        if ($spbill_create_ip != "") {
            $sign .= "spbill_create_ip=" . $spbill_create_ip . "&";
        }
        
        $sign .= "key=" . $this->_config['key'];
        return strtolower(md5($sign));
    }

    public function is_tenpay_sign($params) {
        $cmdno = $params["cmdno"];
        $pay_result = $params["pay_result"];
        $date = $params["date"];
        $transaction_id = $params["transaction_id"];
        $sp_billno = $params["sp_billno"];
        $total_fee = $params["total_fee"];
        $fee_type = $params["fee_type"];
        $attach = $params["attach"];
        $key = $this->_config['key'];
        
        $sign = "cmdno=" . $cmdno . "&" . "pay_result=" . $pay_result . "&" . "date=" . $date . "&" . "transaction_id=" . $transaction_id . "&" . "sp_billno=" . $sp_billno . "&" . "total_fee=" . $total_fee . "&" . "fee_type=" . $fee_type . "&" . "attach=" . $attach . "&" . "key=" . $key;
        
        $sign = strtolower(md5($sign));
        $tenpay_sign = strtolower($params['sign']);
        return $sign == $tenpay_sign;
    }

    public function get_request_url() {
        $this->set_params();
        $sign = $this->create_sign($this->params);
        $this->params['sign'] = $sign;
        return 'http://service.tenpay.com/cgi-bin/v3.0/payservice.cgi?' . http_build_query($this->params);
    }
}
	