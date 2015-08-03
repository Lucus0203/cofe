<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getVer':
		getVer();//获取版本
		break;
	case 'getHotShopCity'://获取热门筛选城市
		getHotShopCity();
		break;
	case 'getShopCity'://获取所有筛选城市
		getShopCity();
		break;
	case 'getShopCityAreaCircle'://获取筛选商圈
		getShopCityAreaCircle();
		break;
	case 'getCountryCityArea':
		getCountryCityArea();//获取全国区域数据
		break;
	default:
		break;
}
//获取版本
function getVer(){
	echo json_result(array('ver'=>'1.0'));
}

//获取热门城市
function getHotShopCity(){
	global $db;
        $hotcity=array('北京','广州','杭州','厦门','大连');
        $hs='';
        foreach ($hotcity as $c) {
            $hs.=" or name='$c'";
        }
        $data=array();
	$sql="select id,name,pinyin,code from ".DB_PREFIX."shop_addcity city where name='上海' {$hs} ";
	$data=$db->getAllBySql($sql);
	echo json_result($data);
}


//筛选城市
function getShopCity(){
	global $db;
        $data=array();
	$sql="select id,name,pinyin,code from ".DB_PREFIX."shop_addcity city where 1=1 ";
	
	$z='a';
	for($i=1;$i<=26;$i++){
		$s=$sql." and pinyin='{$z}' ORDER BY convert(name using gbk) ";
                if($db->getCountBySql($s)>0){
                    $data[$z]=$db->getAllBySql($s);
                }
                ++$z;
	}
        
	echo json_result($data);
}

//获取筛选商圈
function getShopCityAreaCircle(){
	global $db;
	$cityCode=filter(!empty($_REQUEST['cityCode'])?$_REQUEST['cityCode']:'');
        $city=$db->getRow('shop_addcity',array('code'=>$cityCode));
        if(empty($city['id'])){
                echo json_result(null, '1', '抱歉,您的城市数据还在完善中,请定位到其他城市');
        }else{
                $data['city_id']=$city['id'];
                $data['hotarea']=$db->getAll('shop_addcircle',array('city_id'=>$city['id']),array('id as circle_id','name'));//热门商圈
                $area=$db->getAll('shop_addarea',array('city_id'=>$city['id']),array('id as area_id','name'));
                foreach ($area as $k=>$a){
                        $circle=$db->getAll('shop_addcircle',array('area_id'=>$a['area_id']),array('id as circle_id','name'));//区域商圈
                        if(count($circle)>0){
                                $a['circle']=$circle;
                                $data[]=$a;
                        }
                }
                echo json_result($data);
        }
        
}


//获取全国区域数据
function getCountryCityArea($return=false){
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
                                //$pinyinsql="update ".DB_PREFIX."address_city set pinyin='".getFirstCharter($c['name'])."' where id={$c['id']} ";
                                //$db->getAllBySql($pinyinsql);
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
