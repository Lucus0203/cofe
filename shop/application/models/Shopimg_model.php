<?php
/**
 * 
 * @author lucus
 * 店铺图片
 *
 */
class Shopimg_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
		$this->db->set_dbprefix('shop_');
	}
	
	//查所有
	public function getAll($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'img', $where );
		return $query->result_array ();
	}
	
	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'img', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'img', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'img', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'img' );
	}
	//移除所有相关图片
	public function delByCond($where){
		$this->db->where ( $where );
		$this->db->delete ( 'img' );
	}
	
}