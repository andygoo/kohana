<?php defined('SYSPATH') or die('No direct script access.');

class Payment_Alipaysecure extends Payment 
{
	protected function set_params() 
	{
		$this->params = array(
				'partner'			=> $this->_config['partner'],
				'seller'			=> $this->_config['seller_email'],
		        "return_url"		=> $this->_config['return_url'],
		        "notify_url"		=> $this->_config['notify_url'],
				'subject'			=> '交易编号 - '.$this->_config['order_id'],
				'out_trade_no'		=> $this->_config['order_id'],
				'_input_charset'	=> strtolower($this->_config['input_charset']),
				"total_fee"			=> $this->_config['order_amount'],
		);
	}
	
	protected function create_sign($data) 
	{
	    //读取私钥文件
	    //$priKey = file_get_contents('key/rsa_private_key.pem');
	    $priKey = $this->_config['rsa_private_key'];
	
	    //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
	    $res = openssl_get_privatekey($priKey);
	
	    //调用openssl内置签名方法，生成签名$sign
	    openssl_sign($data, $sign, $res);
	
	    //释放资源
	    openssl_free_key($res);
	    
	    //base64编码
	    $sign = base64_encode($sign);
	    return $sign;
	}	

	public function is_alipay_sign($data, $sign)
	{
	    //读取支付宝公钥文件
		//$pubKey = file_get_contents('key/alipay_public_key.pem');
	    $pubKey = $this->_config['alipay_public_key'];
	
	    //转换为openssl格式密钥
	    $res = openssl_get_publickey($pubKey);
	
	    //调用openssl内置方法验签，返回bool值
	    $result = (bool)openssl_verify($data, base64_decode($sign), $res);
		
	    //释放资源
	    openssl_free_key($res);
	
	    //返回资源是否成功
	    return $result;
	}

	public function get_request_url()
	{
		$this->set_params();
        
        $data['partner'] = $this->params['partner'];
        $data['seller'] = $this->params['seller'];
        $data['out_trade_no'] = $this->params['out_trade_no'];
        $data['subject'] = $this->params['subject'];
        $data['body'] = '';
        $data['total_fee'] = $this->params['total_fee'];
        $data['notify_url'] = $this->params['notify_url'];
        
        $signData = http_build_query($data);
        $mySign = $this->create_sign($signData);
	    $return = '<result><is_success>T</is_success><content>' . $signData . '</content><sign>' . $mySign . '</sign></result>';
	    
	    echo urlencode($return);
	}
}
	