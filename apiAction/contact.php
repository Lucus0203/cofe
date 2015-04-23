<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Huanxin.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'nearUsers'://附近想喝咖啡的人
		nearUsers();
		break;
	case 'getUsersByConditions'://筛选附近的人
		getUsersByConditions();
		break;
	case 'getFriends':
		getFriends();//好友/所有联系人(互相关注)
		break;
	case 'getFriendsByUsernames':
		getFriendsByUsernames();//根据咖啡号获取联系人
		break;
	case 'searchUsersByKeyword'://根据关键字查找用户
		searchUsersByKeyword();
		break;
	case 'searchUsersByMobiles'://根据多个手机号码查找用户
		searchUsersByMobiles();
		break;
	case 'searchUsersByNear'://附近可以添加的好友
		searchUsersByNear();
		break;
	case 'recentContacts':
		recentContacts();
		break;
	case 'myFavri':
		myFavri();//我关注的
		break;
	case 'myFuns':
		myFuns();//关注我的
		break;
	case 'myNewFunsCount':
		myNewFunsCount();//新关注我的人数
		break;
	case 'recommend':
		recommend();//推荐联系人
		break;
	case 'createGroup':
		createGroup();//添加分组
		break;
	case 'updateGroup':
		updateGroup();//分组改名
		break;
	case 'myGroups':
		myGroups();//分组列表
		break;
	case 'myGroupWithUsers':
		myGroupWithUsers();//获取分组列表及好友
		break;
	case 'myAllGroupsWithUsers':
		myAllGroupsWithUsers();//获取所有分组列表及好友
		break;
	case 'divideIntoGroups':
		divideIntoGroups();//给联系人分组
		break;
	case 'follow'://邀约
		follow();
		break;
	case 'unfollow'://不再关注
		unfollow();
		break;
	case 'removefan'://移除粉丝
		removefan();
		break;
	case 'black'://拉黑
		black();
		break;
	case 'unblack';//移除黑名单
		unblack();
		break;
	case 'relationName'://备注
		relationName();
		break;
	case 'report'://举报
		report();
		break;
	case 'huanxinFriends'://环信好友
		huanxinFriends();
		break;
	case 'huanxinBlocks'://环信黑名单
		huanxinBlocks();
		break;
	default:
		break;
}

function getFriends(){//好友/所有联系人(互相关注)
	global $db;
	$userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	if(empty($userid)){
		echo json_result(null,'3','请重新登录');
		return;
	}
	$data=array();
	$sql="select u.id as user_id,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.user_name,u.signature,u.sex,u.age,u.constellation,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id 
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1 and ur2.status=1 ";
	
	$z='a';
	for($i=1;$i<=26;$i++){
		$s=$sql." and if ( (ur1.relation_name!='' and ur1.relation_name is not null),if(ur1.relation_pinyin='{$z}',1,0),if(u.pinyin='{$z}',1,0) )  ORDER BY convert(nick_name using gbk); ";
		$data[$z++]=$db->getAllBySql($s);
	}
	
	//26字母以外的
	$s=$sql." and if ( (ur1.relation_name!='' and ur1.relation_name is not null),if(ur1.relation_pinyin='' or ur1.relation_pinyin is null,1,0),if(u.pinyin='' or u.pinyin is null,1,0) )  ORDER BY convert(nick_name using gbk); ";
	$data['other']=$db->getAllBySql($s);
	echo json_result($data);
	
}

//根据咖啡账号查找用户
function getFriendsByUsernames(){
	global $db;
	$usernames=filter($_REQUEST['usernames']);
	$loginid=filter($_REQUEST['loginid']);
	$users=explode(",", $usernames);
	$data=array();
	foreach ($users as $u){
		if(empty($loginid)){
			$sql="select u.id as user_id,upt.path as head_photo,u.nick_name,u.user_name,u.age,u.sex,u.constellation from ".DB_PREFIX."user u left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id where mobile ='$u' ";
		}else{
			$sql="select u.id as user_id,upt.path as head_photo,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.user_name,u.age,u.sex,u.constellation from ".DB_PREFIX."user u 
				left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id 
				left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id and ur1.user_id='$loginid'
				where mobile ='$u' ";
		}
		$obj=$db->getRowBySql($sql);
		if(isset($obj['user_name'])){
			$data[$u]=$obj;
		}
	}
	echo json_result($data);
}

