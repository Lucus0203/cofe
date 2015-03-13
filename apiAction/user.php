<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Upload.php';
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Huanxin.php';
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Sms.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getVerificationCode':
		getVerificationCode();//获取验证码
		break;
	case 'resetPassword':
		resetPassword();//重置密码
		break;
	case 'register':
		register();//注册
		break;
	case 'login':
		login();//登录
		break;
	case 'info':
		info();//个人信息
		break;
	case 'infoEdit':
		infoEdit();//个人信息修改
		break;
	case 'infoImgs'://相册
		infoImgs();
		break;
	case 'uploadImgs'://上传多个图片
		uploadImgs();
		break;
	case 'uploadOnceImg'://上传单个图片
		uploadOnceImg();
		break;
	case 'deleteImg'://删除图片
		deleteImg();
		break;
	case 'changeHeadImg'://选择头像
		changeHeadImg();
		break;
	case 'uploadHeadImg':
		uploadHeadImg();
		break;
	case 'getAdd':
		getLocation();//获取常住经纬度
		break;
	case 'getCurrent';
		getCurrent();//获取当前经纬度
		break;
	case 'updateCurrent':
		updateCurrent();//更新当前经纬度
		break;
	case 'allowLngLat':
		allowLngLat();//允许获取经纬度
		break;
	case 'allowFind':
		allowFind();//允许找到我
		break;
	case 'allowFlow':
		allowFlow();//允许关注我
		break;
	case 'allowNews':
		allowNews();//允许新消息
		break;
	default:
		break;
}

//验证码
function getVerificationCode(){
	global $db;
	$data=filter($_REQUEST);
	$mobile=$data['mobile'];
	$type=empty($data['type'])?'':$data['type'];//1注册验证码2找回密码验证码
	if($type!=2){
		if($db->getCountBySql("select id from ".DB_PREFIX."user where mobile='$mobile' and uuid is not null and uuid <> '' ")>0){
			echo json_result(null,'7','此手机号已经注册过');
			return;
		}
	}
	if(trim($mobile)==''){
		echo json_result(null,'5','请填写手机号码');//请填写手机号码
		return;
	}
	if(trim($mobile)!=''&&!checkMobile($mobile)){
		echo json_result(null,'6','手机号码不正确');//手机号码不正确
		return;
	}
	//生成验证码
	$code=rand(100000, 999999);
	if(!empty($mobile)&&$db->getCount("user",array('mobile'=>$mobile))>0){
		$db->update('user',array('captcha_code'=>$code),array('mobile'=>$mobile));
	}else{
		$user=array('mobile'=>$mobile,'captcha_code'=>$code);
		$db->create('user', $user);
	}
	$sms=new Sms();
	$sms->sendMsg("您本次验证码是:".$code."，欢迎您使用", $mobile);
	echo json_result(array('success'=>"TRUE"));//array('code'=>$code)
	
}
//重置密码
function resetPassword(){
	global $db;
	$data=filter($_REQUEST);
	$mobile=!empty($data['mobile'])?$data['mobile']:'';
	$code=!empty($data['code'])?$data['code']:'';
	$user_pass=!empty($data['user_password'])?$data['user_password']:'';
	if(trim($mobile)==''){
		echo json_result(null,'5','请填写手机号码');//请填写手机号码
		return;
	}
	if(trim($mobile)!=''&&!checkMobile($mobile)){
		echo json_result(null,'6','手机号码不正确');//手机号码不正确
		return;
	}
	
	if(trim($user_pass)==''){
		echo json_result(null,'7','请填写密码');//请填写密码
		return;
	}
	
	if($db->getCount("user",array('mobile'=>$mobile,'captcha_code'=>$code))<=0){
		echo json_result(null,'8','验证码不正确');
		return;
	}
	$userInfo=$db->getRow('user',array('mobile'=>$mobile));
	if(!empty($userInfo['uuid'])){
		$pass=array('user_password'=>md5($user_pass));
		$HuanxinObj=Huanxin::getInstance();
		$huserObj=$HuanxinObj->updatePass(strtolower($mobile), md5($user_pass));
		if($huserObj->duration){
			$flag=$db->update('user', $pass ,array('mobile'=>$mobile,'captcha_code'=>$code));
		}else{
			echo json_result(null,'9','密码修改失败,请联系客服');
			return;
		}
	}else{
		$user=array('user_password'=>md5($user_pass),'mobile'=>$mobile,'sex'=>'3','age'=>'','constellation'=>'保密','created'=>date("Y-m-d H:i:s"));
		$HuanxinObj=Huanxin::getInstance();
		$huserObj=$HuanxinObj->addNewAppUser(strtolower($mobile), md5($user_pass));
		$uuid=$huserObj->entities[0]->uuid;
		if(empty($uuid)){
			echo json_result(null,'101','密码修改失败,请联系客服');
			return;
		}
		$user['uuid']=$uuid;
		$flag=$db->update('user', $user ,array('mobile'=>$mobile,'captcha_code'=>$code));
	}
	echo json_result(array('success'=>$flag));//成功
}

