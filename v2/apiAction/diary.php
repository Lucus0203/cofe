<?php
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Upload.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'addDiary':
		addDiary();//获取版本
		break;
	default:
		break;
}
//获取版本
function addDiary(){
    global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$note=filter(!empty($_REQUEST['note'])?$_REQUEST['note']:'');
	$voice=filter(!empty($_REQUEST['voice'])?$_REQUEST['voice']:'');
	$voice_time=filter(!empty($_REQUEST['voice_time'])?$_REQUEST['voice_time']:'');
	$address=filter(!empty($_REQUEST['address'])?$_REQUEST['address']:'');
	$lng=filter(!empty($_REQUEST['lng'])?$_REQUEST['lng']:'');
	$lat=filter(!empty($_REQUEST['lat'])?$_REQUEST['lat']:'');
        $data=array('user_id'=>$loginid,'note'=>$note,'voice'=>$voice,'voice_time'=>$voice_time,'address'=>$address,'lng'=>$lng,'lat'=>$lat);
        //声音
        $upload = new UpLoad();
        $folder = "upload/voice/";
        if (!file_exists($folder)) {
                mkdir($folder, 0777);
        }
        $upload->setDir($folder . date("Ymd") . "/");
        $upload->setPrefixName('diary' . $loginid);
        $file = $upload->upLoad('voice');
        if ($file['status'] == 4){
            echo json_result(null,'3',$file['errMsg']);
            return ;
        }elseif ($file['status'] == 1) {
            $data['voice']=APP_SITE . $file['file_path'];
        }
        $diary_id=$db->create('diary',$data);
        //相册
        $upload = new UpLoad();
        $folder = "upload/diaryPhoto/";
        if (!file_exists($folder)) {
                mkdir($folder, 0777);
        }
        $upload->setDir($folder . date("Ymd") . "/");
        $upload->setPrefixName('diary' . $loginid);
        $file = $upload->uploadFiles('photos');
        if ($file['status'] == 1) {
                foreach ($file['filepaths'] as $path) {
                        $photo['diary_id'] = $diary_id;
                        $photo['img'] = APP_SITE . $path;
                        $photo['created'] = date("Y-m-d H:i:s");
                        $db->create('diary', $photo);
                }
        }
        echo json_result(array('success'=>'TRUE'));
}