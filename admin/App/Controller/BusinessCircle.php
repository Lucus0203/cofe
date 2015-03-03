<?php
class Controller_BusinessCircle extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_shop;
	var $_admin;
	var $_adminid;
	var $_address_city;
	var $_address_province;
	var $_address_town;
	var $_business_circle;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_shop = get_singleton ( "Model_Shop" );
		$this->_address_city = get_singleton ( "Model_AddressCity" );
		$this->_address_province = get_singleton ( "Model_AddressProvince" );
		$this->_address_town = get_singleton ( "Model_AddressTown" );
		$this->_business_circle = get_singleton ( "Model_BusinessCircle" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	/**
	 * 商圈
	 *
	 */
	function actionIndex() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$province_id = isset ( $_GET ['province_id'] ) ? $this->_common->filter($_GET ['province_id']) : '';
		$city_id = isset ( $_GET ['city_id'] ) ? $this->_common->filter($_GET ['city_id']) : '';

		$pageparm = array ();
		$conditions = ' 1=1 ';
		if(!empty($province_id)){
			$conditions.=" and province.id =$province_id ";
			$pageparm['province_id']=$province_id;
		}
		if(!empty($city_id)){
			$conditions.=" and city.id =$city_id ";
			$pageparm['city_id']=$city_id;
		}
		$sql="select province.name as province,city.name as city,circle.* from ".$prefix."business_circle circle 
			left join ".$prefix."address_province province on circle.province_id=province.id
			left join ".$prefix."address_city city on circle.city_id=city.id where ".$conditions;
		$total=$this->_business_circle->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "BusinessCircle", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_business_circle->findBySql($sql." order by circle.id desc limit $start,$page_size");
		$provinces=$this->_address_province->findAll();
		$prov=$this->_address_province->findByField('id',$province_id);
		$city=$this->_address_city->findAll(array('provinceCode'=>$prov['code']));
		$ctow=$this->_address_city->findByField('id',$city_id);
		$towns=$this->_address_town->findAll(array('cityCode'=>$ctow['code']));
		
		$this->_common->show ( array ('main' => 'businessCircle/list.tpl','list'=>$list,'page'=>$page,'province_id'=>$province_id,'city_id'=>$city_id,'town_id'=>$town_id,'provinces'=>$provinces,'city'=>$city,'towns'=>$towns) );
	}
	

	function actionAdd(){ //添加商圈
		$data=$_POST;
		$data=$this->_business_circle->create($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function actionEdit(){
		$id=$this->_common->filter($_GET['id']);
		if(empty($id)){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$msg='';

		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_business_circle->update($data);
			$msg="更新成功";
		}
		
		$circle=$this->_business_circle->findByField('id',$id);
		
		$provinces=$this->_address_province->findAll();
		$prov=$this->_address_province->findByField('id',$circle['province_id']);
		$city=$this->_address_city->findAll(array('provinceCode'=>$prov['code']));
		
		$this->_common->show ( array ('main' => 'businessCircle/edit.tpl','data'=>$circle,'provinces'=>$provinces,'city'=>$city,'towns'=>$towns,'msg'=>$msg) );
		
	}
	
	function actionDel(){//删除
		$id=$this->_common->filter($_GET['id']);
		$this->_business_circle->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
}

?>