//注册
function register(){
	global $db;
	$data=filter($_REQUEST);
	$mobile=!empty($data['mobile'])?$data['mobile']:'';
	$code=!empty($data['code'])?$data['code']:'';
	$user_pass=!empty($data['user_password'])?$data['user_password']:'';
	if(trim($mobile)==''){
		echo json_result(null,'5','请填写手机号码');//请填写手机号码
		return;
	}
	if(trim($mobile)!=''&&!checkMobile($mobile)){
		echo json_result(null,'6','手机号码不正确');//手机号码不正确
		return;
	}
	
// 	if(trim($email)!=''&&!checkEmail($email)){
// 		echo json_result(null,'4','邮箱格式不正确');
// 		return;
// 	}
	if(trim($user_pass)==''){
		echo json_result(null,'7','请填写密码');//请填写密码
		return;
	}

	if($db->getCount("user",array('mobile'=>$mobile,'captcha_code'=>$code))<=0){
		echo json_result(null,'8','验证码不正确');
		return;
	}
	
	if($db->getCountBySql("select id from ".DB_PREFIX."user where mobile='$mobile' and uuid is not null and uuid <> '' ")>0){
		echo json_result(null,'9','此手机号已经注册过');
		return;
	}
	$user=array('user_password'=>md5($user_pass),'mobile'=>$mobile,'sex'=>'3','age'=>'','constellation'=>'保密','created'=>date("Y-m-d H:i:s"));
	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->addNewAppUser(strtolower($mobile), md5($user_pass));
	$uuid=$huserObj->entities[0]->uuid;
	if(empty($uuid)){
		echo json_result(null,'101','注册失败,请联系客服');
		return;
	}
	$user['uuid']=$uuid;
	$flag=$db->update('user', $user ,array('mobile'=>$mobile,'captcha_code'=>$code));
	echo json_result(array('success'=>$flag));//成功
}

//登录
function login(){
	global $db;
	$data=filter($_REQUEST);
	$user_name=$data['user_name'];
	$user_pass=$data['user_password'];
	if($db->getCount('user',array('user_name'=>$user_name))>0){
		$user=$db->getRow('user',array('user_name'=>$user_name));
	}elseif($db->getCount('user',array('mobile'=>$user_name))>0){
		$user=$db->getRow('user',array('mobile'=>$user_name));
	}elseif($db->getCount('user',array('email'=>$user_name))>0){
		$user=$db->getRow('user',array('email'=>$user_name));
	}else{
		echo json_result(null,'11','帐号不正确');
		return;
	}
	if($user['user_password']!=md5($user_pass)){
		echo json_result(null,'12','密码不正确');
		return;
	}else{
		$info=array();
		$info['userid']=$user['id'];
		$info['user_name']=$user['user_name'];
		$info['sex']=$user['sex'];
		$info['mobile']=$user['mobile'];
		$info['email']=$user['email'];
		$info['head_photo_id']=$user['head_photo_id'];
		$info['allow_find']=$user['allow_find'];
		$info['allow_flow']=$user['allow_flow'];
		if(!empty($info['head_photo_id'])){
			$head=$db->getRow('user_photo',array('id'=>$info['head_photo_id']));
			$info['head_photo']=$head['path'];
		}
		echo json_result($info);
	}
}

