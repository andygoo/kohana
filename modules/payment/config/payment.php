<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// 财付通中介担保支付
	'tenpaymed' => array(
		'mem_id'		=> 'xx',
		'key'			=> 'xx',
		'gateway_url'	=> 'http://service.tenpay.com/cgi-bin/v3.0/payservice.cgi',
		'notify_url'	=> 'http://test/payment/tenpaymed/notify_url.php',
		'return_url'	=> 'http://test/payment/tenpaymed/return_url.php',
	),
	// 财付通即时到账支付
	'tenpay' => array(
		'mem_id'		=> 'xx',
		'key'			=> 'xx',
		'gateway_url'	=> 'http://service.tenpay.com/cgi-bin/v3.0/payservice.cgi',
		'notify_url'	=> 'http://test/payment/tenpay/notify_url.php',
		'return_url'	=> 'http://test/payment/tenpay/return_url.php',
	),
	// 支付宝即时到帐支付
	'alipay' => array(
		'partner'      => 'xx',
		'key'          => 'xx',
		'gateway_url'  => 'https://mapi.alipay.com/gateway.do',
		'notify_url'   => 'http://test/payment/alipay/notify_url.php',
		'return_url'   => 'http://test/payment/alipay/return_url.php',
		'seller_email' => 'brtc2011@yahoo.cn',
		'sign_type'    => 'MD5',
		'input_charset'=> 'utf-8',
		'transport'    => 'http',
	),
	// 支付宝手机网页即时到账支付
	'alipaywap' => array(
		'partner'      => 'xx',
		'key'          => 'xx',
		'gateway_url'  => 'http://wappaygw.alipay.com/service/rest.htm',
		'notify_url'   => 'http://test/payment/alipaywap/notify_url.php',
		'return_url'   => 'http://test/payment/alipaywap/return_url.php',
		'merchant_url'   => '',
		'seller_email' => 'brtc2011@yahoo.cn',
	),
	// 支付宝手机客户端安全支付
	/*'alipaysecure' => array(
		'partner'      => 'xx',
		'key'          => 'xx',
		'gateway_url'  => 'https://mapi.alipay.com/gateway.do',
		'notify_url'   => 'http://test/payment/alipaywap/notify_url.php',
		'return_url'   => 'http://test/payment/alipaywap/return_url.php',
		'seller_email' => 'brtc2011@yahoo.cn',
		'sign_type' => 'RSA',
		'input_charset' => 'utf-8',
		'alipay_public_key' => $alipay_public_key,
		'rsa_private_key' => $rsa_private_key,
	),*/
);

