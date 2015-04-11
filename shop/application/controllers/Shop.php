<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Shop extends CI_Controller {

	var $_tags;
	
	function __construct(){
		parent::__construct();
		$this->load->library('session','common');
		$this->load->helper(array('form','url','path'));
		$this->load->model(array('addressprovince_model','addresscity_model','addresstown_model','shop_model','menu_model','shopimg_model'));

		$this->_tags=array('休闲小憩','情侣约会','随便吃吃','朋友聚餐','可以刷卡','有下午茶',
				'家庭聚会','无线上网','供应早餐','有露天位','免费停车','有无烟区',
				'可送外卖','有景观位','是老字号','商务宴请','生日聚会','节目表演');
		
		$loginInfo=$this->session->userdata('loginInfo');
		if(empty($loginInfo)){
			redirect('login','index');
		}
	}
	
	public function index(){
		redirect('index.php/index.html');
	}
	
	public function info() {
		$this->db->set_dbprefix('shop_');
		$loginInfo=$this->session->userdata('loginInfo');
		$id=$this->input->get('id') ;
		$act=$this->input->post('act');
		$msg='';
		$shopimg=$menu=$provinces=$cities=$towns=array();
		$tags=$this->_tags;
		
		if($act=='edit'){
			$data=$this->input->post();
			//上传
			$dir='uploads/shop/'.date("Ymd").'/';
			if (! file_exists ( $dir )) {
				mkdir ( $dir, 0777 );
			}
			$confimg['file_name'] = time ();
			$confimg['upload_path'] = $dir;
			$confimg['allowed_types'] = 'gif|jpg|png';
			$confimg['max_width'] = '640';
			$confimg['max_height'] = '345';
			$this->load->library('upload', $confimg);
			if ( $this->upload->do_upload('file') ){
				$imginfo = $this->upload->data();
				$filepath=$dir.$imginfo['file_name'];
				//水印
				$confmk['source_image'] = $filepath;
				$confmk['wm_type'] = 'overlay';
				$confmk['wm_overlay_path'] = './images/watermark.png';
				$confmk['wm_vrt_alignment'] = 'bottom';
				$confmk['wm_hor_alignment'] = 'right';
				$confmk['wm_opacity'] = '50';
				$this->load->library('image_lib', $confmk);
				$this->image_lib->watermark();
				
				$data['img']=$filepath;
			}
			//$this->upload->initialize($config);
			//判断经纬度
			if(empty($data['lng'])||empty($data['lat'])){
				$lng=$this->common->getLngFromBaidu($data['address']);
				$data['lng']=$lng['lng'];
				$data['lat']=$lng['lat'];
			}
			//特色
			$feats=implode(",", $data['features']);
			$data['feature']=$feats;
			
			$shopinfo=$data;
			$shopinfo['user_id']=$loginInfo['id'];
			unset($shopinfo['act']);
			unset($shopinfo['shop_img']);
			unset($shopinfo['shop_oldimg']);
			unset($shopinfo['features']);
			unset($shopinfo['menu_oldimg']);
			unset($shopinfo['menu_oldtitle']);
			unset($shopinfo['menu_img']);
			unset($shopinfo['menu_title']);
			unset($shopinfo['iswatermark']);
			if(empty($shopinfo['id'])){
				unset($shopinfo['id']);
				$this->shop_model->create($shopinfo);
			}else{
				$this->shop_model->update($shopinfo,$shopinfo['id']);
			}
			
			exit();
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
		
		$data=$this->shop_model->getRow(array('user_id'=>$loginInfo['id']));
		//特色标签
		$tags=$this->_tags;
		foreach ($tags as $k=>$t){
			$tag=array('tag'=>$t,'checked'=>'');
			$tags[$k]=$tag;
		}
		
		//如果已填写店铺数据
		if(!empty($data)){
			$menu=$this->menu_model->getAll(array('shop_id'=>$data['id']));
			$shopimg=$this->shopimg_model->getAll(array('shop_id'=>$id));
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
			$this->db->set_dbprefix('cofe_');
			$cities=$this->addresscity_model->get_cities($data['province_id']);
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
