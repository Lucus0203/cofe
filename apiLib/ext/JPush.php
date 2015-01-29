<?php
Class JPush {
	var $appkeys;
	var $masterSecret;
	var $authorization;
	var $postUrl;
	
	function __construct(){
		$this->appkeys='acdf50178bfe29414075c7f3';
		$this->masterSecret='329a8a9dc7fba43ef26e6ae3';
		$this->authorization=base64_encode($this->appkeys.':'.$this->masterSecret);
		$this->postUrl='https://api.jpush.cn/v3/push';
	}
	
	/**
	 * 模拟post进行url请求
	 * @param string $param
	 * @return bool|mixed
	 */
	function request_post($param = ''){
		if(empty($param))
		{
			return false;
		}
		$curlPost = json_encode($param);
		$ch       = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $this->postUrl); //抓取指定网页
		curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
		//设置json格式,Authorization: Basic base64_auth_string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type:application/json",'Authorization:Basic '.$this->authorization));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$data = curl_exec($ch); //运行curl
		curl_close($ch);
	
		return $data;
	}
	
	public function jpush($data){
		//platform(必填)目标用户终端手机的平台类型，如： android, ios 多个请使用逗号分隔。
		$param['platform']= 'all';
 		$tag=$data['tag'];//用标签来进行大规模的设备属性、用户属性分群。一次推送最多 20 个。
		
 		//audience推送设备指定
 		$alias=$data['alias'];//咖啡号区别//用别名来标识一个用户。一个设备只能绑定一个别名，但多个设备可以绑定同一个别名。一次推送最多 1000 个。
		$param['audience']=array('alias'=>array($alias));
		//$param['audience']=array('alias'=>array($alias));
		//notification通知内容体。是被推送到客户端的内容。与 message 一起二者必须有其一，可以二者并存
		$content=$data['content'];//发送消息
		$extras=$data['extras'];//返回附加消息一般用户id//"extras" : { "userid" : 321}
		$param['notification']=array(
				//统一的模式--标准模式
				"alert"=>$content,
				//安卓自定义
				"android"=>array(
						"alert"=>$content,
						"title"=>$data['from'],
						"builder_id"=>1
				),
				//ios的自定义
				"ios"=>array(
						"alert"=>$content,
						"badge"=>"1",
						"sound"=>"default"
				)
		);
		//$param['notification']=array('alert'=>$content);
		
		//message应用内消息。或者称作：自定义消息，透传消息。
		$param['message']=array('msg_content'=>$content,'title'=>$data['from']);
		//options推送参数
		//(选填)从消息推送时起，保存离线的时长。秒为单位。最多支持10天（864000秒）。
		// 0 表示该消息不保存离线。即：用户在线马上发出，当前不在线用户将不会收到此消息。
		//此参数不设置则表示默认，默认为保存1天的离线消息（86400秒）。
		$time_to_live = 86400;
		//附加选项
		$param['options'] = array(
				"sendno"=>time(),
				"time_to_live"=>$time_to_live, //保存离线时间的秒数默认为一天
				"apns_production"=>0,        //指定 APNS 通知发送环境：0开发环境，1生产环境。
		);
		
// 		$data = array();
// 		$data['platform'] = 'all';          //目标用户终端手机的平台类型android,ios,winphone
// 		$data['audience'] = array('alias'=>array($alias));      //目标用户
		 
// 		$data['notification'] = array(
// 				//统一的模式--标准模式
// 				"alert"=>$content,
// 				//安卓自定义
// 				"android"=>array(
// 						"alert"=>$content,
// 						"title"=>"",
// 						"builder_id"=>1
// 				),
// 				//ios的自定义
// 				"ios"=>array(
// 						"alert"=>$content,
// 						"badge"=>"1",
// 						"sound"=>"default"
// 				)
// 		);
		
// 		//苹果自定义---为了弹出值方便调测
// 		$data['message'] = array(
// 				"msg_content"=>$content
// 		);
		
// 		//附加选项
// 		$data['options'] = array(
// 				"sendno"=>time(),
// 				"time_to_live"=>$time_to_live, //保存离线时间的秒数默认为一天
// 				"apns_production"=>0,        //指定 APNS 通知发送环境：0开发环境，1生产环境。
// 		);
		//发送请求
		$pushResult = $this->request_post($param);
	
		if($pushResult === false)
		{
			return false;
		}
	
		$pushResult = json_decode($pushResult, true);
	
		$message = array();
		switch(intval($pushResult['error']['code']))
		{
			case 0:
				$message['msg'] = '消息推送成功';
				break;
			case 1000:
				$message['msg'] = '系统内部错误';
				break;
			case 1001:
				$message['msg'] = '只支持 HTTP Post 方法，不支持 Get 方法';
				break;
			case 1002:
				$message['msg'] = '缺少了必须的参数';
				break;
			case 1003:
				$message['msg'] = '参数值不合法';
				break;
			case 1004:
				$message['msg'] = '验证失败';
				break;
			case 1005:
				$message['msg'] = '消息体太大';
				break;
			case 1007:
				$message['msg'] = 'receiver_value 参数 非法';
				break;
			case 1008:
				$message['msg'] = 'appkey参数非法';
				break;
			case 1010:
				$message['msg'] = 'msg_content 不合法';
				break;
			case 1011:
				$message['msg'] = '没有满足条件的推送目标';
				break;
			case 1012:
				$message['msg'] = 'iOS 不支持推送自定义消息。只有 Android 支持推送自定义消息';
				break;
			case 1020:
				$message['msg'] = '只支持 HTTPS 请求';
				break;
			case 1030:
				$message['msg'] = '内部服务超时';
				break;
			default:
				$message=$pushResult;
				break;
		}
	
		return $message;
	}
}

?>