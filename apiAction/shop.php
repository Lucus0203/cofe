<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'recommendShops':
		recommendShops();
		break;
	case 'nearbyShops':
		nearbyShops();//附近店铺
		break;
	case 'getShopByConditions':
		getShopByConditions();//店铺筛选
		break;
	case 'favoriteShops':
		favoriteShops();//用户收藏的店铺
		break;
	case 'removeFavoriteShopById'://取消收藏
		removeFavoriteShopById();
		break;
	case 'shopInfo':
		shopInfo();//店铺详情
		break;
	case 'favorites':
		favorites();//收藏店铺
		break;
	case 'leaveMsg':
		leaveMsg();//店铺留言
		break;
	default:
		break;
}

//推荐的店铺
function recommendShops(){
	global $db;
	$sql="select id,img from ".DB_PREFIX."shop where status=2 and recommend=1 and img <>'' and img is not null";
	$sql.=" order by id desc";
	$shops=$db->getAllBySql($sql);
	echo json_result($shops);
}

//附近咖啡
function nearbyShops(){
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	$sql="select * from ".DB_PREFIX."shop where status=2 ";
	$sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))":'';
	
	$sql .= " limit $start,$page_size";
	$shops=$db->getAllBySql($sql);
	foreach ($shops as $k=>$v){
		$shops[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	//echo json_result(array('shops'=>$shops));
	echo json_result($shops);
}

//筛选出的店铺
function getShopByConditions(){
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$keyword = !empty ( $_GET ['keyword'] ) ? $_GET ['keyword'] : '';//关键字
	$provinceid = !empty ( $_GET ['provinceid'] ) ? $_GET ['provinceid'] : '';//省
	$cityid = !empty ( $_GET ['cityid'] ) ? $_GET ['cityid'] : '';//市
	$townid = !empty ( $_GET ['townid'] ) ? $_GET ['townid'] : '';//区
	$circleid = !empty ( $_GET ['circleid'] ) ? $_GET ['circleid'] : '';//商圈
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	$conditions="";
	if(!empty($keyword)){
		//$conditions.=" and (INSTR(title,'".addslashes($keyword)."') or INSTR(subtitle,'".addslashes($keyword)."') or INSTR(address,'".addslashes($keyword)."') ) ";
		$conditions.=" and ( INSTR(title,'".addslashes($keyword)."') or INSTR(subtitle,'".addslashes($keyword)."') ) ";
		$conditions.=" and round(6378.138*2*asin(sqrt(pow(sin( ($lat*pi()/180-lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(lat*pi()/180)* pow(sin( ($lng*pi()/180-lng*pi()/180)/2),2)))*1000) <= ".RANGE_KILO;
	}
	if(!empty($provinceid)){
		$conditions.=" and province_id=$provinceid ";
	}
	if(!empty($cityid)){
		$conditions.=" and city_id=$cityid ";
	}
	if(!empty($townid)){
		$conditions.=" and town_id=$townid ";
	}
	
	$circlerOrder="";
	if(!empty($circleid)){
		$locat=$db->getRow('business_circle',array('id'=>$circleid),array("lng","lat"));
		$circle_lng=$locat['lng'];
		$circle_lat=$locat['lat'];
		$circlerOrder=" sqrt(power(lng-{$circle_lng},2)+power(lat-{$circle_lat},2)) , ";
	}
	
	$sql="select * from ".DB_PREFIX."shop where status=2 $conditions ";
	$count=$db->getCountBySql($sql);
	
	$sql.=(!empty($lng)&&!empty($lat))?" order by $circlerOrder sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))":'';
	$sql .= " limit $start,$page_size";
	$shops=$db->getAllBySql($sql);
	foreach ($shops as $k=>$v){
		$shops[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	//echo json_result(array('count'=>$count,'shops'=>$shops));

	echo json_result($shops);
}

//收藏的店铺
function favoriteShops(){
	global $db;
	$userid=filter($_REQUEST['userid']);
	if(empty($userid)){
		echo json_result(null,'21','用户未登录');
		return;
	}
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	$sql="select shop.id,shop.title,shop.subtitle,shop.address,shop.img,shopuser.shop_id,shop.lng,shop.lat from ".DB_PREFIX."shop shop left join ".DB_PREFIX."shop_users shopuser on shop.id=shopuser.shop_id where shopuser.user_id=".$userid." and status=2 ";
	$sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))":'';
	
	$sql .= " limit $start,$page_size";
	$shops=$db->getAllBySql($sql);
	foreach ($shops as $k=>$v){
		$shops[$k]['num']=$db->getCount('shop_users',array('shop_id'=>$v['shop_id']));
		$shops[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	//echo json_result(array('shops'=>$shops));
	echo json_result($shops);
}

//取消收藏的店铺
function removeFavoriteShopById(){
	global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	if(empty($userid)){
		echo json_result(null,'21','用户未登录');
		return;
	}
	if(empty($shopid)){
		echo json_result(null,'22','没有找到这个店铺');
		return;
	}
	$up=array('user_id'=>$userid,'shop_id'=>$shopid);
	$db->delete('shop_users', $up);
	echo json_result(array('success'=>'TRUE'));
	
}

//咖啡店铺详情
function shopInfo(){
	global $db;
	$shopid=filter($_REQUEST['shopid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(!empty($shopid)){
		$shop=$db->getRow('shop',array('id'=>$shopid));
		$shop['distance']=(!empty($shop['lat'])&&!empty($shop['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$shop['lat'],$shop['lng']):lang_UNlOCATE;
		$shop['menus']=$db->getAll('shop_menu',array('shop_id'=>$shopid),null," limit 4 ");
		$bbs_sql="select up.path,u.nick_name,u.user_name,bbs.* from ".DB_PREFIX."shop_bbs bbs left join ".DB_PREFIX."user u on u.id=bbs.user_id left join ".DB_PREFIX."user_photo up on up.id=u.head_photo_id where bbs.allow=1 and bbs.shop_id=$shopid";
		$shop['bbsCount']=$db->getCountBySql($bbs_sql);
		$bbs_sql.=" order by bbs.id desc limit $start,$page_size";
		$shop['bbs']=$db->getAllBySql($bbs_sql);
		//特色
		$shop['features']=explode(',', $shop['feature']);
		//店铺图片
		$imgs=array($shop['img']);
		$shopimgs=$db->getAll('shop_img',array('shop_id'=>$shopid));
		foreach ($shopimgs as $im){
			$imgs[]=$im['img'];
		}
		$shop['imgs']=$imgs;
		echo json_result($shop);
	}else{
		echo json_result(null,'22','店铺不存在');
	}
	
}

//收藏店铺
function favorites(){
	global $db;
	$shopid=filter($_REQUEST['shopid']);
	$userid=filter($_REQUEST['userid']);
	if(!empty($shopid)&&!empty($userid)){
		if($db->getCount('shop_users',array('user_id'=>$userid,'shop_id'=>$shopid))==0){
			$up=array('user_id'=>$userid,'shop_id'=>$shopid,'created'=>date("Y-m-d H:i:s"));
			$db->create('shop_users', $up);
		}
		echo json_result(array('shopid'=>$shopid));
	}else{
		echo json_result(null,'23','用户未登录或者该店铺已删除');
	}
}

//店铺留言
function leaveMsg(){
	global $db;
	$shopid=filter($_REQUEST['shopid']);
	$userid=filter($_REQUEST['userid']);
	$content=filterIlegalWord($_REQUEST['content']);
	if(empty($shopid)){
		echo json_result(null,'24','该店铺已删除');
		return;
	}
	if(empty($userid)){
		echo json_result(null,'25','用户未登录');
		return;
	}
	if(empty($content)){
		echo json_result(null,'26','留言内容为空');
		return;
	}
	$bbs=array('user_id'=>$userid,'shop_id'=>$shopid,'content'=>$content,'created'=>date("Y-m-d H:i:s"));
	$db->create('shop_bbs', $bbs);
	echo json_result(array('shopid'=>$shopid));
}