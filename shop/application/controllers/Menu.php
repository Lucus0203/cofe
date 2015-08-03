<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Menu extends CI_Controller {
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
				'shopimg_model' 
		) );

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
	
	/**
	 *咖啡甜品
	 *
	 **/
	public function menu() {
		$loginInfo = $this->session->userdata ( 'loginInfo' );
		$msg = '';
		$menu = array ();
		
		// 读取菜单信息
		$menu = $this->menu_model->getAll ( array (
				'shop_id' => $loginInfo ['shop_id']
		) );
		foreach ($menu as $k=>$m){
			$menu[$k]['prices']=$this->menuprice_model->getAll(array('menu_id'=>$m['id']));
		}
		$res = array (
				'menu' => $menu
		);
		$this->load->view ( 'header');
		$this->load->view ( 'left' );
		$this->load->view ( 'shop/menu', $res );
		$this->load->view ( 'footer' );
	}
	
	//ajax上传菜品
	public function ajaxUploadShopMenu(){
		$logininfo=$this->_logininfo;
		$file=$this->input->post('image-data');
		$title=$this->input->post('title');
		$img = $this->uploadBase64Img($file,'menu');
		$this->imgsizepress->image_png_size_press($img,$img);//压缩图片
		if(!empty($img)){
			$pp = array (
					'shop_id' => $logininfo['shop_id'],
					'title' => $title,
					'img' => base_url().$img,
					'status'=> 1,
					'created' => date ( "Y-m-d H:i:s" ) 
			);
			$id=$this->menu_model->create ( $pp );
		}
		$data=array('src'=>base_url().$img,'id'=>$id,'title'=>$title);
		echo json_encode($data);
	}
	
	// 删除菜品
	public function delmenu() {
		$pid = $this->input->get ( 'pid' );
// 		$img = $this->menu_model->getRow ( array (
// 				'id' => $pid 
// 		) );
// 		$fileurl=str_replace(base_url(), '', $img ['img']);
// 		if (file_exists ( $fileurl ))
// 			unlink ( $fileurl );
		$this->menu_model->del ( $pid );
		echo 1;
	}
	


	/**
	 *菜品价格更新
	 */
	public function menuPriceUpdate(){
		$menuid=$this->input->post('menuid');
		$prices=$this->input->post('prices');
		$prices=explode(',' , $prices);
		$typies=$this->input->post('typies');
		$typies=explode(',' , $typies);
	
		$menu = $this->menu_model->getRow ( array (
				'id' => $menuid
		) );
		$loginInfo = $this->session->userdata ( 'loginInfo' );
		if($menu['shop_id']==$loginInfo ['shop_id'] ){//属于本人的菜品
			$this->menuprice_model->delByCond(array('menu_id'=>$menuid));
			foreach ($prices as $k=>$p){
				$mp=array('shop_id'=>$menu['shop_id'],'menu_id'=>$menuid,'price'=>$p,'type'=>$typies[$k]);
				$this->menuprice_model->create($mp);
			}
			echo 1;
		}else{
			echo '0';//需要重新登录
		}
	}
	
	/**
	 * 菜品上下架
	 */
	public function menuPublic(){
		$menuid=$this->input->post('menuid');
		$public=$this->input->post('public');//1待售,2寄售中
		$menu = $this->menu_model->getRow ( array (
				'id' => $menuid
		) );
		$loginInfo = $this->session->userdata ( 'loginInfo' );
		if($menu['shop_id']==$loginInfo ['shop_id'] ){//属于本人的菜品
			$this->menu_model->update(array('status'=>$public), $menuid);
			echo 1;
		}else{
			echo '0';//需要重新登录
		}
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
	
	
	
	
}
