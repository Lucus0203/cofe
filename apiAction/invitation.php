<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Umeng.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getNewInvitationCounts':
		getNewInvitationCounts();//获取未读邀请函
		break;
	case 'sendInvitation':
		sendInvitation();//发送邀请函
		break;
	case 'getInvitation':
		getInvitation();//查看邀请函
		break;
	case 'acceptInvitation'://接受邀请函
		acceptInvitation();
		break;
	case 'refuseInvitation'://拒绝邀请函
		refuseInvitation();
		break;
	case 'invitationBySend'://我发出的
		invitationBySend();
		break;
	case 'invitationByAccept'://我接受的
		invitationByAccept();
		break;
	case 'cancelInvitation'://取消邀请函
		cancelInvitation();
		break;
	case 'delInvitation'://删除邀请函
		delInvitation();
		break;
	default:
		break;
}

function getNewInvitationCounts(){//获取未读邀请函
	global $db;
	$userid=filter($_REQUEST['userid']);
	$sql="select count(*) as num from ".DB_PREFIX."invitation where ((user_id=$userid && isreaded_user=2 && del_user=2) or (to_user_id=$userid && isreaded_to_user=2 && del_to_user=2))  limit 1";
	$count=$db->getRowBySql($sql);
	echo json_result(array('count'=>$count['num']));
	
}

//发送邀请函
function sendInvitation(){
	global $db;
	
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');
	$to_userid=filter(!empty($_REQUEST['touserid'])?$_REQUEST['touserid']:'');
	$title=filterIlegalWord(!empty($_REQUEST['title'])?$_REQUEST['title']:'');
	$datetime=filter(!empty($_REQUEST['datetime'])?$_REQUEST['datetime']:'');
	//$address=filter(!empty($_REQUEST['address'])?$_REQUEST['address']:'');
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	$tel=filter(!empty($_REQUEST['tel'])?$_REQUEST['tel']:'');

	//待接收邀约数
	$count=$db->getCount('invitation',array('status'=>1,'user_id'=>$userid));
	if($count>0){
		echo json_result(null,'2','您还有一个待对方接收的邀请');//是的,要取消,不,再耐心等等
		return;
	}
	
	//黑名单，对方暂不接受邀请
	$relation=$db->getRow('user_relation',array('user_id'=>$to_userid,'relation_id'=>$userid),array('status'));
	if(!empty($relation['status'])&&$relation['status']==2){
		echo json_result(null,'3','对方暂不接受邀请');//是的,要取消,不,再耐心等等
		return;
	}
	
	$shop=$db->getRow('shop',array('id'=>$shopid));
	//isreaded 1已读 2未读
	$invitation=array('title'=>$title,'datetime'=>$datetime,'shop_id'=>$shopid,'address'=>$shop['address'],'lng'=>$shop['lng'],'lat'=>$shop['lat'],'tel'=>$tel,'user_id'=>$userid,'to_user_id'=>$to_userid,'isreaded_user'=>1,'isreaded_to_user'=>2,'status'=>1,'created'=>date("Y-m-d H:i:s"));
	$db->create('invitation', $invitation);
	
	$fromuser=$db->getRow('user',array('id'=>$userid),array('nick_name'));
	
// 	$Aumeng=new Umeng('Android');
// 	$Aumeng->sendAndroidCustomizedcast("invitation",$to_userid,"您有新的邀约","咖啡约我","新的邀请函","go_app","");//go_activity
	
 	$IOSumeng=new Umeng('IOS');
 	$IOSumeng->sendIOSCustomizedcast("invitation", $to_userid, '您有一封来自"'.$fromuser['nick_name'].'"的邀请函',array('notify'=>'invitation'));

	echo json_result(array('success'=>'TRUE'));

}

