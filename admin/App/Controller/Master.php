<?php
class Controller_Master extends FLEA_Controller_Action {
	/**
	 *
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_shop;
	var $_shop_bbs;
	var $_shop_menu;
	var $_shop_img;
	var $_admin;
	var $_adminid;
	var $_tags;
	var $_address_city;
	var $_address_province;
	var $_address_town;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_shop = get_singleton ( "Model_Shop" );
		$this->_shop_bbs = get_singleton ( "Model_ShopBbs" );
		$this->_shop_menu = get_singleton ( "Model_ShopMenu" );
		$this->_shop_img = get_singleton ( "Model_ShopImg" );
		$this->_address_city = get_singleton ( "Model_AddressCity" );
		$this->_address_province = get_singleton ( "Model_AddressProvince" );
		$this->_address_town = get_singleton ( "Model_AddressTown" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

	/**
	 * 店家认证
	 *
	 */
	function actionIndex() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$SHOP_PREFIX=$config ['shop_prefix'];
		$PREFIX = $config ['prefix'];
		$pageparm = array ();
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? trim($_GET ['title']) : '';
		$province_id = isset ( $_GET ['province_id'] ) ? $this->_common->filter($_GET ['province_id']) : '';
		$city_id = isset ( $_GET ['city_id'] ) ? $this->_common->filter($_GET ['city_id']) : '';
		$town_id = isset ( $_GET ['town_id'] ) ? $this->_common->filter($_GET ['town_id']) : '';

		$pageparm = array ();
		$conditions = ' 1=1 ';
		if(!empty($title)){
			$conditions.="  (INSTR(title,'".addslashes($title)."') or INSTR(subtitle,'".addslashes($title)."') or INSTR(address,'".addslashes($title)."') ) ";
			$pageparm['title']=$title;
		}
		if(!empty($province_id)){
			$conditions.=" and province_id = $province_id ";
			$pageparm['province_id']=$province_id;
		}
		if(!empty($city_id)){
			$conditions.=" and city_id = $city_id ";
			$pageparm['city_id']=$city_id;
		}
		if(!empty($town_id)){
			$conditions.=" and town_id = $town_id ";
			$pageparm['town_id']=$town_id;
		}
		$sql="select * from ".$SHOP_PREFIX."info where ".$conditions;
		
		$total=$this->_shop->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Master", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		
		$list=$this->_shop->findBySql($sql." order by created desc limit $start,$page_size");
		
