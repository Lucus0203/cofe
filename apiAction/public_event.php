<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getEvents':
		getEvents();//首页获取官方活动
		break;
	case 'eventInfo':
		eventInfo();
		break;
	case 'joinEvent':
		joinEvent();
		break;
	default:
		break;
}

//首页获取官方活动
function getEvents(){
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	$pucount="select count(id) as num,public_event_id from ".DB_PREFIX."public_users pu group by pu.public_event_id ";
	$sql="select pe.*,pu.num from ".DB_PREFIX."public_event pe left join ($pucount) pu on pu.public_event_id = pe.id where pe.isdelete = 0 and pe.ispublic=1 ";
	$sql.=(!empty($lng)&&!empty($lat))?" order by pe.num asc,sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),":' order by pe.num asc,';
	
	$sql .= " pe.datetime,pe.id desc limit $start,$page_size";
	$list=$db->getAllBySql($sql);
	foreach ($list as $k=>$v){
		//$list[$k]['datetime']=strtotime($v['datetime']);
		$list[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	echo json_result($list);
}

//官方活动详情
function eventInfo(){
	global $db;
	$id=filter($_REQUEST['eventid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$pub_event=$db->getRow("public_event",array('id'=>$id));//获取活动信息
	$pub_event['distance']=(!empty($pub_event['lat'])&&!empty($pub_event['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$pub_event['lat'],$pub_event['lng']):lang_UNlOCATE;
	$pub_event['photos']=$db->getAll("public_photo",array('public_event_id'=>$id));//活动海报
	//活动用户及头像地址
	$sql="select u.id as user_id,u.nick_name,u.user_name,up.path from ".DB_PREFIX."public_users pu left join ".DB_PREFIX."user u on pu.user_id = u.id left join ".DB_PREFIX."user_photo up on u.head_photo_id = up.id where pu.public_event_id=".$id;
	$pub_event['user_count']=$db->getCountBySql($sql);//参与人数
	$pub_event['users_photo']=$db->getAllBySql($sql);
	echo json_result($pub_event);
}


//报名活动
function joinEvent(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$userid=filter($_REQUEST['userid']);
	if(!empty($eventid)&&!empty($userid)){
		if($db->getCount('public_users',array('user_id'=>$userid,'public_event_id'=>$eventid))==0){
			$up=array('user_id'=>$userid,'public_event_id'=>$eventid,'created'=>date("Y-m-d H:i:s"));
			$db->create('public_users', $up);
		}
		//活动用户及头像地址
		$sql="select u.id as user_id,u.nick_name,u.user_name,up.path from ".DB_PREFIX."public_users pu left join ".DB_PREFIX."user u on pu.user_id = u.id left join ".DB_PREFIX."user_photo up on u.head_photo_id = up.id where pu.public_event_id=".$eventid;
		$pub_event['user_count']=$db->getCountBySql($sql);//参与人数
		$pub_event['users_photo']=$db->getAllBySql($sql);
		echo json_result($pub_event);
		//echo json_result(array('eventid'=>$eventid));
	}else{
		echo json_result(null,'20','用户未登录或者该活动已删除');
	}

}