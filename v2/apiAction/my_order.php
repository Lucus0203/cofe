<?php

$act = filter($_REQUEST['act']);
switch ($act) {
    case 'orderlist'://消费的咖啡
        orderlist();
        break;
    default:
        break;
}

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
        $sql="select shop.id as shop_id,shop.title,shop.subtitle,shop.img,shop.lng,shop.lat from ".DB_PREFIX."order order left join ".DB_PREFIX."shop_users shopuser on shop.id=shopuser.shop_id where shopuser.user_id=".$loginid." and status=2 ";
	$sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))":'';

	$sql .= " limit $start,$page_size";
	$list=$db->getAllBySql($sql);
        echo json_result(array('orders'=>$list));
        
}