//个人信息
function info(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	$myself_id=$data['loginid'];//登陆者id
	$info=$db->getRow('user',array('id'=>$user_id));
	unset($info['user_password']);
	//查询人物关系 当myself_id不为空的时候
	if(!empty($myself_id)){
		$relation_status=getRelationStatus($myself_id,$user_id);
		$info['relation']=$relation_status['relation'];//陌生人
		$info['relation_status']=$relation_status['relation_status'];
		$me=$db->getRow('user',array('id'=>$myself_id));
		$info['distance']=(!empty($me['lat'])&&!empty($me['lng'])&&!empty($info['lat'])&&!empty($info['lng']))?getDistance($info['lat'],$info['lng'],$me['lat'],$me['lng']):lang_UNlOCATE;
		$info['lasttime']=time2Units(time()-strtotime($info['logintime']));
		$info['address']=($info['allow_find']==1)&&!empty($info['lat'])&&!empty($info['lng'])?getAddressFromBaidu($info['lng'],$info['lat']):"未获取到位置";
	}
	if(empty($myself_id)){
		$info['address']=!empty($info['lat'])&&!empty($info['lng'])?getAddressFromBaidu($info['lng'],$info['lat']):"未获取到位置";
	}
	//头像
	if(!empty($info['head_photo_id'])){
		$head=$db->getRow('user_photo',array('id'=>$info['head_photo_id']));
		$info['head_photo']=$head['path'];
	}
	$user_photo=$db->getAll('user_photo',array('user_id'=>$user_id,'isdelete'=>0));
	foreach ($user_photo as $k=>$p){
		$user_photo[$k]['ishead']=($p['id']==$info['head_photo_id'])?1:0;
	}
	$info['user_photos']=$user_photo;
	if(is_array($info)){
		echo json_result($info);
	}else{
		echo json_result(null,'13','信息获取失败');
	}
}

//个人信息修改
function infoEdit(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	$info=array();
	if(!empty($data['sex'])){
		$info['sex']=($data['sex']!=1&&$data['sex']!=2)?3:$data['sex'];
	}
	if(!empty($data['age'])){
		$info['age']=$data['age'];
	}
	if(!empty($data['user_name'])){
		$info['user_name']=$data['user_name'];
		if($db->getCountBySql("select id from ".DB_PREFIX."user where user_name='{$data['user_name']}' and id <> $user_id ")>0){
			echo json_result(null,'15','约我账号已被使用');
			return;
		}
	}
	if(!empty($data['talk'])){
		$info['talk']=$data['talk'];
	}
	if(!empty($data['constellation'])){
		$info['constellation']=$data['constellation'];
	}
	if(!empty($data['nick_name'])){
		$info['nick_name']=$data['nick_name'];
		$info['pinyin']=!empty($info['nick_name'])?getFirstCharter($info['nick_name']):'';
	}
	if(!empty($data['head_photo_id'])){
		$info['head_photo_id']=$data['head_photo_id'];
	}
	if(!empty($data['career'])){
		$info['career']=$data['career'];
	}
	if(!empty($data['signature'])){
		$info['signature']=$data['signature'];
	}
	if(!empty($data['home'])){
		$info['home']=$data['home'];
	}
	if(!empty($data['home_province_id'])){
		$info['home_province_id']=$data['home_province_id'];
	}
	if(!empty($data['home_city_id'])){
		$info['home_city_id']=$data['home_city_id'];
	}
	if(!empty($data['home_town_id'])){
		$info['home_town_id']=$data['home_town_id'];
	}
	if(!empty($data['address'])){
		$info['address']=$data['address'];
	}
	if(!empty($data['interest'])){
		$info['interest']=$data['interest'];
	}
	//经纬度
	$loc_json=file_get_contents("http://api.map.baidu.com/geocoder/v2/?address=".$data['address']."&output=json&ak=".BAIDU_AK);
	$loc=json_decode($loc_json);
	if($loc->status==0){
		$info['ad_lng']=$loc->result->location->lng;
		$info['ad_lat']=$loc->result->location->lat;
	}
	$db->update('user', $info,array('id'=>$user_id));

	//上传相册图片
	$upload=new UpLoad();
	$folder="upload/userPhoto/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user'.$user_id);
	$file=$upload->uploadFiles('photos');//$_File['photo'.$i]
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
		foreach ($file['filepaths'] as $path){
			$photo['path']=APP_SITE.$path;
			$photo['user_id']=$user_id;
			$photo['created']=date("Y-m-d H:i:s");
			$db->create('user_photo', $photo);
		}
	}

	$info=$db->getRow('user',array('id'=>$user_id));
	if(!empty($info['head_photo_id'])){
		$head=$db->getRow('user_photo',array('id'=>$info['head_photo_id']));
		$info['head_photo']=$head['path'];
	}
	unset($info['user_password']);
	echo json_result($info);
}

