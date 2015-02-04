<?php
require_once APP_DIR.DS.'apiLib'. DS . 'db.php';
$db = db::getInstance ();
/**
 *
 * 过滤参数
 * 
 * @return undefine
 * @author liting
 * @property created at 2012-10-29
 * @property updated at 2012-10-29
 * @example
 *
 */
function filter($value) {
	if (is_array ( $value )) {
		foreach ( $value as $k => $v ) {
			if (is_array ( $v )) {
				foreach ( $v as $kk => $vv ) {
					$v [$kk] = htmlspecialchars ( $vv );
				}
				$value [$k] = $v;
			} else {
				$value [$k] = htmlspecialchars ( $v );
			}
		}
	} else {
		$value = htmlspecialchars ( $value );
	}
	return $value;
}
function checkEmail($value) {
	if (! preg_match ( "/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $value )) {
		return false;
	}
	return true;
}
function checkMobile($value) {
	if (! preg_match ( "/^1(3|4|5|7|8)\d{9}$/", $value )) {
		return false;
	}
	return true;
}

/**
 * 根据两点间的经纬度计算距离
 * 
 * @param float $lat
 *        	纬度值
 * @param float $lng
 *        	经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
	$earthRadius = 6367000; // approximate radius of earth in meters
	
	/*
	 * Convert these degrees to radians
	 * to work with the formula
	 */
	
	$lat1 = ($lat1 * pi ()) / 180;
	$lng1 = ($lng1 * pi ()) / 180;
	
	$lat2 = ($lat2 * pi ()) / 180;
	$lng2 = ($lng2 * pi ()) / 180;
	
	/*
	 * Using the
	 * Haversine formula
	 *
	 * http://en.wikipedia.org/wiki/Haversine_formula
	 *
	 * calculate the distance
	 */
	
	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow ( sin ( $calcLatitude / 2 ), 2 ) + cos ( $lat1 ) * cos ( $lat2 ) * pow ( sin ( $calcLongitude / 2 ), 2 );
	$stepTwo = 2 * asin ( min ( 1, sqrt ( $stepOne ) ) );
	$calculatedDistance = $earthRadius * $stepTwo;
	$res=round ( $calculatedDistance/1000,2 ).'';
	return $res;
}
function noNull($a){
	return is_null($a)?'':$a;
}

function replaceNull($arr){
	if(is_array($arr)&&!empty($arr)){
		foreach ($arr as $k=>$a){
	 		if(is_array($a)&&!empty($a)){
	 			$arr[$k]=replaceNull($a);
	 		}else{
				$arr[$k]=is_null($a)?'':$a;
	 		}
		}
	}
	return $arr;
}

//返回带状态的json对象
function json_result($res,$errCode="1",$errMsg=""){
	$res=replaceNull($res);
	$jsonStr=array('err'=>$errCode,'errMsg'=>$errMsg,'result'=>$res);
	return json_encode($jsonStr);
}

/**
 * 时间差计算
 *
 * @param Timestamp $time 时间差
 * @return String Time Elapsed
 * @author Shelley Shyan
 * @copyright http://phparch.cn (Professional PHP Architecture)
 */
function time2Units ($time)
{
		$year   = floor($time / 60 / 60 / 24 / 365);
		$time  -= $year * 60 * 60 * 24 * 365;
		$month  = floor($time / 60 / 60 / 24 / 30);
		$time  -= $month * 60 * 60 * 24 * 30;
		$week   = floor($time / 60 / 60 / 24 / 7);
		$time  -= $week * 60 * 60 * 24 * 7;
		$day    = floor($time / 60 / 60 / 24);
		$time  -= $day * 60 * 60 * 24;
		$hour   = floor($time / 60 / 60);
		$time  -= $hour * 60 * 60;
		$minute = floor($time / 60);
		$time  -= $minute * 60;
		$second = $time;
		$elapse = '';
		
		$unitArr = array('年前'  =>'year', '个月前'=>'month',  '周前'=>'week', '天前'=>'day',
				'小时前'=>'hour', '分钟前'=>'minute', '秒前'=>'second'
		);
		
		foreach ( $unitArr as $cn => $u )
		{
			if ( $year > 0 ) {//大于一年显示年月日
				$elapse = date('Y/m/d',time()-$time);
				break;
			}
			else if ( $$u > 0 )
			{
				$elapse = $$u . $cn;
				break;
			}
		}
		
		return $elapse;
}

?>
