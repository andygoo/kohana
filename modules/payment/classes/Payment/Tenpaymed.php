<?php defined('SYSPATH') or die('No direct script access.');

class Payment_Tenpaymed extends Payment {
	
	protected function set_params() 
	{
		$this->params = array(
				"attach"		 => '1',
				"cmdno"			 => '12',								//任务代码
				"chnid"			 => $this->_config['mem_id'],			//平台商帐号
				"encode_type"	 => "2",								//编码类型 1:gbk 2:utf-8
				"mch_desc" 		 => $this->_config['order_desc'],		//交易说明
				"mch_name" 		 => $this->_config['order_name'],		//商品名称
				"mch_price"		 => $this->_config['order_amount'],		//商品总价，单位为分
				"mch_returl" 	 => $this->_config['notify_url'],		//回调通知URL
				"mch_type" 		 => "1",								//交易类型：1、实物交易，2、虚拟交易
				"mch_vno" 		 => $this->_config['order_id'],			//商家的定单号
				"need_buyerinfo" => "2",								//是否需要在财付通填定物流信息，1：需要，2：不需要。
				"seller" 		 => $this->_config['mem_id'],			//卖家财付通帐号
				"show_url"		 => $this->_config['return_url'],		//支付后的商户支付结果展示页面
				"transport_desc" => '',									//物流公司或物流方式说明
				"transport_fee"  => '',									//需买方另支付的物流费用
				"version"		 => '2', 
				"sign" 			 => ""									//摘要
		);
	}
	
	protected function create_sign($params) 
	{
		ksort($params);
		$sign = "";
		foreach($params as $k => $v) {
			if("" != $v && "sign" != $k) {
				$sign .= $k . "=" . $v . "&";
			}
		}
		$sign .= "key=" . $this->_config['key'];
		return strtolower(md5($sign));
	}	
	
	public function is_tenpay_sign($params) 
	{
		ksort($params);
		$sign = '';
		foreach($params as $k => $v) {
			if($k != 'sign') {
				$sign .= $k . "=" . urldecode($v) . "&";
			}
		}
		$sign .= "key=" . $this->_config['key'];
		$sign = strtolower(md5($sign));
		$tenpay_sign = strtolower($params['sign']);
		return $sign == $tenpay_sign;
	}
	
	public function get_request_url() 
	{
		$this->set_params();
		$sign = $this->create_sign($this->params);
		$this->params['sign'] = $sign;
		return $this->_config['gateway_url'] . '?' . http_build_query($this->params);
	}
}