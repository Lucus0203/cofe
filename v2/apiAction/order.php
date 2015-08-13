<?php

require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Pingxx' . DS . 'init.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Huanxin.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Sms.php';
$act = filter($_REQUEST['act']);
switch ($act) {
        case 'pay':
                pay(); //同步ping++支付,创建订单
                break;
        case 'webhooks':
                webhooks(); //ping++通知更新订单状态
                break;
        default:
                break;
}

//同步ping++支付,创建订单
function pay() {
        global $db;
        $input_data = json_decode(file_get_contents('php://input'), true);
        //$input_data['channel']='alipay';//wx
        //$input_data['amount']=101;
        $channel = !empty($input_data['channel']) ? strtolower(filter($input_data['channel'])) : '';
        $encouterid = !empty($input_data['encouterid']) ? filter($input_data['encouterid']) : '';
        $loginid = !empty($input_data['loginid']) ? filter($input_data['loginid']) : '';
        $receiveid = !empty($input_data['receiveid']) ? filter($input_data['receiveid']) : '';
        $encouter = $db->getRow('encouter', array('id' => $encouterid));
        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
        if (empty($channel)) {
                json_result(null, '110', '请选择支付方式');return;
                
        }
        if (empty($encouterid)) {
                json_result(null, '111', '支付对象丢失');return;
                
        }
        //$encouter['status'] 1待付款 2待领取 3待到店领取 4已领走 5等候待付款 6等候待到店领取 7等候已领走
        $orderNo = $loginid . time();
        $orderNo = $channel == 'alipay' ? '01' . $orderNo : '02' . $orderNo;
        switch ($encouter['type']) {
                case 1://爱心
                case 2://缘分
                case 3://约会
                        if($encouter['status']!=1){
                                json_result(null, '203', '您的订单无需再支付');return;
                        }
                        break;
                case 4://传递
                        if(empty($encouter['prev_encouter_id'])){
                                if($encouter['status']!=1){
                                        json_result(null, '203', '您的订单无需再支付');return;
                                }  
                        }else{
                                $prev_encouter=$db->getRow('encouter',array('id'=>$encouter['prev_encouter_id']));
                                if($prev_encouter['lock']!=1){
                                        $remenus=floor((time()-strtotime($prev_encouter['update'])) / 60);
                                        if($remenus<8){//8分钟锁定
                                                json_result(null, '204', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                        }
                                }
                                if($prev_encouter['status']!=2){
                                        json_result(null, '205', '很抱歉您晚了一步,这杯咖啡已由他人接力');return;
                                }
                                $db->update('encouter',array('lock'=>2,'updated'=>date("Y-m-d H:i:s")),array('id'=>$encouter['prev_encouter_id']));//锁定支付
                        }
                        break;
                case 5://等候
                        if($encouter['lock']!=1){
                                $remenus=floor((time()-strtotime($encouter['update'])) / 60);
                                if($remenus<8){//8分钟锁定
                                        json_result(null, '206', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                }
                        }
                        if($encouter['status']!=5){
                                $paid_order=$db->getRow('order',array('encouter_id'=>$encouterid,'paid'=>1));
                                if($paid_order['user_id']!=$loginid){
                                        json_result(null, '207', '很抱歉您晚了一步,这杯咖啡已由他人买单');return;
                                }else{
                                        json_result(null, '203', '您的订单无需再次支付');return;
                                }
                        }
                        $db->update('encouter',array('lock'=>2,'updated'=>date("Y-m-d H:i:s")),array('id'=>$encouterid));//锁定支付
                        break;
                default:
                        break;
        }
        $menus = array();
        $menubody = $shop['title'];
        $totalamount = 0;
        $product = $encouter['product1'];
        $price = $encouter['price1'];
        $product.=empty($encouter['product2']) ? '' : ',' . $encouter['product2'];
        $price+=empty($encouter['price2']) ? 0 : $encouter['price2'] * 1;
        $menubody .= '(' . $product . ')';
        $totalamount = $price * 100;
        //$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
        $extra = array();
        //sk_live_OJQEx4iDNUjsC0BuR7UdMbRd sk_test_SSm1OOvD8anLzLaHSOGmnzzP
        \Pingpp\Pingpp::setApiKey('sk_test_SSm1OOvD8anLzLaHSOGmnzzP');
        try {
                $ch = \Pingpp\Charge::create(
                        array(
                            "subject" => "[咖啡约我]订单支付",
                            "body" => $menubody,
                            "amount" => $totalamount,
                            "order_no" => $orderNo,
                            "currency" => "cny",
                            "extra" => $extra,
                            "channel" => $channel,
                            "client_ip" => $_SERVER["REMOTE_ADDR"],
                            "app" => array("id" => "app_rLSuDGnvvj9S8Ouf")
                        )
                );
                $order = array('encouter_id' => $encouter['id'], 'user_id' => $loginid, 'shop_id' => $encouter['shop_id'], 'time_created' => $ch['created'], 'paid' => 2, 'channel' => $ch['channel'], 'order_no' => $ch['order_no'], 'amount' => floor($ch['amount'] / 100), 'subject' => $ch['subject'], 'body' => $ch['body'], 'description' => $ch['description'], 'created' => date('Y-m-d H:i:s'));
                if(!empty($receiveid)){
                        $order['encouter_receive_id']=$receiveid;
                }
                $orderid = $db->create('order', $order);
                for ($i = 1; $i <= 2; $i++) {
                        if (!empty($encouter['product' . $i])) {
                                $od = array('order_id' => $orderid, 'user_id' => $loginid, 'shop_id' => $encouter['shop_id'], 'name' => $encouter['product' . $i], 'img' => $encouter['product_img' . $i], 'price' => $encouter['price' . $i], 'created' => date("Y-m-d H:i:s"));
                                $db->create('order_detail', $od);
                        }
                }
                echo $ch;
        } catch (\Pingpp\Error\Base $e) {
                header('Status: ' . $e->getHttpStatus());
                echo($e->getHttpBody());
        }
}

//未支付的订单再次支付
function secondPay(){
        global $db;
        $input_data = json_decode(file_get_contents('php://input'), true);
        $orderid = !empty($input_data['orderid']) ? filter($input_data['orderid']) : '';
        $loginid = !empty($input_data['loginid']) ? filter($input_data['loginid']) : '';
        $old_order = $db->getRow('order', array('id' => $orderid));
        $encouter = $db->getRow('encouter', array('id' => $old_order['encouter_id']));
        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
        if($old_order['user_id']!=$loginid){
                json_result(null, '201', '您无此订单,请核对');return;
        }
        if ($old_order['paid'] == 1) {
                json_result(null, '202', '您的订单已支付');return;
        }
        if ($old_order['status'] == 2) {
                json_result(null, '203', '您的订单已失效');return;
        }
        if ($old_order['status'] == 3) {
                json_result(null, '204', '您的订单已过期');return;
        }
        $orderNo = $old_order['order_no'];
        switch ($encouter['type']) {
                case 1://爱心
                case 2://缘分
                case 3://约会
                        if($encouter['status']!=1){
                                json_result(null, '204', '您的订单无需再支付');return;
                        }
                        break;
                case 4://传递
                        if(empty($encouter['prev_encouter_id'])){
                                if($encouter['status']!=1){
                                        json_result(null, '205', '您的订单无需再支付');return;
                                }  
                        }else{
                                $prev_encouter=$db->getRow('encouter',array('id'=>$encouter['prev_encouter_id']));
                                if($prev_encouter['lock']!=1){
                                        $remenus=floor((time()-strtotime($prev_encouter['update'])) / 60);
                                        if($remenus<8){//8分钟锁定
                                                json_result(null, '206', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                        }
                                }
                                if($prev_encouter['status']!=2){
                                        json_result(null, '207', '很抱歉,这杯咖啡已由他人接力');return;
                                }
                                $db->update('encouter',array('lock'=>2,'updated'=>date("Y-m-d H:i:s")),array('id'=>$encouter['prev_encouter_id']));//锁定支付
                        }
                        break;
                case 5://等候
                        if($encouter['lock']!=1){
                                $remenus=floor((time()-strtotime($encouter['update'])) / 60);
                                if($remenus<8){//8分钟锁定
                                        json_result(null, '208', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                }
                        }
                        if($encouter['status']!=5){
                                $paid_order=$db->getRow('order',array('encouter_id'=>$old_order['encouter_id'],'paid'=>1));
                                if($paid_order['user_id']!=$loginid){
                                        json_result(null, '209', '很抱歉,您晚了一步');return;
                                }else{
                                        json_result(null, '210', '您的订单无需再次支付');return;
                                }
                        }
                        $db->update('encouter',array('lock'=>2,'updated'=>date("Y-m-d H:i:s")),array('id'=>$encouterid));//锁定支付
                        break;
                default:
                        break;
        }
        $menus = array();
        $menubody = $shop['title'];
        $totalamount = 0;
        $product = $encouter['product1'];
        $price = $encouter['price1'];
        $product.=empty($encouter['product2']) ? '' : ',' . $encouter['product2'];
        $price+=empty($encouter['price2']) ? 0 : $encouter['price2'] * 1;
        $menubody .= '(' . $product . ')';
        $totalamount = $price * 100;
        //$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
        $extra = array();
        //sk_live_OJQEx4iDNUjsC0BuR7UdMbRd sk_test_SSm1OOvD8anLzLaHSOGmnzzP
        \Pingpp\Pingpp::setApiKey('sk_test_SSm1OOvD8anLzLaHSOGmnzzP');
        try {
                $ch = \Pingpp\Charge::create(
                        array(
                            "subject" => "[咖啡约我]订单支付",
                            "body" => $menubody,
                            "amount" => $totalamount,
                            "order_no" => $orderNo,
                            "currency" => "cny",
                            "extra" => $extra,
                            "channel" => $channel,
                            "client_ip" => $_SERVER["REMOTE_ADDR"],
                            "app" => array("id" => "app_rLSuDGnvvj9S8Ouf")
                        )
                );
                $order = array('encouter_id' => $encouter['id'], 'user_id' => $loginid, 'shop_id' => $encouter['shop_id'], 'time_created' => $ch['created'], 'paid' => 2, 'channel' => $ch['channel'], 'order_no' => $ch['order_no'], 'amount' => floor($ch['amount'] / 100), 'subject' => $ch['subject'], 'body' => $ch['body'], 'description' => $ch['description'], 'created' => date('Y-m-d H:i:s'));
                $db->update('order', $order, array('id' => $old_order['id']));
                echo $ch;
        } catch (\Pingpp\Error\Base $e) {
                header('Status: ' . $e->getHttpStatus());
                echo($e->getHttpBody());
        }
}

//ping++通知更新订单状态
function webhooks() {
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
                        $chargeid = $event->data->object->id;
                        $paid = $event->data->object->paid; //支付状态1支付2未付
                        $order_no = $event->data->object->order_no; //订单号
                        $amount = $event->data->object->amount; //订单金额
                        $time_paid = $event->data->object->time_paid; //支付时间戳
                        if ($paid) {
                                $orderCondition = array('order_no' => $order_no);
                                $order = $db->getRow('order', $orderCondition);
                                $db->update('order', array('charge_id' => $chargeid, 'paid' => 1,'pay_amount'=>$amount, 'time_paid' => $time_paid), $orderCondition);
                                updateOrderEncouter($order);
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

//领取者付款时数据同步
function updateOrderEncouter($order){
        global $db;
        $encouter=$db->getRow('encouter',array('id'=>$order['encouter_id']));
        //$encouter['status'] 1待付款 2待领取 3待到店领取 4已领走 5等候待付款 6等候待到店领取 7等候已领走
        $db->excuteSql("begin;"); //使用事务查询状态并改变
        switch ($encouter['type']) {
                case 1://爱心
                case 2://缘分
                case 3://约会
                        $db->update('encouter', array('status' => 2), array('id' => $order['encouter_id']));
                        break;
                case 4://传递
                        $receiveid=$encouter['prev_encouter_receive_id'];
                        if(!empty($encouter['prev_encouter_id'])){
                               //可到店领取
                               $db->update('encouter', array('lock' => 1,'status' => 3), array('id' => $encouter['prev_encouter_id']));
                               //其他用户的订单失效
                               $db->update('order', array('status'=>2), array('id <> ' .$order['id'],'encouter_id'=>$encouter['prev_encouter_id']));
                               //可领取
                               $db->update('encouter_receive',array('status'=>2),array('id'=>$receiveid));
                        }
                        $db->update('encouter', array('status' => 2), array('id' => $order['encouter_id']));
                        break;
                case 5://等候
                        $receiveid=$order['encouter_receive_id'];
                        //可到店领取
                        $db->update('encouter', array('status' => 6), array('id' => $order['encouter_id']));
                        //其他用户的订单失效
                        $db->update('order', array('status'=>2), array('id <> ' .$order['id'],'encouter_id'=>$order['encouter_id']));
                        //可领取
                        $db->update('encouter_receive', array('status'=>2), array('id'=>$order['encouter_receive_id']));
                        break;

                default:
                        break;
        }
        $db->excuteSql("commit;");
        if(!empty($receiveid)){
                sendNotifyMsgByReceive($receiveid);//领取成功发送消息
        }
}

//通知消息
require_once APP_DIR . DS . 'encouter_notifymsg.php';;