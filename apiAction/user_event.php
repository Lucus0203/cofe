<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Upload.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getEvents':
		getEvents();//留言板活动
		break;
	case 'myEvents':
		myEvents();//我发布的活动
		break;
	case 'myJoinEvents':
		myJoinEvents();//我加入的活动
		break;
	case 'eventInfo':
		eventInfo();//活动详情
		break;
	case 'joinEvent':
		joinEvent();//报名活动 
		break;
	case 'cancelJoinEvent':
		cancelJoinEvent();//取消报名
		break;
	case 'eventPublic':
		eventPublic();//发起活动 
		break;
	case 'cancelEvent':
		cancelEvent();//取消活动 
		break;
	case 'leaveMsg':
		leaveMsg();//活动留言 
		break;
	case 'someoneEvents':
		someoneEvents();//他人发布和参与的活动
	default:
		break;
}


//构造匹配次数查询
function convertSign($clum){
	$cofelike=split(",", COFFEE_KEYWORD);
	$str=(count($cofelike)>0) ? " 0 ":"";
	foreach ($cofelike as $like){
		$str.=" + sign(LOCATE('".$like."',".$clum.")) ";
	}
	return $str;
}

//留言板活动
function getEvents(){
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	$list=array();
	//官方活动
// 	$pucount="select count(id) as num,public_event_id from ".DB_PREFIX."public_users pu group by pu.public_event_id ";
// 	$sql1="select pe.id as public_event_id,null as user_event_id,pe.title,pe.img,pe.address,pe.lng,pe.lat,pucount.num,pubusers.created from ".DB_PREFIX."public_event pe
// 	left join ($pucount) pucount on pucount.public_event_id = pe.id
// 	left join ".DB_PREFIX."public_users pubusers on pubusers.public_event_id = pe.id
// 	where pe.isdelete = 0 ";
	//用户活动
	$beforeday=date("Y-m-d",strtotime("-2day",time()));
	$sign1=convertSign('ue.title');
	$sign2=convertSign('ue.address');
	$uercount="select count(id) as num,user_event_id from ".DB_PREFIX."userevent_relation uer group by uer.user_event_id ";
	$sql2="select null as public_event_id,ue.id as user_event_id,ue.title,ue.img,ue.address,ue.lng,ue.lat,uercount.num,ue.created,($sign1 + $sign2) as sign from ".DB_PREFIX."user_event ue
	left join ($uercount) uercount on uercount.user_event_id = ue.id
	where ue.allow = 1 and ue.status = 1 and datetime >= '$beforeday 00:00' order by sign desc,created desc,num desc";
	
	//$sql="select * from ( $sql1 union all $sql2 ) s order by created desc limit $start,$page_size";
	$sql="select * from ( $sql2 ) s limit $start,$page_size";
	$events=$db->getAllBySql($sql);
	foreach ($events as $k=>$v){
		$events[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	$list['events']=$events;
	
	//之前版本的查询方式
// 	$pucount="select count(id) as num,public_event_id from ".DB_PREFIX."public_users pu group by pu.public_event_id ";
// 	$sql="select pe.*,pu.num from ".DB_PREFIX."public_event pe left join ($pucount) pu on pu.public_event_id = pe.id where pe.isdelete = 0 order by ";
// 	$sql.=(!empty($lng)&&!empty($lat))?" sqrt(power(pe.lng-{$lng},2)+power(pe.lat-{$lat},2)),pe.created desc":'pe.created desc';
	
// 	$sql .= " limit 0,$page_size";
// 	$public_event=array();//$db->getAllBySql($sql);//官方活动
// 	foreach ($public_event as $k=>$v){
// 		$public_event[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
// 	}
// 	$list['public_event']=$public_event;
// 	$uercount="select count(id) as num,user_event_id from ".DB_PREFIX."userevent_relation uer group by uer.user_event_id ";
// 	$sql="select ue.*,uer.num from ".DB_PREFIX."user_event ue left join ($uercount) uer on uer.user_event_id = ue.id where ue.allow = 1 and ue.status = 1 order by ";
// 	$sql.=(!empty($lng)&&!empty($lat))?" sqrt(power(ue.lng-{$lng},2)+power(ue.lat-{$lat},2)),ue.created desc":'ue.created desc';
	
// 	$sql .= " limit $start,$page_size";
// 	$user_event=$db->getAllBySql($sql);//个人活动
// 	foreach ($user_event as $k=>$v){
// 		$user_event[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
// 	}
// 	$list['user_event']=$user_event;
	echo json_result($list);
}

//我发起的活动
function myEvents(){
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($userid)){
		echo json_result(null,'27','用户未登录');
		return;
	}
	$uercount="select count(id) as num,user_event_id from ".DB_PREFIX."userevent_relation uer group by uer.user_event_id ";
	$sql="select ue.*,uer.num from ".DB_PREFIX."user_event ue left join ($uercount) uer on uer.user_event_id = ue.id where ue.user_id=$userid and ue.allow = 1 and ue.status = 1 order by ue.created desc ";
	$sql .= " limit $start,$page_size";
	$list=$db->getAllBySql($sql);
	foreach ($list as $k=>$v){
		$list[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	echo json_result($list);
}

//我加入的活动
function myJoinEvents(){
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($userid)){
		echo json_result(null,'28','用户未登录');
		return;
	}
	$list=array();
	$pucount="select count(id) as num,public_event_id from ".DB_PREFIX."public_users pu group by pu.public_event_id ";
	$sql1="select pe.id as public_event_id,null as user_event_id,pe.title,pe.img,pe.address,pe.lng,pe.lat,pucount.num,pubusers.created from ".DB_PREFIX."public_event pe
	left join ($pucount) pucount on pucount.public_event_id = pe.id
	left join ".DB_PREFIX."public_users pubusers on pubusers.public_event_id = pe.id
	where pubusers.user_id = $userid and pe.isdelete = 0 ";
	$uercount="select count(id) as num,user_event_id from ".DB_PREFIX."userevent_relation uer group by uer.user_event_id ";
	
	$sql2="select null,ue.id as user_event_id,ue.title,ue.img,ue.address,ue.lng,ue.lat,uercount.num,ue.created from ".DB_PREFIX."user_event ue
	left join ($uercount) uercount on uercount.user_event_id = ue.id
	left join ".DB_PREFIX."userevent_relation relation on relation.user_event_id=ue.id
	where relation.user_id=$userid and ue.allow = 1 and ue.status = 1 ";
	
	$sql="select * from ( $sql1 union all $sql2 ) s order by created desc limit $start,$page_size";
	$events=$db->getAllBySql($sql);
	foreach ($events as $k=>$v){
		$events[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	$list['events']=$events;
			
// 	if($page_no==1){
// 		$pucount="select count(id) as num,public_event_id from ".DB_PREFIX."public_users pu group by pu.public_event_id ";
// 		$sql="select pe.*,pucount.num from ".DB_PREFIX."public_event pe 
// 		left join ($pucount) pucount on pucount.public_event_id = pe.id 
// 		left join ".DB_PREFIX."public_users pubusers on pubusers.public_event_id = pe.id 
// 		where pubusers.user_id = $userid and pe.isdelete = 0 order by pe.created desc";
// 		$public_event=$db->getAllBySql($sql);
// 		foreach ($public_event as $k=>$v){
// 			$public_event[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
// 		}
// 		$list['public_event']=$public_event;
// 	}
// 	$uercount="select count(id) as num,user_event_id from ".DB_PREFIX."userevent_relation uer group by uer.user_event_id ";
// 	$sql="select ue.*,uercount.num from ".DB_PREFIX."user_event ue 
// 	left join ($uercount) uercount on uercount.user_event_id = ue.id 
// 	left join ".DB_PREFIX."userevent_relation relation on relation.user_event_id=ue.id 
// 	where relation.user_id=$userid and ue.allow = 1 and ue.status = 1 order by ue.created desc ";
// 	$sql .= " limit $start,$page_size";
// 	$user_event=$db->getAllBySql($sql);
// 	foreach ($user_event as $k=>$v){
// 		$user_event[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
// 	}
// 	$list['user_event']=$user_event;
	
	echo json_result($list);
}

//活动详情
function eventInfo(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($eventid)){
		echo json_result(null,'29','该活动已删除');
		return;
	}
	$event=$db->getRow('user_event',array('id'=>$eventid));
	$event['distance']=(!empty($event['lat'])&&!empty($event['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$event['lat'],$event['lng']):lang_UNlOCATE;
	$user=$db->getRow('user',array('id'=>$event['user_id']));
	//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 	$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$user['lat'].",".$user['lng']."&output=json&ak=".BAIDU_AK);
// 	$add=json_decode($add_json);
// 	if($add->status==0){
// 		$user['address']=$add->result->formatted_address;//当前用户位置
// 	}
	//用户之间的距离
	$user['distance']=(!empty($user['lat'])&&!empty($user['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$user['lat'],$user['lng']):lang_UNlOCATE;
	$userphoto=$db->getRow('user_photo',array('id'=>$user['head_photo_id']));
	$user['head_path']=$userphoto['path'];
	$event['user']=$user;
	//报名的人
	$sql="select u.id as user_id,u.nick_name,u.user_name,up.path as head_path from ".DB_PREFIX."userevent_relation uer left join ".DB_PREFIX."user u on uer.user_id = u.id left join ".DB_PREFIX."user_photo up on u.head_photo_id = up.id where uer.user_event_id=".$eventid." order by uer.id desc ";
	$event['jioncount']=$db->getCountBySql($sql);//参与人数
	$event['joins']=$db->getAllBySql($sql);
	//留言的人
	$sql="select up.path as head_path,u.nick_name,u.user_name,u.lng,u.lat,bbs.* from ".DB_PREFIX."userevent_bbs bbs left join ".DB_PREFIX."user u on u.id=bbs.user_id left join ".DB_PREFIX."user_photo up on up.id=u.head_photo_id 
			where bbs.allow=1 and bbs.user_event_id=$eventid";
	$sql.=" order by bbs.id desc limit $start,$page_size";
	$bbs=$db->getAllBySql($sql);
	foreach ($bbs as $k=>$b){
// 		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$b['lat'].",".$b['lng']."&output=json&ak=".BAIDU_AK);
// 		$add=json_decode($add_json);
// 		if($add->status==0){
// 			$bbs[$k]['address']=$add->result->formatted_address;//当前用户位置
// 		}
		$bbs[$k]['distance']=(!empty($b['lat'])&&!empty($b['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$b['lat'],$b['lng']):lang_UNlOCATE;
		
	}
	$event['bbscount']=count($bbs);
	$event['bbs']=$bbs;
	
	//增加浏览数
	$viewcount=($event['viewcount']*1+1);
	$db->update('user_event', array('viewcount'=>$viewcount),array('id'=>$eventid));
	
	echo json_result($event);
}

//我要报名
function joinEvent(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$userid=filter($_REQUEST['userid']);
	if(!empty($eventid)&&!empty($userid)){
		if($db->getCount('userevent_relation',array('user_id'=>$userid,'user_event_id'=>$eventid))==0){
			$up=array('user_id'=>$userid,'user_event_id'=>$eventid,'created'=>date("Y-m-d H:i:s"));
			$db->create('userevent_relation', $up);
		}
		echo json_result(array('eventid'=>$eventid));
	}else{
		echo json_result(null,'30','用户未登录或该活动已删除');
	}
	
}

//取消报名
function cancelJoinEvent(){
	global $db;
	$userid=filter($_REQUEST['userid']);
	$public_event_id=filter($_REQUEST['public_event_id']);
	$user_event_id=filter($_REQUEST['user_event_id']);
	$flag=false;
	if(!empty($user_event_id)){
		$flag=$db->delete('userevent_relation', array('user_event_id'=>$user_event_id,'user_id'=>$userid));
	}
	if(!empty($public_event_id)){
		$flag=$db->delete('public_users', array('public_event_id'=>$user_event_id,'user_id'=>$userid));
	}
	if($flag){
		echo json_result(array('userid'=>$userid));
	}else{
		echo json_result(null,'30','报名已取消失败,请联系管理员');
	}
}

//取消活动
function cancelEvent(){
	global $db;
	$flag=false;
	$userid=filter($_REQUEST['userid']);
	$eventid=filter($_REQUEST['eventid']);
	$flag=$db->update('user_event', array('status'=>2),array('user_id'=>$userid,'id'=>$eventid));
	if($flag){
		echo json_result(array('userid'=>$userid));
	}else{
		echo json_result(null,'30','活动取消失败,请联系管理员');
	}
}

//发起活动
function eventPublic(){
	global $db;
	$userid=filter($_REQUEST['userid']);
	$title=filter($_REQUEST['title']);
	$dating=filter($_REQUEST['dating']);
	$datetime=filter($_REQUEST['datetime']);
	$address=filter($_REQUEST['address']);
	$content=filter($_REQUEST['content']);
	if(empty($userid)){
		echo json_result(null,'31','用户未登录');
		return;
	}
	if(empty($title)){
		echo json_result(null,'32','请填写活动标题');
		return;
	}
	if(empty($dating)){
		echo json_result(null,'33','请填写活动对象');
		return;
	}
	if(empty($datetime)){
		echo json_result(null,'34','请填写活动时间');
		return;
	}
	if(empty($address)){
		echo json_result(null,'35','请填写活动地点');
		return;
	}
	if(empty($content)){
		echo json_result(null,'36','请填写活动内容');
		return;
	}
	$event=array('user_id'=>$userid,'title'=>$title,'dating'=>$dating,'datetime'=>$datetime,'address'=>$address,'content'=>$content,'created'=>date("Y-m-d H:i:s"));
	//$file=$_FILES['photo'];上传图片
	$upload=new UpLoad();
	$folder="upload/userEvent/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user_event'.$userid);
	$file=$upload->upLoad('photo');
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
		$event['img']=APP_SITE.$file['file_path'];
	}
	//获取经纬度
	$loc_json=file_get_contents("http://api.map.baidu.com/geocoder/v2/?address=".$address."&output=json&ak=".BAIDU_AK);
	$loc=json_decode($loc_json);
	if($loc->status==0){
		$event['lng']=$loc->result->location->lng;
		$event['lat']=$loc->result->location->lat;
	}
	$eventid=$db->create('user_event', $event);
	echo json_result(array('eventid'=>$eventid));
}

//活动留言
function leaveMsg(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$userid=filter($_REQUEST['userid']);
	$content=filter($_REQUEST['content']);
	if(empty($eventid)){
		echo json_result(null,'37','该活动已删除');
		return;
	}
	if(empty($userid)){
		echo json_result(null,'38','用户未登录');
		return;
	}
	if(empty($content)){
		echo json_result(null,'39','留言内容为空');
		return;
	}
	$bbs=array('user_id'=>$userid,'user_event_id'=>$eventid,'content'=>$content,'created'=>date("Y-m-d H:i:s"));
	$db->create('userevent_bbs', $bbs);
	echo json_result(array('eventid'=>$eventid));
}

//他人参加和发布的活动
function someoneEvents(){
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($userid)){
		echo json_result(null,'28','用户不正确');
		return;
	}
	$list=array();
	$pucount="select count(id) as num,public_event_id from ".DB_PREFIX."public_users pu group by pu.public_event_id ";
	$sql1="select pe.id as public_event_id,null as user_event_id,pe.title,pe.img,pe.address,pe.lng,pe.lat,pucount.num,pubusers.created from ".DB_PREFIX."public_event pe
	left join ($pucount) pucount on pucount.public_event_id = pe.id
	left join ".DB_PREFIX."public_users pubusers on pubusers.public_event_id = pe.id and pubusers.user_id = $userid
	where pubusers.user_id = $userid and pe.isdelete = 0 ";
	$uercount="select count(id) as num,user_event_id from ".DB_PREFIX."userevent_relation uer group by uer.user_event_id ";
	$sql2="select null,ue.id as user_event_id,ue.title,ue.img,ue.address,ue.lng,ue.lat,uercount.num,ue.created from ".DB_PREFIX."user_event ue
	left join ($uercount) uercount on uercount.user_event_id = ue.id
	left join ".DB_PREFIX."userevent_relation relation on relation.user_event_id=ue.id and relation.user_id=$userid
	where (ue.user_id=$userid or relation.user_id=$userid) and ue.allow = 1 and ue.status = 1 ";
	$sql="select * from ( $sql1 union all $sql2 ) s order by created desc limit $start,$page_size";
	$events=$db->getAllBySql($sql);
	foreach ($events as $k=>$v){
		$events[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	$list['events']=$events;
	$list['user']=$db->getRow('user',array('id'=>$userid),array('id','user_name','nick_name'));
	
	echo json_result($list);
}