<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Shop extends CI_Controller {
	var $_tags;
	var $_logininfo;
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'upload' , 'image_lib' ,'imgsizepress'));
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
		$this->load->model ( array (
				'addressprovince_model',
				'addresscity_model',
				'addresstown_model',
				'shop_model',
				'menu_model',
				'menuprice_model',
				'shopimg_model',
				'master_model'
		) );
		
		$this->_tags = array (
				'古典风格',
				'时尚风格',
				'运动主题',
				'萌喵主题',
				'女仆主题',
				'执事主题',
				'嘉宾驻唱',
				'有露天位',
				'购物中心',
				'大屏电影',
				'典雅古镇',
				'特色街道',
				'有景观位',
				'可送外卖',
				'可以刷卡',
				'供应早餐',
				'免费停车',
				'临时办公',
				'临近大学',
				'桌游',
				'书吧',
				'影吧',
				'简餐'
		);

		$this->_logininfo=$this->session->userdata('loginInfo');
		if (empty ( $this->_logininfo )) {
			redirect ( 'login', 'index' );
		}else{
			$this->load->vars(array('loginInfo'=>$this->_logininfo));
		}
	}
	
	public function index() {
		redirect ( 'index.php/index.html' );
	}
	
	public function info() {
		$loginInfo = $this->session->userdata ( 'loginInfo' );
		$act = $this->input->post ( 'act' );
		$msg = '';
		$shopimg = $provinces = $cities = $towns = array ();
		$tags = $this->_tags;
		
		if ($act == 'edit') {
			// 构造shop数据
			$shopinfo = array();
			$lng=$this->input->post ('lng');
			$lat=$this->input->post ('lat');
			// 判断经纬度
			if (empty ( $lng ) || empty ( $lat )) {
				$lng = $this->common->getLngFromBaidu ( $this->input->post ('address') );
				$shopinfo ['lng'] = $lng ['lng'];
				$shopinfo ['lat'] = $lng ['lat'];
			}
			// 特色
			$features=$this->input->post ('features');
			if (! empty ( $features )) {
				$feats = implode ( ",", $features );
				$shopinfo ['feature'] = $feats;
			}
			$shopinfo ['master_id'] = $loginInfo ['id'];
			$shopinfo ['title'] = $this->input->post ('title');
			$shopinfo ['subtitle'] = $this->input->post ('subtitle');
			$shopinfo ['img'] = $this->input->post ('img');
			$shopinfo ['tel'] = $this->input->post ('tel');
			$shopinfo ['hours'] = $this->input->post ('hours');
			$shopinfo ['province_id'] = $this->input->post ('province_id');
			$shopinfo ['city_id'] = $this->input->post ('city_id');
			$shopinfo ['town_id'] = $this->input->post ('town_id');
			$shopinfo ['address'] = $this->input->post ('address');
			$shopinfo ['lng'] = $this->input->post ('lng');
			$shopinfo ['lat'] = $this->input->post ('lat');
			$shopinfo ['introduction'] = $this->input->post ('introduction');
			$shopinfo ['created'] = date("Y-m-d H:i:s");
			$shopinfo ['status'] = $this->input->post ('status');
			if(empty($loginInfo['shop_id'])){//创建店铺数据
				$loginInfo['shop_id']=$this->shop_model->create ( $shopinfo );
				$this->master_model->update ( $loginInfo , $loginInfo['id'] );//更新master的shop_id
				$this->session->set_userdata('loginInfo',$loginInfo);
			}else{
			//更新数据
				$this->shop_model->update ( $shopinfo, $loginInfo ['shop_id'] );
				$msg = "更新成功!";
			}
	 		
		}
		
		$data = $this->shop_model->getRow ( array (
				'master_id' => $loginInfo ['id'] 
		) );
		// 特色标签
		$tags = $this->_tags;
		foreach ( $tags as $k => $t ) {
			$tag = array (
					'tag' => $t,
					'checked' => '' 
			);
			$tags [$k] = $tag;
		}
		
		if(!empty($data)){//如果已有店铺数据
			$shopimg = $this->shopimg_model->getAll ( array (
					'shop_id' => $data ['id'] 
			) );
			// 特色标签
			$feats = explode ( ',', $data ['feature'] );
			$feats = array_flip ( $feats );
			$tags = $this->_tags;
			foreach ( $tags as $k => $t ) {
				$tag = array (
						'tag' => $t,
						'checked' => '' 
				);
				if (array_key_exists ( $t, $feats )) {
					$tag ['checked'] = 'checked';
				}
				$tags [$k] = $tag;
			}
		}
		$data ['province_id'] = empty ( $data ['province_id'] ) ? 9 : $data ['province_id'];
		$data ['city_id'] = empty ( $data ['city_id'] ) ? 75 : $data ['city_id'];
		$cities = $this->addresscity_model->get_cities ( $data ['province_id'] );
		$towns = $this->addresstown_model->get_towns ( $data ['city_id'] );
			
		$provinces = $this->addressprovince_model->get_provinces ();
		
		$res = array (
				'data' => $data,
				'shopimg' => $shopimg,
				'msg' => $msg,
				'tags' => $tags,
				'provinces' => $provinces,
				'cities' => $cities,
				'towns' => $towns 
		);
		
		$this->load->view ( 'header');
		$this->load->view ( 'left' );
		$this->load->view ( 'shop/info', $res );
		$this->load->view ( 'footer' );
	}
	
	
	//ajax上传店铺图片
	public function ajaxUploadShopImg(){
		$logininfo=$this->_logininfo;
		$file=$this->input->post('image-data');
		$img = $this->uploadBase64Img($file,'shop');
		$this->imgsizepress->image_png_size_press($img,$img);//压缩图片
		if(!empty($img)){
			$pp = array (
					'shop_id' => $logininfo['shop_id'],
					'img' => base_url().$img,
					'created' => date ( "Y-m-d H:i:s" ) 
			);
			$id=$this->shopimg_model->create ( $pp );
		}
		$data=array('src'=>base_url().$img,'id'=>$id);
		echo json_encode($data);
	}
	
	// 删除店铺图片
	public function delshopimg() {
		$pid = $this->input->get ( 'pid' );
		$img = $this->shopimg_model->getRow ( array (
				'id' => $pid 
		) );
		$fileurl=str_replace(base_url(), '', $img ['img']);
		if (file_exists ( $fileurl ))
			unlink ( $fileurl );
		$this->shopimg_model->del ( $pid );
		echo 1;
	}
	
	
	
	//上传base64图片文件
	function uploadBase64Img($img,$type='shop'){
		$logininfo=$this->_logininfo;
		// 获取图片
		list($imgtype, $data) = explode(',', $img);
		// 判断类型
		if(strstr($imgtype,'image/jpeg')!==''){
			$ext = '.jpg';
		}elseif(strstr($imgtype,'image/gif')!==''){
			$ext = '.gif';
		}elseif(strstr($imgtype,'image/png')!==''){
			$ext = '.png';
		}
		if($type=='shop'){
			$dir = 'uploads/shop/' . date ( "Ymd" ) . '/';
			if (! file_exists ( $dir )) {
				mkdir ( $dir, 0777 );
			}
			// 生成的文件名
			$filepath = $dir.$logininfo['id'].time().$ext;
			// 生成文件
			if (file_put_contents($filepath, base64_decode($data), true)) {
				// 水印
// 				$confmk ['source_image'] = $filepath;
// 				$confmk ['wm_type'] = 'overlay';
// 				$confmk ['wm_overlay_path'] = './images/watermark.png';
// 				$confmk ['wm_vrt_alignment'] = 'bottom';
// 				$confmk ['wm_hor_alignment'] = 'right';
// 				$confmk ['wm_opacity'] = '50';
// 				//$this->load->library ( 'image_lib', $confmk );
// 				$this->image_lib->initialize($confmk);
// 				$this->image_lib->watermark ();
				return $filepath;
			}else{
				return '';
			}
		}elseif($type=='menu'){
			$dir = 'uploads/shopMenu/' . date ( "Ymd" ) . '/';
			if (! file_exists ( $dir )) {
				mkdir ( $dir, 0777 );
			}
			// 生成的文件名
			$filepath = $dir.$logininfo['id'].time().$ext;
			// 生成文件
			if (file_put_contents($filepath, base64_decode($data), true)) {
// 				$confmk ['source_image'] = $filepath;
// 				$confmk ['wm_type'] = 'overlay';
// 				$confmk ['wm_overlay_path'] = './images/watermark_menu.png';
// 				$confmk ['wm_vrt_alignment'] = 'bottom';
// 				$confmk ['wm_hor_alignment'] = 'right';
// 				$confmk ['wm_opacity'] = '50';
// 				//$this->load->library ( 'image_lib', $confmk );
// 				$this->image_lib->initialize($confmk);
// 				$this->image_lib->watermark ();
				return $filepath;
			}else{
				return '';
			}
		}
		return '';
	}
	
	// 上传店铺图片
	public function uploadShopImg($file) {
		$dir = 'uploads/shop/' . date ( "Ymd" ) . '/';
		if (! file_exists ( $dir )) {
			mkdir ( $dir, 0777 );
		}
		$confimg ['file_name'] = time ();
		$confimg ['upload_path'] = $dir;
		$confimg ['allowed_types'] = 'gif|jpg|png';
		$confimg ['max_width'] = '640';
		$confimg ['max_height'] = '480';
		//$this->load->library ( 'upload', $confimg );
		$this->upload->initialize($confimg);
		if ($this->upload->do_upload ( $file )) {
			$imginfo = $this->upload->data ();
			$filepath = $dir . $imginfo ['file_name'];
			// 水印
			$confmk ['source_image'] = $filepath;
			$confmk ['wm_type'] = 'overlay';
			$confmk ['wm_overlay_path'] = './images/watermark.png';
			$confmk ['wm_vrt_alignment'] = 'bottom';
			$confmk ['wm_hor_alignment'] = 'right';
			$confmk ['wm_opacity'] = '50';
			//$this->load->library ( 'image_lib', $confmk );
			$this->image_lib->initialize($confmk);
			$this->image_lib->watermark ();
				
			return $filepath;
		}
		return '';
		// $this->upload->initialize($config);
	}
	
	// 上传菜单图片
	public function uploadMenuImg($file) {
		$dir = 'uploads/shopMenu/' . date ( "Ymd" ) . '/';
		if (! file_exists ( $dir )) {
			mkdir ( $dir, 0777 );
		}
		$confimg ['file_name'] = time ();
		$confimg ['upload_path'] = $dir;
		$confimg ['allowed_types'] = 'gif|jpg|png';
		$confimg ['max_width'] = '292';
		$confimg ['max_height'] = '233';
		//$this->load->library ( 'upload', $confimg );
		$this->upload->initialize($confimg);
		if ($this->upload->do_upload ( $file )) {
			$imginfo = $this->upload->data ();
			$filepath = $dir . $imginfo ['file_name'];
			// 水印
			$confmk ['source_image'] = $filepath;
			$confmk ['wm_type'] = 'overlay';
			$confmk ['wm_overlay_path'] = './images/watermark_menu.png';
			$confmk ['wm_vrt_alignment'] = 'bottom';
			$confmk ['wm_hor_alignment'] = 'right';
			$confmk ['wm_opacity'] = '50';
			//$this->load->library ( 'image_lib', $confmk );
			$this->image_lib->initialize($confmk);
			$this->image_lib->watermark ();
				
			return $filepath;
		}
		return '';
		// $this->upload->initialize($config);
	}
	
	// 多个文件处理
	function multifile_array($fname) {
		if (count ( $_FILES ) == 0)
			return;
	
		$files = array ();
		$all_files = $_FILES [$fname] ['name'];
		$i = 0;
	
		foreach ( $all_files as $filename ) {
			$files [++ $i] ['name'] = $filename;
			$files [$i] ['type'] = current ( $_FILES [$fname] ['type'] );
			next ( $_FILES [$fname] ['type'] );
			$files [$i] ['tmp_name'] = current ( $_FILES [$fname] ['tmp_name'] );
			next ( $_FILES [$fname] ['tmp_name'] );
			$files [$i] ['error'] = current ( $_FILES [$fname] ['error'] );
			next ( $_FILES [$fname] ['error'] );
			$files [$i] ['size'] = current ( $_FILES [$fname] ['size'] );
			next ( $_FILES [$fname] ['size'] );
		}
	
		return $files;
	}
	
	
	
	
}