//相册
function infoImgs(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	$user_photo=$db->getAll('user_photo',array('user_id'=>$user_id,'isdelete'=>0));
	foreach ($user_photo as $k=>$p){
		$user_photo[$k]['ishead']=($p['id']==$info['head_photo_id'])?1:0;
	}
	$info['photos']=$user_photo;
	echo json_result($info);
	
}

//上传多图
function uploadImgs(){
	global $db;
	$user_id=filter($_REQUEST['userid']);
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	//上传相册图片
	$upload=new UpLoad();
	$folder="upload/userPhoto/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user'.$user_id);
	$file=$upload->uploadFiles('photos');//$_File['photo'.$i]
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
		foreach ($file['filepaths'] as $path){
			$photo['path']=APP_SITE.$path;
			$photo['user_id']=$user_id;
			$photo['created']=date("Y-m-d H:i:s");
			$db->create('user_photo', $photo);
		}
	}
	echo json_result(array('userid'=>$user_id));
}

//上传单张图片
function uploadOnceImg(){
	global $db;
	$user_id=filter($_REQUEST['userid']);
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	//上传相册图片
	$upload=new UpLoad();
	$folder="upload/userPhoto/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user'.$user_id);
	$upload->setSHeight(290);
	$upload->setSWidth(290);
	$upload->setLHeight(640);
	$upload->setLWidth(640);
	$file=$upload->upLoadImg('photo');//$_File['photo'.$i]
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
		$photo['path']=APP_SITE.$file['s_path'];
		$photo['user_id']=$user_id;
		$photo['created']=date("Y-m-d H:i:s");
		$photo['id']=$db->create('user_photo', $photo);
		//默认一张图片做头像
		$userinfo=$db->getRow('user',array('id'=>$user_id));
		if(empty($userinfo['head_photo_id'])){
			$db->update('user', array('head_photo_id'=>$photo['id']),array('id'=>$user_id));
		}
	}
	echo json_result($photo);
}

//删除图片
function deleteImg(){
	global $db;
	$user_id=filter($_REQUEST['userid']);
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	$pids=filter($_REQUEST['pid']);
	$pids=explode(",", $pids);
	foreach ($pids as $pid){
		$photo=$db->getRow('user_photo',array('id'=>$pid,'user_id'=>$user_id));
		if(!is_array($photo)){
			echo json_result(null,'38','图片已删除');
			return;
		}
		$path=str_replace(APP_SITE, "", $photo['path']);
		unlink($path);
		$path=str_replace("_s", "_b", $path);
		unlink($path);
		$db->delete('user_photo', array('id'=>$pid,'user_id'=>$user_id));
	}
	echo json_result(array('userid'=>$user_id));
}

