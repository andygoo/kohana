<?php defined('SYSPATH') or die('No direct script access.');

class Payment_Alipaywap extends Payment 
{
	protected function set_params() 
	{
	}
	
	protected function create_sign($params, $ksort = true) 
	{
		if ($ksort)
		{
			ksort($params);
		}
		$arg  = '';
		foreach ($params as $key => $val)
		{
			if($key == "sign" || $key == "sign_type" || $val == "") 
			{
				continue;
			}
			else
			{
				$arg .= $key.'='.$val.'&';
			}
		}
		$arg = substr($arg, 0, -1);
		return md5($arg.$this->_config['key']);
	}	

	public function is_alipay_sign($params)
	{
		if(empty($params)) 
		{
			return false;
		}
		
		if (!isset($params["sign"]))
		{
			$mysign = $this->create_sign($params, false);
			$sign = $_POST["sign"];
		}
		else
		{
			$mysign = $this->create_sign($params);
			$sign = $params["sign"];
		}
		
		if ($mysign == $sign) 
		{
			return true;
		} 
		else 
		{
			//Kohana::$log->add(Log::ERROR, 'alipay 网页即时到帐支付  验证错误：'.$mysign.'--'.$params["sign"]);
			return false;
		}
	}

	public function get_request_url()
	{
		$req_data = '<direct_trade_create_req>';
		$req_data .= '<subject>交易编号 - ' . $this->_config['order_id'] . '</subject>';
		$req_data .= '<out_trade_no>' . $this->_config['order_id'] . '</out_trade_no>';
		$req_data .= '<total_fee>' . $this->_config['order_amount'] . '</total_fee>';
		$req_data .= '<seller_account_name>' . $this->_config['seller_email'] . '</seller_account_name>';
		$req_data .= '<notify_url>' . $this->_config['notify_url'] . '</notify_url>';
		$req_data .= '<out_user></out_user>';
		$req_data .= '<merchant_url>' . $this->_config['merchant_url'] . '</merchant_url>';
		$req_data .= '<call_back_url>' . $this->_config['return_url'] . '</call_back_url>';
		$req_data .= '</direct_trade_create_req>';
		
		$pms1 = array (
				"req_data" => $req_data,
				"service" => 'alipay.wap.trade.create.direct',
				"sec_id" => 'MD5',
				"partner" => $this->_config['partner'],
				"req_id" => date ('Ymdhms'),
				"format" => 'xml',
				"v" => '2.0'
		);
		
		$sign1 = $this->create_sign($pms1);
		$pms1['sign'] = $sign1;
		$result = $this->curl_post($this->_config['gateway_url'], $pms1);
		$token = $this->getToken($result);
		
		$pms2 = array (
				"req_data" => "<auth_and_execute_req><request_token>" . $token . "</request_token></auth_and_execute_req>",
				"service" => 'alipay.wap.auth.authAndExecute',
				"sec_id" => 'MD5',
				"partner" => $this->_config['partner'],
				"call_back_url" => $this->_config['return_url'],
				"format" => 'xml',
				"v" => '2.0' 
		);
		$sign2 = $this->create_sign($pms2);
		$pms2['sign'] = $sign2;
		$data = http_build_query($pms2);
		
		return $this->_config['gateway_url'] . '?' . http_build_query($pms2);
	}

	protected function getToken($result) 
	{
		$result = urldecode ($result);
		$Arr = explode ('&', $result);
	
		$myArray = array();
		for($i = 0; $i < count($Arr); $i++) 
		{
			$temp = explode('=', $Arr[$i], 2);
			$myArray[$temp[0]] = $temp[1];
		}

		if ($this->is_alipay_sign($myArray)) 
		{
			return $this->getDataForXML($myArray['res_data'], '/direct_trade_create_res/request_token' );
		} 
		return false;
	}
	
	protected function curl_post($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); // 配置网关地址
		curl_setopt($ch, CURLOPT_HEADER, 0); // 过滤HTTP头
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1); // 设置post提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // post传输数据
		$data = curl_exec($ch);
		curl_close( $ch );
		return $data;
	}

	public function getDataForXML($res_data, $node)
	{
		$xml = simplexml_load_string($res_data);
		$result = $xml->xpath($node);
	
		while(list( , $node) = each($result))
		{
			return $node;
		}
	}
}
	