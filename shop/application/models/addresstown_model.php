<?php
class Addresstown_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
		$this->db->set_dbprefix('cofe_');
	}
	
	public function get_towns($cityid = FALSE){
		if($cityid===FALSE){
			$query = $this->db->get('address_town');
		}else{
			$query = $this->db->get_where('address_town',array('city_id'=>$cityid));
		}
		return $query->result_array();
	}
	
}