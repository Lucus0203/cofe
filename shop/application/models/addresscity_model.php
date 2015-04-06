<?php
class Addresscity_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
		$this->db->set_dbprefix('cofe_');
	}
	
	public function get_cities($provinceid = FALSE){
		if($provinceid === FALSE){
			$query = $this->db->get('address_city');
		}else{
			$query = $this->db->get_where('address_city',array('province_id'=>$provinceid));
		}
		return $query->result_array();
	}
	
}