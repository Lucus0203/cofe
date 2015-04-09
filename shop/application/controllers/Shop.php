<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Shop extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		$this->load->model(array('addressprovince_model','addresscity_model','addresstown_model','shop_model'));
		
		$loginInfo=$this->session->userdata('loginInfo');
		if(empty($loginInfo)){
			redirect('login','index');
		}
	}
	
	
	public function info() {
		$loginInfo=$this->session->userdata('loginInfo');
		$id=!empty ( $this->input->get('id') ) ? $this->input->get('id') : '';
		$act=!empty ( $this->input->post('act') ) ? $this->input->post('act') : '';
		$msg='';
// 		if($act=='edit'){
// 			$data=$_POST;
// 			//水印
// 			$ImgWaterMark= & get_singleton ( "Service_ImgWaterMark" );
// 			$waterpath=SERVERROOT.'/resource/images/watermark.png';
// 			$waterpath_menu=SERVERROOT.'/resource/images/watermark_menu.png';
				
// 			$Upload=$this->getUploadObj('shop');
// 			$img=$Upload->upload('file');
// 			if($img['status']==1){
// 				$this->delAppImg($data['img']);
// 				if($data['iswatermark']=='1'){
// 					$path=str_replace(APP_SITE,'../', $img['file_path']);
// 					$ImgWaterMark->imageWaterMark($path,9,$waterpath);
// 				}
// 				$data['img']=$img['file_path'];
// 			}
// 			//判断经纬度
// 			if(empty($data['lng'])||empty($data['lat'])){
// 				$lng=$this->_common->getLngFromBaidu($data['address']);
// 				$data['lng']=$lng['lng'];
// 				$data['lat']=$lng['lat'];
// 			}
// 			//特色
// 			$feats=implode(",", $data['features']);
// 			$data['feature']=$feats;
// 			$this->_shop->update($data);
// 			$this->_shop_img->removeByConditions(array('shop_id'=>$id));
// 			$this->_shop_menu->removeByConditions(array('shop_id'=>$id));
// 			//创建新更多店铺图
		
// 			if(isset($data['shop_oldimg'] )){
// 				foreach ($data['shop_oldimg'] as $mk=>$pub){
// 					$pp=array('shop_id'=>$id,'img'=>$pub);
// 					$this->_shop_img->create($pp);
// 				}
// 			}
// 			$shimgs=$Upload->uploadFiles('shop_img');
// 			if($shimgs['status']==1){
// 				foreach ($shimgs['filepaths'] as $k=>$p){
// 					if($data['iswatermark']=='1'){
// 						$path=str_replace(APP_SITE,'../', $p);
// 						$ImgWaterMark->imageWaterMark($path,9,$waterpath);
// 					}
// 					$pp=array('shop_id'=>$id,'img'=>$p,'created'=>date("Y-m-d H:i:s"));
// 					$this->_shop_img->create($pp);
// 				}
// 			}
// 			//创建新的菜品
// 			if(isset($data['menu_oldimg'] )){
// 				foreach ($data['menu_oldimg'] as $mk=>$pub){
// 					$pp=array('shop_id'=>$id,'title'=>$data['menu_oldtitle'][$mk],'img'=>$pub,'created'=>date("Y-m-d H:i:s"));
// 					$this->_shop_menu->create($pp);
// 				}
// 			}
		
// 			$Upload=$this->getUploadObj('shopMenu');
// 			$files=$Upload->uploadFiles('menu_img');
// 			if($files['status']==1){
// 				foreach ($files['filepaths'] as $k=>$p){
// 					if($data['iswatermark']=='1'){
// 						$path=str_replace(APP_SITE,'../', $p);
// 						$ImgWaterMark->imageWaterMark($path,9,$waterpath_menu);
// 					}
// 					$pp=array('shop_id'=>$id,'title'=>$data['menu_title'][$k],'img'=>$p,'created'=>date("Y-m-d H:i:s"));
// 					$this->_shop_menu->create($pp);
// 				}
// 			}
// 			$msg="更新成功!";
// 		}
		$shop=$this->shop_model->get_shop(array('user_id'=>$loginInfo['id']));
		$menu=$this->_shop_menu->findAll(array('shop_id'=>$id));
		$shopimg=$this->_shop_img->findAll(array('shop_id'=>$id));
		//特色标签
		$feats=explode(',', $data['feature']);
		$feats=array_flip($feats);
		$tags=$this->_tags;
		foreach ($tags as $k=>$t){
			$tag=array('tag'=>$t,'checked'=>'');
			if(array_key_exists($t, $feats)){
				$tag['checked']='checked';
			}
			$tags[$k]=$tag;
		}
		$data['province_id']=empty($data['province_id'])?19:$data['province_id'];
		$data['city_id']=empty($data['city_id'])?200:$data['city_id'];
		$provinces=$this->addressprovince_model->get_provinces();
		$prov=$this->_address_province->findByField('id',$data['province_id']);//广州
		$cities=$this->addresscity_model->get_cities($data['province_id']);
		$ctow=$this->addresstown_model->get_town('id',$data['city_id']);//广州
		$towns=$this->addresstown_model->get_towns($data['city_id']);
		
		$res = array ('data'=>$data,'menu'=>$menu,'shopimg'=>$shopimg,'msg'=>$msg,'tags'=>$tags,'provinces'=>$provinces,'city'=>$city,'towns'=>$towns );
		
		$this->load->view ( 'header' );
		$this->load->view ( 'left' );
		$this->load->view ( 'shop/info' , $res);
		$this->load->view ( 'footer' );
	}
}
