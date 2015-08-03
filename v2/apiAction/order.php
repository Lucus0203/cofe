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
	global $db;
	$input_data = json_decode(file_get_contents('php://input'), true);
	//$input_data['channel']='alipay';//wx
	//$input_data['amount']=101;
	if (empty($input_data['channel']) || empty($input_data['amount'])) {
	    json_result(null,'110','支付方式未选择或金额不正确');
		exit();
	}
	$channel = strtolower($input_data['channel']);
	$amount = $input_data['amount'];
	$orderNo = $input_data['loginid'].time();
	$orderNo = $channel=='alipay'?'01'.$orderNo:'02'.$orderNo;	
	$shop = $db->getRow('shop',array('id'=>$input_data['shopid']));
	$menus=array();
	$menupriceids=$input_data['menu_price_ids'];
	$menupriceids=explode(',' , $menupriceids);
	$menubody=$shop['title'];
	$totalamount=0;
	foreach ($menupriceids as $pid){
		$menuprice = $db->getRow('shop_menu_price',array('id'=>$pid));
		$menu = $db->getRow('shop_menu',array('id'=>$menuprice['menu_id']));
		$menubody .= ','.$menu['title'];
		$totalamount+=$menuprice['price'];
		$menus[]=$menu;
	}
	if($totalamount*100!=$amount){
		echo json_result(null,'110','店家价格变更,请重新订单');
		exit();
	}
	//$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
	$extra = array();
	\Pingpp\Pingpp::setApiKey('sk_test_SSm1OOvD8anLzLaHSOGmnzzP');
	try {
	    $ch = \Pingpp\Charge::create(
	        array(
	            "subject"   => "[咖啡约我]订单支付",
	            "body"      => $shop['title'].$menubody,
	            "amount"    => $amount,
	            "order_no"  => $orderNo,
	            "currency"  => "cny",
	            "extra"     => $extra,
	            "channel"   => $channel,
	            "client_ip" => $_SERVER["REMOTE_ADDR"],
	            "app"       => array("id" => "app_rLSuDGnvvj9S8Ouf")
	        )
	    );
	    $order=array('user_id'=>$input_data['loginid'],'charge_id'=>$ch['id'],'time_created'=>$ch['created'],'paid'=>2,'channel'=>$ch['channel'],'order_no'=>$ch['order_no'],'amount'=>$ch['amount'],'subject'=>$ch['subject'],'body'=>$ch['body'],'description'=>$ch['description'],'created'=>date('Y-m-d H:i:s'));
	    $orderid=$db->create('order', $order);
	    foreach ($menus as $m){
	    	$od=array('order_id'=>$orderid,'user_id'=>$input_data['loginid'],'shop_id'=>$shop['id'],'name'=>$m['name'],'img'=>$m['img'],'price'=>$m['price'],'created'=>date("Y-m-d H:i:s"));
	    	$db->create('order_detail', $od);
	    }
	    echo $ch;
	} catch (\Pingpp\Error\Base $e) {
	    header('Status: ' . $e->getHttpStatus());
	    echo($e->getHttpBody());
	}
	
}

//ping++通知更新订单状态
function webhooks(){
	global $db;
	$input_data = json_decode(file_get_contents("php://input"), true);
	if($input_data['type'] == 'charge.succeeded'&& $input_data['data']['object']['paid'] == true)
	{
		//TODO update database
                $order = array('paid'=>1,'time_paid'=>$input_data['data']['time_paid']);
                $db->update('order', $order , array('order_no'=>$input_data['data']['order_no']));
		http_response_code(200);// PHP 5.4 or greater
	
	}
	
	else if($input_data['type'] == 'refund.succeeded'&& $input_data['data']['object']['succeed'] == true)
	{
		//TODO update database
		http_response_code(200);// PHP 5.4 or greater
	}
	
}

