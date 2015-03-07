<?php
define('CLIENTID', 'YXA6QsMZ4HnIEeSoQhVnJpbyzQ');
define('CLIENTSECRET', 'YXA6_WPJ7tDIfsPSljWqdzHae7SAUV0');
Class Huanxin {
	var $_access_token;
	var $_token_file;
	private static $instance;
	private function __construct() {
		$this->_token_file=dirname(__FILE__) . '/access_token.hx';
		$ctime = filectime($this->_token_file);
		$this->_access_token = file_get_contents($this->_token_file);
		if(empty($this->_access_token)||(time() - $ctime)>=60*60*24*5){//五天
			$this->setToken();
		}
	}
	
	private function __clone() {
	}
	
	public static function getInstance() {
		if (! self::$instance instanceof self) {
			self::$instance = new Huanxin;
		}
		return self::$instance;
	}
	
	function setToken(){
		$tokenurl="https://a1.easemob.com/zcsy/coffee/token";
		$parm=array('grant_type'=>'client_credentials','client_id'=>CLIENTID,'client_secret'=>CLIENTSECRET);
		$data=$this->sendJsonDataWithNoToken($tokenurl,json_encode($parm),1);
		$this->_access_token=$data->access_token;
		file_put_contents($this->_token_file, $data->access_token);
	}
	
	function getToken(){
		return $this->_access_token;
	}


	//请求json数据
	function sendJsonDataWithNoToken($url,$parm="",$post=0){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
		curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$jsondata = curl_exec($ch); //运行curl
		curl_close($ch);
		return json_decode($jsondata);
	}

	function getJsonData($url,$parm="",$post=0){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
		curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type:application/json","Authorization: Bearer ".$this->_access_token));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$jsondata = curl_exec($ch); //运行curl
		curl_close($ch);
		return json_decode($jsondata);
	}
	
	//发送json数据
	function sendJsonData($url,$parm="",$post=0){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
		curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type:application/json","Authorization: Bearer ".$this->_access_token));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$jsondata = curl_exec($ch); //运行curl
		curl_close($ch);
		return json_decode($jsondata);
	
	}
	
	//注册
	function addNewAppUser($username,$password){
		$data=array('username'=>$username,'password'=>$password);
		return $this->sendJsonData("https://a1.easemob.com/zcsy/coffee/users",json_encode($data),1);
	}
	//修改密码
	function updatePass($username,$password){
		$data=array('newpassword'=>$password);
		return $this->sendJsonData("https://a1.easemob.com/zcsy/coffee/users/{$username}/password",json_encode($data),1);
	}
	//查找
	function findIMUser($username){
		return $this->getJsonData("https://a1.easemob.com/zcsy/coffee/users/".$username);
	}
	//加入黑名单
	function block($login,$user){
		$data=array('usernames'=>array($user));
		return $this->sendJsonData("https://a1.easemob.com/zcsy/coffee/users/{$login}/blocks/users'",json_encode($data),1);
	}
	//移除黑名单
	function unblock($login,$user){
		return $this->sendJsonData("https://a1.easemob.com/zcsy/coffee/users/{$login}/blocks/users/{$user}'",'','DELETE');
	}
	
}

//$hx=Huanxin::getInstance();
//echo $hx->getToken();