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
	if (empty($input_data['channel'])) {
	    json_result(null,'110','支付方式未选择或金额不正确');return;
	}
	if (empty($input_data['encouterid'])) {
	    json_result(null,'111','支付对象丢失');return;
	}
	$channel = strtolower($input_data['channel']);
	$orderNo = $input_data['loginid'].time();
	$orderNo = $channel=='alipay'?'01'.$orderNo:'02'.$orderNo;
        $encouter = $db->getRow('shop',array('id'=>$input_data['encouterid']));
        if($input_data['loginid']!=$encouter['login_id']){
	    json_result(null,'112','你的订单不存在,请核对');return;
        }
	$shop = $db->getRow('shop',array('id'=>$encouter['shopid']));
	$menus=array();
	$menubody=$shop['title'];
	$totalamount=0;
        $product=$encouter['product1'];
        $price=$encouter['price1'];
        $product.=empty($encouter['product2'])?'':','.$encouter['product2'];
        $price+=empty($encouter['price2'])?0:$encouter['price2']*1;
        $menubody .= '('.$product.')';
        $totalamount=$price*100;
        echo $totalamount;
	//$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
	$extra = array();
	\Pingpp\Pingpp::setApiKey('sk_test_SSm1OOvD8anLzLaHSOGmnzzP');
	try {
	    $ch = \Pingpp\Charge::create(
	        array(
	            "subject"   => "[咖啡约我]订单支付",
	            "body"      => $menubody,
	            "amount"    => $totalamount,
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
	    for ($i=1;$i<=2;$i++){
                if(!empty($encouter['product'.$i])){
	    	$od=array('order_id'=>$orderid,'user_id'=>$input_data['loginid'],'shop_id'=>$shop['id'],'name'=>$encouter['product'.$i],'img'=>$encouter['product_img'.$i],'price'=>$encouter['price'.$i],'created'=>date("Y-m-d H:i:s"));
	    	$db->create('order_detail', $od);
                }
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
        $event = json_decode(file_get_contents("php://input"));
        // 对异步通知做处理
        if (!isset($event->type)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        switch ($event->type) {
            case "charge.succeeded":
                // 开发者在此处加入对支付异步通知的处理代码
                $chargeid=$event->data->object->id;
                $paid=$event->data->object->paid;//支付状态1支付2未付
                $order_no=$event->data->object->order_no;//订单号
                $amount=$event->data->object->amount;//订单金额
                $time_paid=$event->data->object->time_paid;//支付时间戳
                if($paid){
                        $orderCondition=array('charge_id'=>$chargeid,'order_no'=>$order_no);
                        $order = $db->getRow('order',$orderCondition);
                        $db->update('order', array('paid'=>1,'time_paid'=>$time_paid) , $orderCondition);
                        $db->update('encouter',array('status'=>2),array('id'=>$order['encouter_id']));
                }
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            case "refund.succeeded":
                // 开发者在此处加入对退款异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            default:
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                break;
        }
//	if($input_data['type'] == 'charge.succeeded'&& $input_data['data']['object']['paid'] == true)
//	{
//		//TODO update database
//                $order = array('paid'=>1,'time_paid'=>$input_data['data']['time_paid']);
//                $db->update('order', $order , array('order_no'=>$input_data['data']['order_no']));
//		http_response_code(200);// PHP 5.4 or greater
//	
//	}
//	
//	else if($input_data['type'] == 'refund.succeeded'&& $input_data['data']['object']['succeed'] == true)
//	{
//		//TODO update database
//		http_response_code(200);// PHP 5.4 or greater
//	}
	
}

