<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Shop extends CI_Controller {

	var $_tags;
	
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		$this->load->model(array('addressprovince_model','addresscity_model','addresstown_model','shop_model','menu_model','shopimg_model'));

		$this->_tags=array('休闲小憩','情侣约会','随便吃吃','朋友聚餐','可以刷卡','有下午茶',
				'家庭聚会','无线上网','供应早餐','有露天位','免费停车','有无烟区',
				'可送外卖','有景观位','是老字号','商务宴请','生日聚会','节目表演');
		
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
		$shopimg=$menu=$provinces=$cities=$towns=array();
		$tags=$this->_tags;
		
		if($act=='edit'){
			$data=$_POST;
			//水印
			$ImgWaterMark= $this->ImgWaterMark;
			$waterpath=base_url().'images/watermark.png';
			$waterpath_menu=base_url().'images/watermark_menu.png';
				
			$Upload=$this->getUploadObj('shop');
			$img=$Upload->upload('file');
			if($img['status']==1){
				$this->delAppImg($data['img']);
				if($data['iswatermark']=='1'){
					$path=str_replace(APP_SITE,'../', $img['file_path']);
					$ImgWaterMark->imageWaterMark($path,9,$waterpath);
				}
				$data['img']=$img['file_path'];
			}
			//判断经纬度
			if(empty($data['lng'])||empty($data['lat'])){
				$lng=$this->_common->getLngFromBaidu($data['address']);
				$data['lng']=$lng['lng'];
				$data['lat']=$lng['lat'];
			}
			//特色
			$feats=implode(",", $data['features']);
			$data['feature']=$feats;
			$this->_shop->update($data);
			$this->_shop_img->removeByConditions(array('shop_id'=>$id));
			$this->_shop_menu->removeByConditions(array('shop_id'=>$id));
			//创建新更多店铺图
		
			if(isset($data['shop_oldimg'] )){
				foreach ($data['shop_oldimg'] as $mk=>$pub){
					$pp=array('shop_id'=>$id,'img'=>$pub);
					$this->_shop_img->create($pp);
				}
			}
			$shimgs=$Upload->uploadFiles('shop_img');
			if($shimgs['status']==1){
				foreach ($shimgs['filepaths'] as $k=>$p){
					if($data['iswatermark']=='1'){
						$path=str_replace(APP_SITE,'../', $p);
						$ImgWaterMark->imageWaterMark($path,9,$waterpath);
					}
					$pp=array('shop_id'=>$id,'img'=>$p,'created'=>date("Y-m-d H:i:s"));
					$this->_shop_img->create($pp);
				}
			}
			//创建新的菜品
			if(isset($data['menu_oldimg'] )){
				foreach ($data['menu_oldimg'] as $mk=>$pub){
					$pp=array('shop_id'=>$id,'title'=>$data['menu_oldtitle'][$mk],'img'=>$pub,'created'=>date("Y-m-d H:i:s"));
					$this->_shop_menu->create($pp);
				}
			}
		
			$Upload=$this->getUploadObj('shopMenu');
			$files=$Upload->uploadFiles('menu_img');
			if($files['status']==1){
				foreach ($files['filepaths'] as $k=>$p){
					if($data['iswatermark']=='1'){
						$path=str_replace(APP_SITE,'../', $p);
						$ImgWaterMark->imageWaterMark($path,9,$waterpath_menu);
					}
					$pp=array('shop_id'=>$id,'title'=>$data['menu_title'][$k],'img'=>$p,'created'=>date("Y-m-d H:i:s"));
					$this->_shop_menu->create($pp);
				}
			}
			$msg="更新成功!";
		}
		
		$this->db->set_dbprefix('shop_');
		$data=$this->shop_model->getRow(array('user_id'=>$loginInfo['id']));
		//特色标签
		$tags=$this->_tags;
		foreach ($tags as $k=>$t){
			$tag=array('tag'=>$t,'checked'=>'');
			$tags[$k]=$tag;
		}
		if(!empty($data)){
			$menu=$this->menu_model->findAll(array('shop_id'=>$data['id']));
			$shopimg=$this->shopimg_model->findAll(array('shop_id'=>$id));
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
			$this->db->set_dbprefix('coffee_');
			$prov=$this->_address_province->findByField('id',$data['province_id']);
			$cities=$this->addresscity_model->get_cities($data['province_id']);
			$ctow=$this->addresstown_model->get_town('id',$data['city_id']);
			$towns=$this->addresstown_model->get_towns($data['city_id']);
		}
		$this->db->set_dbprefix('cofe_');
		$provinces=$this->addressprovince_model->get_provinces();
		
		
		
		$res = array ('data'=>$data,'menu'=>$menu,'shopimg'=>$shopimg,'msg'=>$msg,'tags'=>$tags,'provinces'=>$provinces,'cities'=>$cities,'towns'=>$towns );
		
		$this->load->view ( 'header' );
		$this->load->view ( 'left' );
		$this->load->view ( 'shop/info' , $res);
		$this->load->view ( 'footer' );
	}
}
