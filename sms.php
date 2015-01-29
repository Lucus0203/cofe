<?php
$sn="zhongchun";
$pass="RU3SGD";
//124.173.70.59
// $url='http://113.215.202.188:8081/SmsAndMms/mg?Sn='.$sn.'&Pwd='.$pass.'&mobile=13918767701&content=验证码是2718232,来自咖啡约我测试信息。';
// $ch = curl_init();
// $timeout = 5;
// curl_setopt ($ch, CURLOPT_URL, $url);
// curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
// $file_contents = curl_exec($ch);
// curl_close($ch);
// print_r($file_contents);

$uid="80265";//代码zcsy
$pass="zcsy123";
$auth=md5("zcsyzcsy123");
$mobile="18521356928";
$msg="验证码是2718232,来自咖啡约我测试信息";
$url='http://210.5.158.31/hy?uid='.$uid.'&auth='.$auth.'&mobile='.$mobile.'&msg='.$msg.'&expid=0&encode=utf-8';

function Get($url)
{
	if(function_exists('file_get_contents'))
	{
		$file_contents = file_get_contents($url);
	}
	else
	{
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
echo Get($url);
