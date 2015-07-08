<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getEvents':
		getEvents();//首页获取官方活动
		break;
	case 'eventInfo':
		eventInfo();
		break;
	case 'collectEvent'://收藏
		collectEvent();
		break;
	case 'isCollect'://查看是否收藏
		isCollect();
		break;
	default:
		break;
}

//首页获取官方活动
function getEvents(){
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	//$pucount="select count(id) as num,public_event_id from ".DB_PREFIX."public_users pu group by pu.public_event_id ";
	if(!empty($loginid)){//已登录者
		$sql="select pe.id,pe.title,pe.address,pe.datetime,if(pu.user_id is not null,1,2) as iscollect from ".DB_PREFIX."public_event pe 
				left join ".DB_PREFIX."public_users pu on pe.id=pu.public_event_id and pu.user_id=$loginid 
				where pe.isdelete = 0 and pe.ispublic=1 and (pe.end_date > '".date('Y-m-d H:i:s')."' or pe.end_date = '' or pe.end_date is null ) 
			 	order by pu.user_id,pe.num asc";
	}else{//未登录
		$sql="select pe.id,pe.title,pe.address,pe.datetime from ".DB_PREFIX."public_event pe 
				where pe.isdelete = 0 and pe.ispublic=1 and (pe.end_date > '".date('Y-m-d H:i:s')."' or pe.end_date = '' or pe.end_date is null ) 
				order by pe.num asc";
	}
	$sql.=(!empty($lng)&&!empty($lat))?",sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))":'';
	$sql .= ",pe.created,pe.id desc limit $start,$page_size";
	$list=$db->getAllBySql($sql);
	foreach ($list as $k=>$v){
		//$list[$k]['datetime']=strtotime($v['datetime']);
		$created = date("m.d",strtotime($v['created']));
		if(empty($v['end_date'])){
			$list[$k]['created'] = date("Y.m.d",strtotime($v['created']));
		}else{
			$end_date = date("m.d",strtotime($v['end_date']));
			$list[$k]['created'] = $created.'~'.$end_date;
		}
		$list[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	echo json_result($list);
}

//收藏活动
function collectEvent(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$loginid=filter($_REQUEST['loginid']);
	if(!empty($eventid)&&!empty($loginid)){
		if($db->getCount('public_users',array('user_id'=>$loginid,'public_event_id'=>$eventid))==0){
			$up=array('user_id'=>$loginid,'public_event_id'=>$eventid,'created'=>date("Y-m-d H:i:s"));
			$db->create('public_users', $up);
		}
		//活动用户及头像地址
// 		$sql="select u.id as user_id,u.nick_name,u.user_name,up.path from ".DB_PREFIX."public_users pu left join ".DB_PREFIX."user u on pu.user_id = u.id left join ".DB_PREFIX."user_photo up on u.head_photo_id = up.id where pu.public_event_id=".$eventid;
// 		$pub_event['user_count']=$db->getCountBySql($sql);//参与人数
// 		$pub_event['users_photo']=$db->getAllBySql($sql);
		//echo json_result($pub_event);
		echo json_result('success');
		//echo json_result(array('eventid'=>$eventid));
	}else{
		echo json_result(null,'20','用户未登录或者该活动已删除');
	}

}

//是否收藏
function isCollect(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$loginid=filter($_REQUEST['loginid']);
	if(!empty($eventid)&&!empty($loginid)){
		if($db->getCount('public_users',array('user_id'=>$loginid,'public_event_id'=>$eventid))>0){
			echo json_result('1');//已收藏
		}else{
			echo json_result('2');//未收藏
		}
	}else{
		echo json_result(null,'20','用户未登录或者该活动已删除');
	}
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
