<?php

$act = filter($_REQUEST['act']);
switch ($act) {
    case 'orderlist'://消费的咖啡
        orderlist();
        break;
    default:
        break;
}

//消费的咖啡
function orderlist(){
        global $db;
	$loginid = filter($_REQUEST['loginid']);
	if(empty($loginid)){
		echo json_result(null,'21','用户未登录');
		return;
	}
	$type = filter($_REQUEST['type']);//1已付2未付3过期
        $type = empty($type)?1:$type;
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
        $sql="select od.id as order_id,od.encouter_id,encouter.type as encouter_type,encouter.product_img1,encouter.product1,encouter.price1,encouter.product_img2,encouter.product2,encouter.price2,shop.title as shop,od.amount,od.created from ".DB_PREFIX."order od "
                . "left join ".DB_PREFIX."shop shop on shop.id=od.shop_id "
                . "left join ".DB_PREFIX."encouter encouter on encouter.id=od.encouter_id "
                . "where od.user_id=".$loginid;
        if($type==1){
                $sql.=" and paid=1 and od.status=1 ";
        }elseif ($type==2) {
                $sql.=" and paid=2 and od.status=1 ";
        }else{
                $sql.=" and TIMESTAMPDIFF(DAY,encouter.created,now())>encouter.days and encouter.status=2 ";
        }
	$sql .=" order by od.id desc ";
	$sql .= " limit $start,$page_size ";
	$list=$db->getAllBySql($sql);
        echo json_result(array('orders'=>$list));
        
}
