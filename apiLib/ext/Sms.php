<?php
class Sms{
	var $_uid;
	var $_pass;
	var $_auth;
	private function __construct() {
		$this->_uid="80265";
		$this->_pass="zcsy123";
		$this->_auth=md5("zcsyzcsy123");
		$ctime = filectime($this->_token_file);
		$this->_access_token = file_get_contents($this->_token_file);
		if(empty($this->_access_token)||(time() - $ctime)>=60*60*24*5){//五天
			$this->setToken();
		}
	}
	
	private function __clone() {
	}
	
	function sendMsg($msg,$mobile){
		$msg="验证码是2718232,来自咖啡约我测试信息";
		$url='http://210.5.158.31/hy?uid='.$this->_uid.'&auth='.$this->_auth.'&mobile='.$mobile.'&msg='.$msg.'&expid=0&encode=utf-8';
		return $this->Get($url);
	}
	
	function Get($url){
		if(function_exists('file_get_contents')){
			$file_contents = file_get_contents($url);
		}else{
			$ch = curl_init();
			$timeout = 5;
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$file_contents = curl_exec($ch);
			curl_close($ch);
		}
		return $file_contents;
	}
}
