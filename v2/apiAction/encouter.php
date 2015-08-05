<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Upload.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'deposit'://寄存咖啡
		deposit();
		break;
	case 'nearCafe'://附近邂逅咖啡
		nearCafe();
		break;
	default:
		break;
}

//寄存/等候咖啡
function deposit(){
        global $db;
	$userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$type=filter(!empty($_REQUEST['type'])?$_REQUEST['type']:'');//1爱心2缘分3约会4传递5等候
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	$days=filter(!empty($_REQUEST['days'])?$_REQUEST['days']:'');
	$menuprice1_id=filter(!empty($_REQUEST['menuprice1_id'])?$_REQUEST['menuprice1_id']:'');
	$menuprice2_id=filter(!empty($_REQUEST['menuprice2_id'])?$_REQUEST['menuprice2_id']:'');
	$question=filter(!empty($_REQUEST['question'])?$_REQUEST['question']:'');
	$topic=filter(!empty($_REQUEST['topic'])?$_REQUEST['topic']:'');
	$msg=filter(!empty($_REQUEST['msg'])?$_REQUEST['msg']:'');
        
        $data=array();
        if(empty($userid)){
                echo json_result(null,'2','请您先登录');return ;
        }else{
                $data['user_id']=$userid;
        }
        if(empty($type)){
                echo json_result(null,'3','请选择主题');return ;
        }else{
                $data['type']=$type;
        }
        if(empty($shopid)){
                echo json_result(null,'4','请选择店铺');return ;
        }else{
                $data['shop_id']=$shopid;
        }
        if(empty($days)){
                echo json_result(null,'5','请选择寄存天数');return ;
        }else{
                $data['days']=$days;
        }
        if(empty($menuprice1_id)){
                echo json_result(null,'6','请选择寄存咖啡');return ;
        }else{
                $menuprice=$db->getRow('shop_menu_price',array('id'=>$menuprice1_id));
                if($menuprice['shop_id']!=$shopid){
                        echo json_result(null,'61','您选择的咖啡店发生改变,请重新选择咖啡');return ;
                }
                $data['menuprice1_id']=$menuprice1_id;
                $data['menu1_id']=$menuprice['menu_id'];
                $data['price1']=$menuprice['price'];
                $menu=$db->getRow('shop_menu',array('id'=>$menuprice['menu_id']));
                $data['product1']=$menu['title'];
        }
        switch ($type){
                case 2://缘分咖啡
                        if(empty($question)){
                                echo json_result(null,'7','请输入你想问的问题');return ;
                        }else{
                                $data['question']=$question;
                        }
                        break;
                case 3://约会咖啡
                        if(empty($menuprice2_id)){
                                echo json_result(null,'8','请选择第二杯寄存咖啡');return ;
                        }else{
                                $menuprice=$db->getRow('shop_menu_price',array('id'=>$menuprice2_id));
                                if($menuprice['shop_id']!=$shopid){
                                        echo json_result(null,'81','您选择的咖啡店发生改变,请重新选择咖啡');return ;
                                }
                                $data['menuprice2_id']=$menuprice2_id;
                                $data['menu2_id']=$menuprice['menu_id'];
                                $data['price2']=$menuprice['price'];
                                $menu=$db->getRow('shop_menu',array('id'=>$menuprice['menu_id']));
                                $data['product2']=$menu['title'];
                        }
                        break;
                case 4://传递
                        if(empty($topic)){
                                echo json_result(null,'9','请输入你的话题');return ;
                        }else{
                                $data['topic']=$topic;
                        }
                        break;
                default :
                        break;
        }
        if(empty($msg)){
                echo json_result(null,'10','请输入你的寄语');return ;
        }else{
                $data['msg']=$msg;
        }
        $data['created']=date("Y-m-d H:i:s");
        $encouterid=$db->create('encouter',$data);
        //上传三张图片
        if($type==5){
                $flag=false;
                $upload=new UpLoad();
                $folder="upload/encouterPhoto/";
                if (! file_exists ( $folder )) {
                        mkdir ( $folder, 0777 );
                }
                $upload->setDir($folder.date("Ymd")."/");
                $upload->setPrefixName('user'.$userid);
                $file=$upload->uploadFiles('photos');//$_File['photo'.$i]
                if($file['status']!=0&&$file['status']!=1){
                        echo json_result(null,'701',$file['errMsg']);return;
                }
                if($file['status']==1){
                        foreach ($file['filepaths'] as $path){
                                $photo['img']=APP_SITE.$path;
                                $photo['user_id']=$userid;
                                $photo['encouter_id']=$encouterid;
                                $photo['created']=date("Y-m-d H:i:s");
                                $db->create('encounter_img', $photo);
                                $flag=true;
                        }
                }
                if(!$flag){
                        echo json_result(null,'11','请上传至少一张图片');return;
                }
        }
        
        echo json_result(array('success'=>'TRUE'));
        
}

function nearCafe(){
        global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$city_code=filter($_REQUEST['city_code']);
	$area_id=filter($_REQUEST['area_id']);
	$circle_id=filter($_REQUEST['circel_id']);
	$tag_ids=filter($_REQUEST['tag_ids']);
        $type=filter($_REQUEST['type']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	$sql="select encouter.id,user.head_photo as img,shop.lng,shop.lat "
                . "from ".DB_PREFIX."encouter encouter "
                . "left join ".DB_PREFIX."shop shop on encouter.shop_id=shop.id "
                . "left join ".DB_PREFIX."user user on encouter.user_id=user.id "
                . "left join ".DB_PREFIX."user_tag tag on user.id=tag.user_id where encouter.status=1 group by encouter.user_id ";
        echo $sql;
        if(!empty($city_code)){
                $city=$db->getRow('shop_addcity',array('code'=>$city_code));
                $sql.=(!empty($city['id']))?" and addcity_id={$city['id']} ":'';
        }
        $sql.=(!empty($area_id))?" and addarea_id={$area_id} ":'';
        $sql.=(!empty($circle_id))?" and addcircle_id={$circle_id} ":'';
        $sql.=(!empty($keyword))?" and ( INSTR(title,'".addslashes($keyword)."') or INSTR(subtitle,'".addslashes($keyword)."') or INSTR(address,'".addslashes($keyword)."') ) ":'';
        $sql.=(!empty($tag_ids))?" and shop_tag.tag_id in ({$tag_ids}) ":'';
        
        $sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),id ":' order by id ';
	$sql .= " limit $start,$page_size";
	$shops=$db->getAllBySql($sql);
	foreach ($shops as $k=>$v){
		$shops[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	//echo json_result(array('shops'=>$shops));
	echo json_result($shops);
}