//查看邀请函
function getInvitation(){
	global $db;
	
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$invitation=$db->getRow('invitation',array('id'=>$invitationid));
	$shop=$db->getRow('shop',array('id'=>$invitation['shop_id']),array('title'));
	$invitation['shop_title']=$shop['title'];
	if(empty($invitation)){
		echo json_result(null,'2','您查看的内容不存在');
		return;
	}
	if($invitation['user_id']==$userid){
		$db->update('invitation', array('isreaded_user'=>1),array('id'=>$invitationid));
	}
	if($invitation['to_user_id']==$userid){
		$db->update('invitation', array('isreaded_to_user'=>1),array('id'=>$invitationid));
	}
	$from=$db->getRow('user',array('id'=>$invitation['user_id']),array('head_photo_id','nick_name'));
	$invitation['user_nickname']=$from['nick_name'];
	$invitation['user_photo']='';
	$touser=$db->getRow('user',array('id'=>$invitation['to_user_id']),array('head_photo_id','nick_name'));
	$invitation['to_user_nickname']=$touser['nick_name'];
	$invitation['to_user_photo']='';
	if(!empty($from['head_photo_id'])){
		$fromphoto=$db->getRow('user_photo',array('id'=>$from['head_photo_id']));
		$invitation['user_photo']=$fromphoto['path'];
	}
	if(!empty($touser['head_photo_id'])){
		$tophoto=$db->getRow('user_photo',array('id'=>$touser['head_photo_id']));
		$invitation['to_user_photo']=$tophoto['path'];
	}
	$res['invitation']=$invitation;
	
// 	$info=$db->getRow('user',array('id'=>$invitation['user_id']));
// 	unset($info['user_password']);
// 	$me=$db->getRow('user',array('id'=>$userid));
// 	$info['distance']=(!empty($me['lat'])&&!empty($me['lng'])&&!empty($info['lat'])&&!empty($info['lng']))?getDistance($info['lat'],$info['lng'],$me['lat'],$me['lng']):lang_UNlOCATE;
// 	$info['lasttime']=time2Units(time()-strtotime($info['logintime']));
// 	$info['address']=getAddressFromBaidu($info['lng'],$info['lat']);
// 	//头像
// 	if(!empty($info['head_photo_id'])){
// 		$head=$db->getRow('user_photo',array('id'=>$info['head_photo_id']));
// 		$info['head_photo']=$head['path'];
// 	}
// 	$res['userInfo']=$info;
	
	echo json_result($res);
	
}

//接受
function acceptInvitation(){
	global $db;
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$count=$db->getCount('invitation',array('id'=>$invitationid,'to_user_id'=>$userid));
	if ($count<=0){
		echo json_result(null,'2','数据不符,您不能接受不属于您的邀请函');
		return;
	}
	$db->update('invitation', array('isreaded_user'=>2,'isreaded_to_user'=>1,'status'=>2),array('id'=>$invitationid));
	
	//通知
	$inv=$db->getRow('invitation',array('id'=>$invitationid),array('user_id'));
	$touser=$db->getRow('user',array('id'=>$userid),array('nick_name'));
	
	$IOSumeng=new Umeng('IOS');
 	$IOSumeng->sendIOSCustomizedcast("invitation", $inv['user_id'], '"'.$touser['nick_name'].'"接受了您的邀请函',array('notify'=>'invitation'));
	
 	echo json_result(array('success'=>'TRUE'));
	
}

//拒绝
function refuseInvitation(){
	global $db;
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$count=$db->getCount('invitation',array('id'=>$invitationid,'to_user_id'=>$userid));
	if ($count<=0){
		echo json_result(null,'2','数据不符,您不能拒绝不属于您的邀请函');
		return;
	}
	$db->update('invitation', array('isreaded_user'=>2,'isreaded_to_user'=>1,'status'=>3),array('id'=>$invitationid));
	
	//通知
	$inv=$db->getRow('invitation',array('id'=>$invitationid),array('user_id'));
	$touser=$db->getRow('user',array('id'=>$userid),array('nick_name'));
	
	$IOSumeng=new Umeng('IOS');
	$IOSumeng->sendIOSCustomizedcast("invitation", $inv['user_id'], '"'.$touser['nick_name'].'"拒绝了您的邀请函',array('notify'=>'invitation'));
	
	echo json_result(array('success'=>'TRUE'));
	
}