//选择头像
function changeHeadImg(){
	global $db;
	$user_id=filter($_REQUEST['userid']);
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	$pid=filter($_REQUEST['pid']);
	$photo=$db->getRow('user_photo',array('id'=>$pid,'user_id'=>$user_id));
	if(!is_array($photo)){
		echo json_result(null,'38','图片已删除');
		return;
	}
	$data=array('head_photo_id'=>$pid);
	$db->update('user', $data,array('id'=>$user_id));
	echo json_result(array('userid'=>$user_id));
	
}

//上传头像
function uploadHeadImg(){
	global $db;
	$user_id=filter($_REQUEST['userid']);
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	//上传相册图片
	$upload=new UpLoad();
	$folder="upload/userPhoto/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user'.$user_id);
	$upload->setSHeight(290);
	$upload->setSWidth(290);
	$upload->setLHeight(640);
	$upload->setLWidth(640);
	$file=$upload->upLoadImg('photo');//$_File['photo'.$i]
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
		$photo['path']=APP_SITE.$file['s_path'];
		$photo['user_id']=$user_id;
		$photo['created']=date("Y-m-d H:i:s");
		$photo['id']=$db->create('user_photo', $photo);
		
		$db->update('user', array('head_photo_id'=>$photo['id']),array('id'=>$user_id));
	}
	echo json_result(array('path'=>$photo['path']));
}

//常住位置经纬度
function getLocation(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	if(empty($user_id)){
		echo json_result(null,'15','找不到用户');
		return;
	}
	$loc=$db->getRow('user',array('id'=>$user_id),array('ad_lng','ad_lat'));
	echo json_result($loc);
}

//当前位置经纬度
function getCurrent(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	if(empty($user_id)){
		echo json_result(null,'16','找不到用户');
		return;
	}
	$loc=$db->getRow('user',array('id'=>$user_id),array('lng','lat'));
	echo json_result($loc);
}

//更新当前位置经纬度
function updateCurrent(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	if(empty($user_id)){
		echo json_result(null,'17','找不到用户');
		return;
	}
	if(empty($data['lng'])){
		echo json_result(null,'18','经度为空');;
		return;
	}
	if(empty($data['lat'])){
		echo json_result(null,'19','纬度为空');;
		return;
	}
	$info['lng']=$data['lng'];
	$info['lat']=$data['lat'];
	$info['logintime']=date("Y-m-d H:i:s");
	$db->update('user',$info,array('id'=>$user_id));
	$address=getAddressFromBaidu($info['lng'],$info['lat']);
	echo json_result(array('address'=>$address));
}

//允许获取经纬度
function allowLngLat(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	$allow=$data['allow'];//1允许2不允许
	if (empty($user_id)){
		echo json_result(null,'17','您还未登录');
		return;
	}
	$db->update('user',array('allow_add'=>$allow),array('id'=>$user_id));
	echo json_result(array('userid'=>$user_id));
	
}
//允许找到我
function allowFind(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	$allow=$data['allow'];//1允许2不允许
	if (empty($user_id)){
		echo json_result(null,'17','您还未登录');
		return;
	}
	$db->update('user',array('allow_find'=>$allow),array('id'=>$user_id));
	echo json_result(array('userid'=>$user_id));
}
//允许关注我
function allowFlow(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	$allow=$data['allow'];//1允许2不允许
	if (empty($user_id)){
		echo json_result(null,'17','您还未登录');
		return;
	}
	$db->update('user',array('allow_flow'=>$allow),array('id'=>$user_id));
	echo json_result(array('userid'=>$user_id));
}
//允许新消息
function allowNews(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	$allow=$data['allow'];//1允许2不允许
	if (empty($user_id)){
		echo json_result(null,'17','您还未登录');
		return;
	}
	$db->update('user',array('allow_news'=>$allow),array('id'=>$user_id));
	echo json_result(array('userid'=>$user_id));
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
			$info['relation']='对方黑名单中';//对方黑名单中
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