<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'JPush.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'sendMsg':
		sendMsg();//用户聊天
		break;
	case 'getChatBySendToId':
		getChatBySendToId();//聊天记录
		break;
	case 'getChatList':
		getChatList();//消息列表
		break;
	default:
		break;
}

function sendMsg(){//发送聊天消息
	global $db;
	$fromid=filter($_REQUEST['from_userid']);
	$toid=filter($_REQUEST['to_userid']);
	$content=filter($_REQUEST['content']);
	$fromuser=$db->getRow('user',array('id'=>$fromid),array('user_name','nick_name'));
	$touser=$db->getRow('user',array('id'=>$toid),array('user_name'));
	//查找两者关系
	$relation=$db->getRow('user_relation',array('user_id'=>$toid,'relation_id'=>$fromid,'status'=>2));//对方黑名单
	if(is_array($relation)&&count($relation)>0){
		echo json_result(null,'44','您在对方黑名单中,您不能给该用户发消息');
		return;
	}
	$info=array('updated'=>date("Y-m-d H:i:s"));
	$db->update('user_relation', $info,array('user_id'=>$fromid,'relation_id'=>$toid));
	$data['from']=empty($fromuser['nick_name'])?$fromuser['user_name']:$fromuser['nick_name'];
	$data['content']=$content;//发送消息 必须
	$data['tag']=$toid;//用户id区别//用标签来进行大规模的设备属性、用户属性分群。一次推送最多 20 个。 必须
	$data['alias']=$touser['user_name'];//咖啡号区别//用别名来标识一个用户。一个设备只能绑定一个别名，但多个设备可以绑定同一个别名。一次推送最多 1000 个。 必须
	$data['extras']=array('userid'=>$toid);//返回附加消息一般用户id//"extras" : { "userid" : 321}
	$jpush=new jPush();
	$res=$jpush->jpush($data);
	if($res['msg'] == '消息推送成功'){
		$chat=array('user_id'=>$fromid,'sendto_id'=>$toid,'content'=>$content,'isread'=>2,'created'=>date("Y-m-d H:i:s"));
		$db->create('chat', $chat);
		echo json_result($res);
	}else{
		echo json_result(null,'1000',$res);
	}	
}

function getChatBySendToId(){//查看聊天记录
	global $db;
	$loginid=filter($_REQUEST['from_userid']);
	$sendtoid=filter($_REQUEST['to_userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($loginid)){
		echo json_result(null,'38','您还未登录');
		return;
	}
	if(empty($sendtoid)){
		echo json_result(null,'44','未指定联系人');
		return;
	}
	$sql="select fromu.id as from_user_id,fromup.path as from_head_photo,tou.id as to_user_id,toup.path as to_head_photo,c.content,c.created from ".DB_PREFIX."chat c 
			left join ".DB_PREFIX."user fromu on fromu.id=c.user_id
	 		left join ".DB_PREFIX."user_photo fromup on fromu.head_photo_id=fromup.id 
			left join ".DB_PREFIX."user tou on tou.id=c.sendto_id
	 		left join ".DB_PREFIX."user_photo toup on tou.head_photo_id=toup.id 
			where (c.user_id=$loginid and c.sendto_id = $sendtoid) or (c.user_id=$sendtoid and c.sendto_id = $loginid) order by created desc";
	$sql.=" limit $start,$page_size";
	$chats=$db->getAllBySql($sql);
	$touser=$db->getRow('user',array('id'=>$sendtoid));
	$distance=($touser['allow_add']==1&&!empty($lng)&&!empty($lat)&&!empty($touser['lng'])&&!empty($touser['lat']))?getDistance($lat, $lng, $touser['lat'], $touser['lng']):lang_UNlOCATE;
	$res=array('time'=>date("m-d H:i"),'distance'=>$distance,'chats'=>$chats);
	//更新已读状态
	$db->update('chat', array('isread'=>1),array('user_id'=>$sendtoid,'sendto_id'=>$loginid));
	echo json_result($res);
	
}

function getChatList(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	if(empty($loginid)){
		echo json_result(null,'38','您还未登录');
		return;
	}
	$sql="select c.*,up.path as head_photo,u.nick_name,u.user_name,u.lng,u.lat from ".DB_PREFIX."chat c left join ".DB_PREFIX."user u on c.user_id=u.id left join ".DB_PREFIX."user_photo up on up.id=u.head_photo_id where c.sendto_id = $loginid group by c.user_id order by c.id desc ";
	$chatlist=$db->getAllBySql($sql);
	foreach ($chatlist as $k=>$c){
		$chatlist[$k]['num']=$db->getCount('chat',array('user_id'=>$c['user_id'],'isread'=>2));
		
		//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$c['lat'].",".$c['lng']."&output=json&ak=".BAIDU_AK);
		$add=json_decode($add_json);
		if($add->status==0){
			$chatlist[$k]['current_address']=$add->result->formatted_address;//当前用户位置
		}
		
	}
	echo json_result($chatlist);
}