<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Shop extends CI_Controller {
	var $_tags;
	var $_logininfo;
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'upload' , 'image_lib'));
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
				'shopimg_model' 
		) );
		
		$this->_tags = array (
				'休闲小憩',
				'情侣约会',
				'随便吃吃',
				'朋友聚餐',
				'可以刷卡',
				'有下午茶',
				'家庭聚会',
				'无线上网',
				'供应早餐',
				'有露天位',
				'免费停车',
				'有无烟区',
				'可送外卖',
				'有景观位',
				'是老字号',
				'商务宴请',
				'生日聚会',
				'节目表演' 
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
		$this->db->set_dbprefix ( 'shop_' );
		$loginInfo = $this->session->userdata ( 'loginInfo' );
		$act = $this->input->post ( 'act' );
		$msg = '';
		$shopimg = $menu = $provinces = $cities = $towns = array ();
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
			$shopinfo ['user_id'] = $loginInfo ['id'];
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
			
			//更新数据
			$this->shop_model->update ( $shopinfo, $shopinfo ['user_id'] );
			// 创建新更多店铺图
// 			if (isset ( $data ['shop_oldimg'] )) {
// 				foreach ( $data ['shop_oldimg'] as $mk => $pub ) {
// 					$pp = array (
// 							'shop_id' => $shopid,
// 							'img' => $pub 
// 					);
// 					$this->shopimg_model->create ( $pp );
// 				}
// 			}
// 			// 创建更多菜单
// 			if (isset ( $data ['menu_oldimg'] )) {
// 				foreach ( $data ['menu_oldimg'] as $mk => $pub ) {
// 					$pp = array (
// 							'shop_id' => $shopid,
// 							'title' => $data ['menu_oldtitle'] [$mk],
// 							'img' => $pub,
// 							'created' => date ( "Y-m-d H:i:s" ) 
// 					);
// 					$this->menu_model->create ( $pp );
// 				}
// 			}
// 			//更新图片
// 			$shop_img_files=$this->multifile_array ( 'shop_img' ); // 多图结构化$_FILES
// 			$menu_img_files=$this->multifile_array ( 'menu_img' ); // 多图结构化$_FILES
// 			$_FILES=$shop_img_files;
// 			foreach ( $_FILES as $file => $file_data ) {
// 				$path = $this->uploadShopImg ( $file );
// 				if(!empty($path)){
// 					$pp = array (
// 							'shop_id' => $shopid,
// 							'img' => $path,
// 							'created' => date ( "Y-m-d H:i:s" ) 
// 					);
// 					$this->shopimg_model->create ( $pp );
// 				}
// 			}
// 			$_FILES=$menu_img_files;
// 			$k = 0;
// 			foreach ( $_FILES as $file => $file_data ) {
// 				$path = $this->uploadMenuImg ( $file );
// 				if(!empty($path)){
// 					$pp = array (
// 							'shop_id' => $shopid,
// 							'title' => $data ['menu_title'] [$k ++],
// 							'img' => $path,
// 							'created' => date ( "Y-m-d H:i:s" ) 
// 					);
// 					$this->menu_model->create ( $pp );
// 				}
// 			}
			$msg = "更新成功!";
		}
		
		$data = $this->shop_model->getRow ( array (
				'user_id' => $loginInfo ['id'] 
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
		
		// 读取店铺信息
		$menu = $this->menu_model->getAll ( array (
				'user_id' => $loginInfo ['id'] 
		) );
		$shopimg = $this->shopimg_model->getAll ( array (
				'user_id' => $loginInfo ['id'] 
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
		$data ['province_id'] = empty ( $data ['province_id'] ) ? 19 : $data ['province_id'];
		$data ['city_id'] = empty ( $data ['city_id'] ) ? 200 : $data ['city_id'];
		$this->db->set_dbprefix ( 'cofe_' );
		$cities = $this->addresscity_model->get_cities ( $data ['province_id'] );
		$towns = $this->addresstown_model->get_towns ( $data ['city_id'] );
			
		$this->db->set_dbprefix ( 'cofe_' );
		$provinces = $this->addressprovince_model->get_provinces ();
		
		$res = array (
				'data' => $data,
				'menu' => $menu,
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
		if(!empty($img)){
			$pp = array (
					'user_id' => $logininfo['id'],
					'img' => $img,
					'created' => date ( "Y-m-d H:i:s" ) 
			);
			$id=$this->shopimg_model->create ( $pp );
		}
		$data=array('src'=>$img,'id'=>$id);
		echo json_encode($data);
	}
	
	//ajax上传菜品图片
	public function ajaxUploadShopMenu(){
		$logininfo=$this->_logininfo;
		$file=$this->input->post('image-data');
		$title=$this->input->post('title');
		$img = $this->uploadBase64Img($file,'menu');
		if(!empty($img)){
			$pp = array (
					'user_id' => $logininfo['id'],
					'title' => $title,
					'img' => $img,
					'created' => date ( "Y-m-d H:i:s" ) 
			);
			$id=$this->menu_model->create ( $pp );
		}
		$data=array('src'=>$img,'id'=>$id,'title'=>$title);
		echo json_encode($data);
	}
	
	// 删除店铺图片
	public function delshopimg() {
		$pid = $this->input->get ( 'pid' );
		$img = $this->shopimg_model->getRow ( array (
				'id' => $pid 
		) );
		if (file_exists ( $img ['img'] ))
			unlink ( $img ['img'] );
		$this->shopimg_model->del ( $pid );
		echo 1;
	}
	
	// 删除菜品
	public function delmenu() {
		$pid = $this->input->get ( 'pid' );
		$img = $this->menu_model->getRow ( array (
				'id' => $pid 
		) );
		if (file_exists ( $img ['img'] ))
			unlink ( $img ['img'] );
		$this->menu_model->del ( $pid );
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
	

	//店铺认领
	public function claim(){
		$this->db->set_dbprefix ( 'shop_' );
		$loginInfo = $this->session->userdata ( 'loginInfo' );
		
		$pageparm = array ();
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? trim($_GET ['title']) : '';
		$province_id = isset ( $_GET ['province_id'] ) ? $this->_common->filter($_GET ['province_id']) : '';
		$city_id = isset ( $_GET ['city_id'] ) ? $this->_common->filter($_GET ['city_id']) : '';
		$town_id = isset ( $_GET ['town_id'] ) ? $this->_common->filter($_GET ['town_id']) : '';

		$conditions=array();
		if(!empty($title)){
			$conditions[]=" (INSTR(title,'".addslashes($title)."') or INSTR(subtitle,'".addslashes($title)."') or INSTR(address,'".addslashes($title)."') )";
			$pageparm['title']=$title;
		}
		if(!empty($province_id)){
			$conditions['province_id']=$province_id;
			$pageparm['province_id']=$province_id;
		}
		if(!empty($city_id)){
			$conditions['city_id']=$city_id;
			$pageparm['city_id']=$city_id;
		}
		if(!empty($town_id)){
			$conditions['town_id']=$town_id;
			$pageparm['town_id']=$town_id;
		}

		$total=$this->_shop->findCount($conditions);

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Shop", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_shop->findAll($conditions," recommend,id desc limit $start,$page_size");

		
		$provinces=$this->_address_province->findAll();
		$prov=$this->_address_province->findByField('id',$province_id);
		$city=$this->_address_city->findAll(array('provinceCode'=>$prov['code']));
		$ctow=$this->_address_city->findByField('id',$city_id);
		$towns=$this->_address_town->findAll(array('cityCode'=>$ctow['code']));
		
		$res = array ('data'=>$data,'msg'=>$msg);
	
		$this->load->view ( 'header');
		$this->load->view ( 'left' );
		$this->load->view ( 'shop/claim', $res );
		$this->load->view ( 'footer' );
	}
	
	
	
}
