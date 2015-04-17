<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getCityArea':
		getCityArea();//获取全国区域数据
		break;
	case 'getShopCityArea':
		getShopCityArea();//获取店铺区域数据
		break;
	case 'getBusinessCircle':
		getBusinessCircle();//获取商圈
		break;
	case 'getBusinessShopCircle':
		getBusinessShopCircle();//获取商圈1.1
		break;
	case 'getInvitationTitle':
		getInvitationTitle();//邀请函主题数据
	default:
		break;
}

//获取全国区域数据
function getCityArea($return=false){
	global $db;
	$areafile=APP_DIR. '/upload/city_area.db';
	$ctime = filectime($areafile);
	$areadata = file_get_contents($areafile);
	if(empty($areadata)||(time() - $ctime)>=60*60*24*5){//五天
		$sql="select p.id,p.name from ".DB_PREFIX."address_province p ";
		$province=$db->getAllBySql($sql);
		foreach ($province as $pk=>$p){
			$sql="select c.id,c.name from ".DB_PREFIX."address_city c where c.province_id = {$p['id']} order by code asc ";
			$city=$db->getAllBySql($sql);
			foreach ($city as $ck=>$c){
				$sql="select t.id,t.name from ".DB_PREFIX."address_town t where t.city_id = {$c['id']} order by code asc ";
				$town=$db->getAllBySql($sql);
				$city[$ck]['town']=$town;
			}
			$province[$pk]['city']=$city;
		}
		$res['province']=$province;
		$areadata=json_result($res);
		file_put_contents($areafile, $areadata);
	}
	
	if(!$return){
		echo $areadata;
	}else{
		return $areadata;
	}
}

//获取区域数据
function getShopCityArea($return=false){
	global $db;
	//0.135
// 	$sql1=" select province_id,city_id,town_id from ".DB_PREFIX."shop shop group by shop.province_id,shop.city_id,shop.town_id ";
// 	$sql2="select p.id as province_id,p.name as province,c.id as city_id,c.name as city,t.id as town_id,t.name as town from ".DB_PREFIX."address_province p left join ".DB_PREFIX."address_city c on c.province_id=p.id left join ".DB_PREFIX."address_town t on t.city_id=c.id";
// 	$sql="select s2.* from ($sql1) s1 inner join ($sql2) s2 on s1.province_id=s2.province_id and s1.city_id=s2.city_id and s1.town_id = s2.town_id ";
// 	echo $sql;
// 	echo time();
// 	$db->getAllBySql($sql);
	$sql="select p.id,p.name from ".DB_PREFIX."address_province p inner join ".DB_PREFIX."shop shop on shop.province_id = p.id group by shop.province_id ";
	$province=$db->getAllBySql($sql);
	foreach ($province as $pk=>$p){
		$sql="select c.id,c.name from ".DB_PREFIX."address_city c inner join ".DB_PREFIX."shop shop on shop.city_id = c.id where c.province_id = ".$p['id']." group by shop.city_id ";
		$city=$db->getAllBySql($sql);
		foreach ($city as $ck=>$c){
			$sql="select t.id,t.name from ".DB_PREFIX."address_town t inner join ".DB_PREFIX."shop shop on shop.town_id = t.id where t.city_id = ".$c['id']." group by shop.town_id";
			$town=$db->getAllBySql($sql);
			$city[$ck]['town']=$town;
		}
		$province[$pk]['city']=$city;
	}
	$res['province']=$province;
	if(!$return){
		echo json_result($res);
	}else{
		return $res;
	}
}
//获取商圈
function getBusinessCircle($return=false){
	global $db;
	$sql="select p.id,p.name from ".DB_PREFIX."address_province p inner join ".DB_PREFIX."business_circle circle on circle.province_id = p.id group by circle.province_id ";
	$province=$db->getAllBySql($sql);
	foreach ($province as $pk=>$p){
		$sql="select c.id,c.name from ".DB_PREFIX."address_city c inner join ".DB_PREFIX."business_circle circle on circle.city_id = c.id where c.province_id = ".$p['id']." group by circle.city_id ";
		$city=$db->getAllBySql($sql);
		foreach ($city as $ck=>$c){
			//$sql="select t.id,t.name from ".DB_PREFIX."address_town t inner join ".DB_PREFIX."business_circle circle on circle.town_id = t.id where t.city_id = ".$c['id']." group by circle.town_id";
			//$town=$db->getAllBySql($sql);
			//foreach ($town as $tk=>$t){
				$sql="select id,name,lng,lat from ".DB_PREFIX."business_circle circle where circle.city_id = {$c['id']}";
				$circle=$db->getAllBySql($sql);
				//$town[$tk]['circle']=$circle;
				$city[$ck]['circle']=$circle;
			//}
			//$city[$ck]['town']=$town;
		}
		$province[$pk]['city']=$city;
	}
	$res['province']=$province;
	if(!$return){
		echo json_result($res);
	}else{
		return $res;
	}
}

//获取商圈1.1
function getBusinessShopCircle($return=false){
	global $db;
	$sql="select p.id,p.name from ".DB_PREFIX."address_province p inner join ".DB_PREFIX."business_circle circle on circle.province_id = p.id group by circle.province_id ";
	$province=$db->getAllBySql($sql);
	foreach ($province as $pk=>$p){
		$sql="select c.id,c.name from ".DB_PREFIX."address_city c inner join ".DB_PREFIX."business_circle circle on circle.city_id = c.id where c.province_id = ".$p['id']." group by circle.city_id ";
		$city=$db->getAllBySql($sql);
		foreach ($city as $ck=>$c){
			$sql="select t.id,t.name from ".DB_PREFIX."address_town t inner join ".DB_PREFIX."business_circle circle on circle.town_id = t.id where t.city_id = ".$c['id']." group by circle.town_id";
			$town=$db->getAllBySql($sql);
			foreach ($town as $tk=>$t){
				$sql="select id,name,lng,lat from ".DB_PREFIX."business_circle circle where circle.city_id = {$t['city_id']}";
				$circle=$db->getAllBySql($sql);
				$town[$tk]['circle']=$circle;
				//$city[$ck]['circle']=$circle;
			}
			$city[$ck]['town']=$town;
		}
		$province[$pk]['city']=$city;
	}
	$res['province']=$province;
	if(!$return){
		echo json_result($res);
	}else{
		return $res;
	}
}

//邀请函主题数据
function getInvitationTitle(){
	global $db;
	$data=$db->getAll('invitation_title',array(),array('name'));
	echo json_result($data);
}