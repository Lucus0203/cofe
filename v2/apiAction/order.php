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
	    json_result(null,'110','支付方式未选择');return;
	}
	if (empty($input_data['encouterid'])) {
	    json_result(null,'111','支付对象丢失');return;
	}
	$channel = strtolower($input_data['channel']);
        $isoldOrder=false;//是否已存在订单
        if ($db->getCount('order',array('encouter_id'=>$input_data['encouterid']))>0){//如果已存在订单则判断
                $old_order=$db->getRow('order',array('encouter_id'=>$input_data['encouterid']));
                if($old_order['paid']==1){
                        json_result(null,'201','订单已支付');return;
                }
                $orderNo=$old_order['order_no'];
                $isoldOrder=true;
        }else{
                $orderNo = $input_data['loginid'].time();
                $orderNo = $channel=='alipay'?'01'.$orderNo:'02'.$orderNo;
        }
        $encouter = $db->getRow('encouter',array('id'=>$input_data['encouterid']));
        if($input_data['loginid']!=$encouter['user_id']){
	    json_result(null,'112','你的订单不存在,请核对');return;
        }
	$shop = $db->getRow('shop',array('id'=>$encouter['shop_id']));
	$menus=array();
	$menubody=$shop['title'];
	$totalamount=0;
        $product=$encouter['product1'];
        $price=$encouter['price1'];
        $product.=empty($encouter['product2'])?'':','.$encouter['product2'];
        $price+=empty($encouter['price2'])?0:$encouter['price2']*1;
        $menubody .= '('.$product.')';
        $totalamount=$price*100;
	//$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
	$extra = array();
        //sk_live_OJQEx4iDNUjsC0BuR7UdMbRd sk_test_SSm1OOvD8anLzLaHSOGmnzzP
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
                $order=array('encouter_id'=>$encouter['id'],'user_id'=>$input_data['loginid'],'shop_id'=>$encouter['shop_id'],'time_created'=>$ch['created'],'paid'=>2,'channel'=>$ch['channel'],'order_no'=>$ch['order_no'],'amount'=>floor($ch['amount']/100),'subject'=>$ch['subject'],'body'=>$ch['body'],'description'=>$ch['description'],'created'=>date('Y-m-d H:i:s'));
                if($isoldOrder){
                        $db->update('order', $order,array('id'=>$old_order['id']));
                }else{
                        $orderid=$db->create('order', $order);
                        for ($i=1;$i<=2;$i++){
                            if(!empty($encouter['product'.$i])){
                            $od=array('order_id'=>$orderid,'user_id'=>$input_data['loginid'],'shop_id'=>$encouter['shop_id'],'name'=>$encouter['product'.$i],'img'=>$encouter['product_img'.$i],'price'=>$encouter['price'.$i],'created'=>date("Y-m-d H:i:s"));
                            $db->create('order_detail', $od);
                            }
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
                $circlefile=APP_DIR. '/upload/order_no.db';
                if($paid){
                        $orderCondition=array('order_no'=>$order_no);
                        $order = $db->getRow('order',$orderCondition);
                        $db->update('order', array('charge_id'=>$chargeid,'paid'=>1,'time_paid'=>$time_paid) , $orderCondition);
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
        
	
}

