<?php
class User_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
	public function get_user($user = FALSE) {
		if ($user === FALSE) {
			return array();
		}
		
		$query = $this->db->get_where ( 'user', array (
				'user_name' => $user 
		) );
		return $query->row_array ();
	}
	
}