		$provinces=$this->_address_province->findAll();
		$prov=$this->_address_province->findByField('id',$province_id);
		$city=$this->_address_city->findAll(array('provinceCode'=>$prov['code']));
		$ctow=$this->_address_city->findByField('id',$city_id);
		$towns=$this->_address_town->findAll(array('cityCode'=>$ctow['code']));
		$this->_common->show ( array ('main' => 'master/index.tpl','list'=>$list,'page'=>$page,'title'=>$title,'province_id'=>$province_id,'city_id'=>$city_id,'town_id'=>$town_id,'provinces'=>$provinces,'city'=>$city,'towns'=>$towns) );
	}
	
	function actionShopInfo(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$SHOP_PREFIX=$config ['shop_prefix'];
		$PREFIX = $config ['prefix'];
		$shopid=isset ( $_GET ['shopid'] ) ? $this->_common->filter($_GET ['shopid']) : '';
		$act=isset ( $_POST ['act'] ) ? $this->_common->filter($_POST ['act']) : '';
		$msg='';
		if(!empty($act)){
			$status=isset ( $_POST ['status'] ) ? $this->_common->filter($_POST ['status']) : '';
			if($status==1){//待审核
				$this->depassShop($shopid);
			}elseif ($status==2){//审核通过
				$this->passShop($shopid);
			}

			$msg='更新成功';
		}
		$sql="select shop.*,p.name as province,c.name as city,t.name as town from ".$SHOP_PREFIX."info shop left join ".$PREFIX."address_province p on p.id=shop.province_id left join ".$PREFIX."address_city c on c.id=shop.city_id left join ".$PREFIX."address_town t on shop.town_id = t.id  where shop.id = $shopid ";
		$data=$this->_shop->findBySql($sql);
		$data=$data[0];
		$shopimgsql="select * from ".$SHOP_PREFIX."img img where img.user_id = {$data['user_id']}";
		$shopimg=$this->_shop->findBySql($shopimgsql);
		$menusql="select * from ".$SHOP_PREFIX."menu menu where menu.user_id = {$data['user_id']}";
		$menu=$this->_shop->findBySql($menusql);

		$mastersql="select * from ".$SHOP_PREFIX."master master where master.user_id = {$data['user_id']}";
		$masterinfo=$this->_shop->findBySql($mastersql);
		$masterinfo=@$masterinfo[0];
		$this->_common->show ( array ('main' => 'master/shop_info.tpl','data'=>$data,'shopimg'=>$shopimg,'menu'=>$menu,'masterinfo'=>$masterinfo,'msg'=>$msg) );
		
	}
	
	function actionPass(){
		$shopid=isset ( $_GET ['shopid'] ) ? $this->_common->filter($_GET ['shopid']) : '';
		if(!empty($shopid)){
			$this->passShop($shopid);
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function actionDePass(){
		$shopid=isset ( $_GET ['shopid'] ) ? $this->_common->filter($_GET ['shopid']) : '';
		if(!empty($shopid)){
			$this->depassShop($shopid);
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function passShop($mastshopid){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$SHOP_PREFIX=$config ['shop_prefix'];
		$PREFIX = $config ['prefix'];
		
		$sql="select * from ".$SHOP_PREFIX."info shop  where shop.id = $mastshopid ";
		$data=$this->_shop->findBySql($sql);
		$data=$data[0];
		$master_userid=$data['user_id'];
		if(empty($data['shop_id'])){
			unset($data['id']);
			unset($data['shop_id']);
			$data['status']=2;
			$shopid=$this->_shop->create($data);
		}else{
			$shopid=$data['id']=$data['shop_id'];
			unset($data['shop_id']);
			$data['status']=2;
			$this->_shop->update($data);
		}
		$updatasql="update ".$SHOP_PREFIX."info set shop_id = $shopid,status=2 where id={$mastshopid} ";
		$this->_shop->execute($updatasql);
		$updatemastersql="update ".$SHOP_PREFIX."master set status=2 where user_id={$master_userid} ";
		$this->_shop->execute($updatemastersql);
		//更新菜单和店铺图片
		$this->_shop_img->removeByConditions(array('shop_id'=>$shopid));
		$this->_shop_menu->removeByConditions(array('shop_id'=>$shopid));
		$imgsql="insert into ".$PREFIX."shop_img (img,shop_id) select img,$shopid from ".$SHOP_PREFIX."img img where img.user_id={$master_userid} ";
		$this->_shop->execute($imgsql);
		$menusql="insert into ".$PREFIX."shop_menu (img,title,shop_id) select img,title,$shopid from ".$SHOP_PREFIX."menu menu where menu.user_id={$master_userid} ";
		$this->_shop->execute($menusql);
		
	}
	
	function depassShop($mastshopid){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$SHOP_PREFIX=$config ['shop_prefix'];
		$PREFIX = $config ['prefix'];

		$sql="select * from ".$SHOP_PREFIX."info shop  where shop.id = $mastshopid ";
		$data=$this->_shop->findBySql($sql);
		$data=$data[0];
		if(empty($data['shop_id'])){
			unset($data['id']);
			unset($data['shop_id']);
			$data['status']=1;
			$shopid=$this->_shop->create($data);
		}else{
			$shopid=$data['id']=$data['shop_id'];
			unset($data['shop_id']);
			$data['status']=1;
			$this->_shop->update($data);
		}
		$updatesql="update ".$SHOP_PREFIX."info set shop_id = $shopid,status = 1 where id={$mastshopid} ";
		$this->_shop->execute($updatesql);
		
		
	}
	
	
}