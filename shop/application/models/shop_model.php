<?php
/**
 * 
 * @author lucus
 * 店铺
 *
 */
class Shop_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
		$this->db->set_dbprefix('shop_');
	}
	
	// 查
	public function get_shop($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'info', $where );
		return $query->row_array ();
	}
	// 增
	public function create_shop($shop) {
		return $this->db->insert ( 'info', $shop );
	}
	// 改
	public function update_shop($shop, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'info', $shop );
	}
	// 删
	public function del_shop($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'info' );
	}
	
}