//根据咖啡号手机号名称查找
function searchUsersByKeyword(){
	global $db;
	$keyword=filter(!empty($_REQUEST['keyword'])?$_REQUEST['keyword']:'');
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($keyword)){
		echo json_result(null,'2','请输入想要查询的内容');
		return;
	}
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	if(empty($loginid)){
		echo json_result(null,'3','请重新登录');
		return;
	}
	$sql="select u.id as user_id,upt.path as head_photo,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.user_name,u.sex,u.age,u.constellation,if(ur1.id !='','added','unadd') isadd from ".DB_PREFIX."user u 
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id 
		left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id and ur1.user_id=$loginid
		where user_name ='$keyword' or mobile = '$keyword' or nick_name = '$keyword' ";
	$res['count']=$db->getCountBySql($sql);
	$sql .= " limit $start,$page_size";
	$data = $db->getAllBySql($sql);
	$res['users']=$data;
	echo json_result($res);
		
}

//附近可以添加的好友
function searchUsersByNear(){
	global $db;
	$lng=filter(!empty($_REQUEST['lng'])?$_REQUEST['lng']:'');
	$lat=filter(!empty($_REQUEST['lat'])?$_REQUEST['lat']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($loginid)){
		echo json_result(null,'3','请重新登录');
		return;
	}
	if(empty($lng)||empty($lat)){
		echo json_result(null,'40','获取不到经纬度,请设置允许获取位置');
		return;
	}
	$sql="select u.id as user_id,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.user_name,upt.path as head_photo,u.sex,u.age,u.constellation,u.lng,u.lat,if(ur1.id !='','added','unadd') isadd from ".DB_PREFIX."user u
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
		left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id and ur1.user_id=$loginid
			where u.user_name is not null and u.allow_add = 1 and allow_find=1 and round(6378.138*2*asin(sqrt(pow(sin( ($lat*pi()/180-lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(lat*pi()/180)* pow(sin( ($lng*pi()/180-lng*pi()/180)/2),2)))*1000) <= ".RANGE_KILO;
	
	$res['count']=$db->getCountBySql($sql);
	$data=$db->getAllBySql($sql." order by  sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)) limit $start,$page_size");
	foreach ($data as $k=>$d){
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
	
	}
	$res['users']=$data;
	echo json_result($res);
}

//根据多个手机号查找用户
function searchUsersByMobiles(){
	global $db;
	$mobile=filter($_REQUEST['mobile']);
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($loginid)){
		echo json_result(null,'3','请重新登录');
		return;
	}
	$data=array();
	if(!empty($mobile)){
		$mobiles=explode(',', $mobile);
		$cons='';
		foreach ($mobiles as $m){
			if(!empty($m)){
				$cons.="or mobile='$m' ";
			}
		}
		if(!empty($cons)){
			$cons=substr($cons, 2);
		}else{
			echo json_result(null,'2','没有匹配到手机号');
			return;
		}
		
		$sql="select u.id as user_id,upt.path as head_photo,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.user_name,u.sex,u.age,u.constellation, if(ur1.id !='','added','unadd') isadd from ".DB_PREFIX."user u 
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id 
			left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id and ur1.user_id=$loginid	
			where u.user_name is not null and u.id <> $loginid and ( $cons ) ";
		$res['count']=$db->getCountBySql($sql);
		$sql .= " limit $start,$page_size";
		$data = $db->getAllBySql($sql);
		$res['users']=$data;
	}
	echo json_result($res);
}

// function recentContacts(){//根据user_relation的updated时间判断最近更新的联系人
// 	global $db;
// 	$userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
// 	$lng=filter(!empty($_REQUEST['lng'])?$_REQUEST['lng']:'');
// 	$lat=filter(!empty($_REQUEST['lat'])?$_REQUEST['lat']:'');
// 	$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
// 			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id 
// 			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
// 			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1  order by ur1.updated desc ";
// 	$data=$db->getAllBySql($sql);
// 	foreach ($data as $k=>$d){
// 		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
// 	}
	
// 	echo json_result($data);
// }

function myFavri(){//我关注的
	global $db;
	$userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$lng=filter(!empty($_REQUEST['lng'])?$_REQUEST['lng']:'');
	$lat=filter(!empty($_REQUEST['lat'])?$_REQUEST['lat']:'');
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	if(empty($userid)){
		echo json_result(null,'3','请重新登录');
		return;
	}

	//好友
	$friendsSql="select ur1.relation_id from ".DB_PREFIX."user_relation ur1 
			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id
			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1 ";
	//我关注的人
	$mysql="select u.id as user_id,u.nick_name,u.user_name,u.constellation,u.signature,u.sex,u.age,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id 
	left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
	where allow_find=1 and ur1.user_id = $userid and ur1.status=1 order by ur1.id desc ";
	
	//排除好友
	$sql="select * from ($mysql) m where not exists ( select * from ($friendsSql) f where f.relation_id = m.user_id ) ";
	
	$res['count']=$db->getCountBySql($sql);
	$sql .= " limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	foreach ($data as $k=>$d){
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
	}
	$res['users']=$data;
	
	echo json_result($res);

}

function myFuns(){//关注我的
	global $db;
	$userid=filter($_REQUEST['loginid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	if(empty($userid)){
		echo json_result(null,'3','请重新登录');
		return;
	}
	
	//好友
	$friendsSql="select ur1.relation_id from ".DB_PREFIX."user_relation ur1
			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id
				where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1 ";
	//关注我的人
	$mysql="select u.id as user_id,u.nick_name,u.user_name,u.constellation,u.signature,u.sex,u.age,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur2 on u.id=ur2.user_id 
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where ur2.relation_id = $userid and ur2.status=1 order by ur2.id desc ";
	
	//排除好友
	$sql="select * from ($mysql) m where not exists ( select * from ($friendsSql) f where f.relation_id = m.user_id ) ";
	
	$res['count']=$db->getCountBySql($sql);
	$sql .= " limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	foreach ($data as $k=>$d){
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
	}
	$db->update('user_relation', array('ischeck'=>1),array('relation_id'=>$userid));
	$res['users']=$data;
	echo json_result($res);
	
}

function myNewFunsCount(){//新关注我的人数
	global $db;
	$userid=filter($_REQUEST['userid']);
	$count=$db->getCount('user_relation',array('relation_id'=>$userid,'ischeck'=>'0'));
	echo json_result(array('count'=>$count));
}

function recommend(){//推荐(附近常住地址) RANGE_KILO公里以内
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($lng)||empty($lat)){
		$user=$db->getRow('user',array('id'=>$userid));
		$lng=$user['ad_lng'];
		$lat=$user['ad_lat'];
	}
	if(!empty($lng)&&!empty($lat)){
		$sql="select *,u.head_photo_id,upt.user_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u 
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where u.allow_add = 1 and allow_find=1 and round(6378.138*2*asin(sqrt(pow(sin( ($lat*pi()/180-ad_lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(ad_lat*pi()/180)* pow(sin( ($lng*pi()/180-ad_lng*pi()/180)/2),2)))*1000) <= ".RANGE_KILO;
		$data=$db->getAllBySql($sql." limit $start,$page_size");
		foreach ($data as $k=>$d){
			$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
			
		}
		echo json_result($data);
	}else{
		echo json_result(null,'40','获取不到经纬度,请设置允许获取位置');
	}
}

function nearUsers(){//附近想喝咖啡的人
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$userid=empty($_REQUEST['userid'])?'':filter($_REQUEST['userid']);
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($lng)||empty($lat)){
		echo json_result(null,'40','获取不到经纬度,请设置允许获取位置');
		return;
	}
	$selfcondition="";
	if(!empty($userid)){
		//$selfcondition=" and u.id <> $userid ";
	}
		
	$sql="select u.id,u.nick_name,u.user_name,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.head_photo_id,upt.user_id,upt.path as head_photo,u.sex,u.age,u.constellation,u.lng,u.lat from ".DB_PREFIX."user u 
			left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id and ur1.user_id='$userid'
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
		where CHAR_LENGTH(u.user_name) > 5 and u.user_name is not null and u.head_photo_id is not null and u.allow_add = 1 and allow_find=1 $selfcondition and round(6378.138*2*asin(sqrt(pow(sin( ($lat*pi()/180-lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(lat*pi()/180)* pow(sin( ($lng*pi()/180-lng*pi()/180)/2),2)))*1000) <= ".RANGE_KILO;
	$data=$db->getAllBySql($sql." order by  sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)) , u.updated desc limit $start,$page_size");
	foreach ($data as $k=>$d){
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
	}
	
	//客服
	if($page_no==1){
		$sql="select u.id,u.nick_name,u.user_name,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.head_photo_id,upt.user_id,upt.path as head_photo,u.sex,u.age,u.constellation,u.lng,u.lat from ".DB_PREFIX."user u
				left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id and ur1.user_id='$userid'
					left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
					where u.user_name = '001' ";
		$firstdata=$db->getRowBySql($sql);
		$firstdata['distance']='0';
				array_unshift($data, $firstdata);
	}
	echo json_result($data);
}

function getUsersByConditions(){//筛选附近的人
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$type=!empty($_REQUEST['type'])?$_REQUEST['type']:'';//1只看女,2只看男,3只看同籍
	
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($lng)||empty($lat)){
		echo json_result(null,'40','获取不到经纬度,请设置允许获取位置');
		return;
	}

	$conditions="";
	$homeselect=$homeorderby="";
	if(!empty($userid)){
		$conditions.=" and u.id <> $userid ";
	}
	if(!empty($type)){
		if($type==1){
			$conditions.=" and u.sex = 2 ";
		}
		if($type==2){
			$conditions.=" and u.sex = 1 ";
		}
		if($type==3){
			if(empty($userid)){
				echo json_result(null,'2','如果想查看同籍的人请先登录');
				return;
			}
			$userinfo=$db->getRow('user',array('id'=>$userid));
			$homeselect=", if (home_town_id='{$userinfo['home_town_id']}',1,if (home_city_id='{$userinfo['home_city_id']}',2,3)) as homeorder ";
			$conditions.=" and (home_town_id='{$userinfo['home_town_id']}' or home_city_id='{$userinfo['home_city_id']}' or home_province_id='{$userinfo['home_province_id']}' )";
			$homeorderby=" homeorder asc ,";//先是县区再市最后省排序
		}
	}
	$sql="select u.id,u.nick_name,u.user_name,u.nick_name,u.head_photo_id,upt.user_id,upt.path as head_photo,u.sex,u.age,u.constellation,u.lng,u.lat $homeselect from ".DB_PREFIX."user u 
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
		where u.allow_add = 1 and allow_find=1 and round(6378.138*2*asin(sqrt(pow(sin( ($lat*pi()/180-lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(lat*pi()/180)* pow(sin( ($lng*pi()/180-lng*pi()/180)/2),2)))*1000) <= ".RANGE_KILO;
	$sql.=$conditions;
	
	$data=$db->getAllBySql($sql." order by $homeorderby sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)) limit $start,$page_size");
	foreach ($data as $k=>$d){
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
	}
	echo json_result($data);
}

function myGroups(){//分组列表
	global $db;
	$userid=filter($_REQUEST['userid']);
	$groups=$db->getAll('user_group',array('user_id'=>$userid));
	echo json_result($groups);
}

function myGroupWithUsers(){//获取分组好友
	global $db;
	$userid=filter($_REQUEST['userid']);
	$groupid=filter($_REQUEST['groupid']);
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
		left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
		where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1  and ur1.group_id in (".$groupid.")";
	$users=array();
	$users=$db->getAllBySql($sql);
	echo json_result($users);
}


function myAllGroupsWithUsers(){//获取所有分组列表及好友
	global $db;
	$userid=filter($_REQUEST['userid']);
	$groups=$db->getAll('user_group',array('user_id'=>$userid));
	$data=array();
	foreach ($groups as $g){
		$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1  and ur1.group_id=".$g['id'];
		$users=array();
		$users=$db->getAllBySql($sql);
		$g['users']=$users;
		$data[]=$g;
	}
	echo json_result($data);
}

function createGroup(){//添加分组
	global $db;
	$userid=filter($_REQUEST['userid']);
	$name=filter($_REQUEST['name']);//分组名称
	if(empty($userid)){
		echo json_result(null,'38','用户未登录');
		return;
	}
	if(empty($name)){
		echo json_result(null,'41','分组名称为空');
		return;
	}
	$info=array('user_id'=>$userid,'name'=>$name,'created'=>date("Y-m-d H:i:s"));
	$info['id']=$db->create('user_group', $info);
	echo json_result($info);
	
}

function updateGroup(){//修改分组名
	global $db;
	$userid=filter($_REQUEST['userid']);
	$groupid=filter($_REQUEST['groupid']);
	$name=filter($_REQUEST['name']);//分组名称
	if(empty($userid)){
		echo json_result(null,'38','用户未登录');
		return;
	}
	if(empty($groupid)){
		echo json_result(null,'45','分组id为空');
		return;
	}
	if(empty($name)){
		echo json_result(null,'46','分组名称为空');
		return;
	}
	$info=array('user_id'=>$userid,'id'=>$groupid);
	$db->update('user_group',array('name'=>$name), $info);
	$info['name']=$name;
	echo json_result($info);
}

function divideIntoGroups(){//给联系人分组
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	$groupid=filter($_REQUEST['groupid']);
	if(empty($loginid)){
		echo json_result(null,'38','用户未登录');
		return;
	}
	if(empty($userid)){
		echo json_result(null,'42','未指定联系人');
		return;
	}
	if(empty($groupid)){
		echo json_result(null,'43','未指定分组');
		return;
	}
	$ur=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	$db->update('user_relation',array('group_id'=>$groupid),array('user_id'=>$loginid,'relation_id'=>$userid));
	$gus=array();
	$group_old=$db->getRow('user_group',array('id'=>$ur['group_id'],'user_id'=>$loginid));
	$group_old['users']=getUsersByGroupId($loginid,$group_old['id']);
	$group_new=$db->getRow('user_group',array('id'=>$groupid,'user_id'=>$loginid,));
	$group_new['users']=getUsersByGroupId($loginid,$groupid);
	$gus['group_old']=$group_old;
	$gus['group_new']=$group_new;
	echo json_result($gus);
}

function follow(){//关注
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	//默认分组好友
	$ginfo=array('user_id'=>$loginid,'name'=>'好友');
	$fgroup=$db->getRow('user_group',$ginfo);
	if(!is_array($fgroup)&&count($fgroup)==0){//没有好友分组
		$ginfo['created']=date("Y-m-d H:i:s");
		$gid=$db->create('user_group', $ginfo);
	}else{
		$gid=$fgroup['id'];
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	$touser=$db->getRow('user',array('id'=>$userid));
	if($touser['allow_flow']==2){
		echo json_result(null,'47','对方不想被人添加关注');
		return;
	}
	if(!is_array($relation)||count($relation)==0){//没关注
		$nickname=$db->getRow('user',array('id'=>$userid),array('nick_name','pinyin'));
		$rinfo['group_id']=$gid;
		$rinfo['created']=date("Y-m-d H:i:s");
		$rinfo['relation_name']=$nickname['nick_name'];
		$rinfo['relation_pinyin']=$nickname['pinyin'];
		$db->create('user_relation', $rinfo);//关注
	}elseif ($relation['status']==2){//拉黑者
		$relation['status']=1;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

//不再关注
function unfollow(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	if(empty($loginid)){
		echo json_result(null,2,'请先登录');
		return;
	}
	if(empty($userid)){
		echo json_result(null,3,'对方不在您的关注状态中');
		return;
	}
	$db->delete('user_relation', array('user_id'=>$loginid,'relation_id'=>$userid));
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

//移除粉丝
function removefan(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	if(empty($loginid)){
		echo json_result(null,2,'请先登录');
		return;
	}
	if(empty($userid)){
		echo json_result(null,3,'对方不是您的粉丝');
		return;
	}
	$db->delete('user_relation', array('user_id'=>$userid,'relation_id'=>$loginid));
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

function black(){//拉黑
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	//默认分组好友
	$ginfo=array('user_id'=>$loginid,'name'=>'好友');
	$fgroup=$db->getRow('user_group',$ginfo);
	if(!is_array($fgroup)&&count($fgroup)==0){//没有好友分组
		$ginfo['created']=date("Y-m-d H:i:s");
		$gid=$db->create('user_group', $ginfo);
	}else{
		$gid=$fgroup['id'];
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	if(!is_array($relation)||count($relation)==0){//没关注
		$nickname=$db->getRow('user',array('id'=>$userid),array('nick_name','pinyin'));
		$rinfo['group_id']=$gid;
		$rinfo['status']=2;
		$rinfo['created']=date("Y-m-d H:i:s");
		$rinfo['relation_name']=$nickname['nick_name'];
		$rinfo['relation_pinyin']=$nickname['pinyin'];
		$db->create('user_relation', $rinfo);//关注
	}else{//拉黑者
		$relation['status']=2;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	
	$login=$db->getRow('user',array('id'=>$loginid),array('mobile'));
	$user=$db->getRow('user',array('id'=>$userid),array('mobile'));
	//环信拉黑
	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->block($login['mobile'], $user['mobile']);
	
	$db->update('user_relation',array('status'=>2),array('user_id'=>$loginid,'relation_id'=>$userid));
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

function unblack(){//移除黑名单
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	//默认分组好友
	$ginfo=array('user_id'=>$loginid,'name'=>'好友');
	$fgroup=$db->getRow('user_group',$ginfo);
	if(!is_array($fgroup)&&count($fgroup)==0){//没有好友分组
		$ginfo['created']=date("Y-m-d H:i:s");
		$gid=$db->create('user_group', $ginfo);
	}else{
		$gid=$fgroup['id'];
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	if(!is_array($relation)||count($relation)==0){//没关注
		$rinfo['group_id']=$gid;
		$rinfo['status']=1;
		$rinfo['created']=date("Y-m-d H:i:s");
		$db->create('user_relation', $rinfo);//关注
	}else{//已关注
		$relation['status']=1;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	

	$login=$db->getRow('user',array('id'=>$loginid),array('mobile'));
	$user=$db->getRow('user',array('id'=>$userid),array('mobile'));
	//环信移除黑名单
	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->unblock($login['mobile'], $user['mobile']);
	
	$db->update('user_relation',array('status'=>1),array('user_id'=>$loginid,'relation_id'=>$userid));
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

function getUsersByGroupId($userid,$groupid){//获取分组好友
	global $db;
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
		where ur1.user_id = $userid and ur1.status=1  and ur1.group_id =".$groupid." order by ur1.created asc";
	$users=$db->getAllBySql($sql);
	return $users;
}

//备注
function relationName(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	$name=filter($_REQUEST['name']);
	if(empty($loginid)){
		echo json_result(null,2,'请先登录');
		return;
	}
	$conditions=array('user_id'=>$loginid,'relation_id'=>$userid);
	$pinyin=!empty($name)?getFirstCharter($name):'';
	$db->update('user_relation',array('relation_name'=>$name,'relation_pinyin'=>$pinyin),$conditions);
	echo json_result(array('success'=>'TRUE'));
}

//举报
function report(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	$content=filter($_REQUEST['content']);
	//举报
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid,'content'=>$content);
	$reportcount=$db->getCount('user_report',array('relation_id'=>$userid));
	$rinfo['created']=date("Y-m-d H:i:s");
	$db->create('user_report', $rinfo);//举报

	$reportcount=$db->getCount('user_report',array('relation_id'=>$userid));
	$db->update('user', array('report'=>$reportcount));//更新举报次数

	echo json_result(array('success'=>"TRUE"));
}

//环信好友
function huanxinFriends(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$login=$db->getRow('user',array('id'=>$loginid),array('mobile'));

	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->getFriends($login['mobile']);
	print_r($huserObj);
}

//环信黑名单
function huanxinBlocks(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$login=$db->getRow('user',array('id'=>$loginid),array('mobile'));

	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->getBlocks($login['mobile']);
	print_r($huserObj);
}

function getRelationStatus($myself_id,$user_id){
	global $db;
	$info=array();
	//我关注的
	$myfav_count=$db->getCount('user_relation',array('user_id'=>$myself_id,'relation_id'=>$user_id));
	//关注我的
	$myfun_count=$db->getCount('user_relation',array('user_id'=>$user_id,'relation_id'=>$myself_id));
	if($myfav_count>0&&$myfun_count>0){
		$info['relation']='好友';
		$info['relation_status']=4;
	}elseif ($myfav_count>0){
		$info['relation']='关注中';//我关注的人
		$info['relation_status']=2;
	}elseif ($myfun_count>0){
		$info['relation']='被关注';//关注我的人
		$info['relation_status']=3;
	}
	if ($myfun_count>0){
		$re=$db->getRow('user_relation',array('user_id'=>$user_id,'relation_id'=>$myself_id));
		if($re['status']==2){
			$info['relation']='陌生人';//对方黑名单中
			$info['relation_status']=6;
		}
	}
	if ($myfav_count>0){
		$re=$db->getRow('user_relation',array('user_id'=>$myself_id,'relation_id'=>$user_id));
		if($re['status']==2){
			$info['relation']='黑名单';//黑名单
			$info['relation_status']=5;
		}
	}
	if($myfav_count<=0&&$myfun_count<=0){
		$info['relation']='陌生人';//陌生人
		$info['relation_status']=1;
	}
	return $info;
}

