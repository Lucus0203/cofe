<?php
/**
 * 
 * @author lucus
 * 店家用户
 *
 */
class User_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
		$this->db->set_dbprefix('shop_');
	}
	
	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'user', $where );
		return $query->row_array ();
	}
	// 增
	public function create($user) {
		return $this->db->insert ( 'user', $user );
	}
	// 改
	public function update($user, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'user', $user );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'user' );
	}
	
}