//我发出的邀请函
function invitationBySend(){
	global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = 10;
	$start = ($page_no - 1) * $page_size;
	
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	$sql="select inv.id,inv.title,inv.user_id,u.nick_name,tu.nick_name as to_nick_name,inv.to_user_id,inv.status,inv.isreaded_user,inv.isreaded_to_user,upt.path as photo from ".DB_PREFIX."invitation inv 
			left join ".DB_PREFIX."user u on inv.user_id = u.id 
			left join ".DB_PREFIX."user tu on inv.to_user_id = tu.id 
			left join ".DB_PREFIX."user_photo upt on upt.id=tu.head_photo_id where 1=1 ";
	$sql.=" and inv.user_id=$userid and inv.del_user <> '1' order by id desc ";
	$sql .= " limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	echo json_result($data);
}

//我接受的邀请函
function invitationByAccept(){
	global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = 10;
	$start = ($page_no - 1) * $page_size;
	
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	$sql="select inv.id,inv.title,u.nick_name,inv.user_id,tu.nick_name as to_nick_name,inv.to_user_id,inv.status,inv.isreaded_user,inv.isreaded_to_user,upt.path as photo from ".DB_PREFIX."invitation inv 
			left join ".DB_PREFIX."user u on inv.user_id = u.id 
			left join ".DB_PREFIX."user tu on inv.to_user_id = tu.id 
			left join ".DB_PREFIX."user_photo upt on upt.id=u.head_photo_id where 1=1 ";
	$sql.=" and inv.to_user_id=$userid and inv.del_to_user <> '1' order by id desc";
	$sql .= " limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	echo json_result($data);
	
}

//取消邀请函
function cancelInvitation(){
	global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	$count=$db->getCount('invitation',array('id'=>$invitationid,'user_id'=>$userid));
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	if($count<=0){
		echo json_result(null,'3','请选择您发出的邀请函');
		return;
	}
	$db->update('invitation', array('status'=>4),array('id'=>$invitationid,'user_id'=>$userid));
	echo json_result(array('success'=>'TRUE'));
}

//删除邀请函邀请函
function delInvitation(){
	global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	$inv=$db->getRow('invitation',array('id'=>$invitationid),array('status','user_id','to_user_id'));
	$touser=$db->getRow('user',array('id'=>$inv['to_user_id']),array('nick_name'));
	//发起者删除
	$condition=array('id'=>$invitationid,'user_id'=>$userid);
	$count=$db->getCount('invitation',$condition);
	if($count>0){
		$data=array('del_user'=>'1');
		if($inv['status']==1&&$inv['isreaded_to_user']==2){
			echo json_result(null,'3','请等待对方回应或取消');
			return;
		}elseif($inv['status']==1){
			$data['status']=4;
		}
		$condition=array('id'=>$invitationid,'user_id'=>$userid);
		$db->update('invitation', $data,$condition);
	}
	//接受者删除
	$condition=array('id'=>$invitationid,'to_user_id'=>$userid);
	$count=$db->getCount('invitation',$condition);
	if($count>0){
		$data=array('del_to_user'=>'1');
		if($inv['status']==1){
			$data['status']=3;
			$IOSumeng=new Umeng('IOS');
			$IOSumeng->sendIOSCustomizedcast("invitation", $inv['user_id'], '"'.$touser['nick_name'].'"拒绝了您的邀请函',array('notify'=>'invitation'));
			
		}
		$condition=array('id'=>$invitationid,'to_user_id'=>$userid);
		$db->update('invitation', $data , $condition);
	}
	echo json_result(array('success'=>'TRUE'));
}



