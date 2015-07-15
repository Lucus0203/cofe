<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Pingxx'.DS.'init.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'pay':
		pay();//同步ping++支付,创建订单
		break;
	case 'webhooks':
		webhooks();//ping++通知更新订单状态
		break;
	default:
		break;
}
//同步ping++支付,创建订单
function pay(){
	$input_data = json_decode(file_get_contents('php://input'), true);
	//$input_data['channel']='alipay';
	//$input_data['amount']=101;
	if (empty($input_data['channel']) || empty($input_data['amount'])) {
	    exit();
	}
	$channel = strtolower($input_data['channel']);
	$amount = $input_data['amount'];
	$orderNo = time();
	
	//$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
	$extra = array();
	switch ($channel) {
	    //这里值列举了其中部分渠道的，具体的extra所需参数请参见官网中的 API 文档
	    case 'alipay_wap':
	        $extra = array(
	            'success_url' => 'http://coffee15.com/success',
	            'cancel_url' => 'http://coffee15.com/cancel'
	        );
	        break;
	    case 'upmp_wap':
	        $extra = array(
	            'result_url' => 'http://coffee15.com/result?code='
	        );
	        break;
	    case 'bfb_wap':
	        $extra = array(
	            'result_url' => 'http://coffee15.com/result?code='
	        );
	        break;
	    case 'upacp_wap':
	        $extra = array(
	            'result_url' => 'http://coffee15.com/result?code='
	        );
	        break;
	    case 'wx_pub':
	        $extra = array(
	            'open_id' => 'Openid'
	        );
	        break;
	    case 'wx_pub_qr':
	        $extra = array(
	            'product_id' => 'Productid'
	        );
	        break;
	
	}
	
	\Pingpp\Pingpp::setApiKey('sk_test_SSm1OOvD8anLzLaHSOGmnzzP');
	try {
	    $ch = \Pingpp\Charge::create(
	        array(
	            "subject"   => "一杯咖啡",
	            "body"      => "商品描述",
	            "amount"    => $amount,
	            "order_no"  => $orderNo,
	            "currency"  => "cny",
	            "extra"     => $extra,
	            "channel"   => $channel,
	            "client_ip" => $_SERVER["REMOTE_ADDR"],
	            "app"       => array("id" => "app_rLSuDGnvvj9S8Ouf")
	        )
	    );
	    echo $ch;
	} catch (\Pingpp\Error\Base $e) {
	    header('Status: ' . $e->getHttpStatus());
	    echo($e->getHttpBody());
	}
	
}

//ping++通知更新订单状态
function webhooks(){
	
	$input_data = json_decode(file_get_contents("php://input"), true);
	if($input_data['type'] == 'charge.succeeded'&& $input_data['data']['object']['paid'] == true)
	{
		//TODO update database
		http_response_code(200);// PHP 5.4 or greater
	
	}
	
	else if($input_data['type'] == 'refund.succeeded'&& $input_data['data']['object']['succeed'] == true)
	{
		//TODO update database
		http_response_code(200);// PHP 5.4 or greater
	}
	
}

