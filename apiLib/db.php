<?php
require_once 'apiLib/config.php';
class db {
	private $conn;
	private static $sql;
	private static $instance;
	private function __construct() {
		$this->conn = mysql_connect ( SERVER_NAME, DB_USER_NAME, DB_PASSWORD );
		try {
			mysql_select_db ( DATABASE, $this->conn );
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
		mysql_query ( "set names utf8;",$this->conn );
	}
	private function __clone() {
	}
	public static function getInstance() {
		if (! self::$instance instanceof self) {
			self::$instance = new db;
		}
		return self::$instance;
	}
	
	/*
	 * 查询数据库
	 */
	public function getAll($table, $condition = array(), $field = array()) {
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
			
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		$fieldstr = '';
		if (! empty ( $field )) {
			
			foreach ( $field as $k => $v ) {
				$fieldstr .= $v . ',';
			}
			$fieldstr = rtrim ( $fieldstr, ',' );
		} else {
			$fieldstr = '*';
		}
		self::$sql = "select {$fieldstr} from {$table} {$where}";
		$result = mysql_query ( self::$sql, $this->conn );
		$resuleRow = array ();
		$i = 0;
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			foreach ( $row as $k => $v ) {
				$resuleRow [$i] [$k] = $v;
			}
			$i ++;
		}
		return $resuleRow;
	}
	//查询一条记录
	public function getRow($table, $condition = array(), $field = array()) {
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
				
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		$fieldstr = '';
		if (! empty ( $field )) {
				
			foreach ( $field as $k => $v ) {
				$fieldstr .= $v . ',';
			}
			$fieldstr = rtrim ( $fieldstr, ',' );
		} else {
			$fieldstr = '*';
		}
		self::$sql = "select {$fieldstr} from {$table} {$where}";
		$result = mysql_query ( self::$sql, $this->conn );
		$resuleRow = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			return $row;
		}
	}
	//用sql查询记录条数
	public function getCount($table,$condition=array()){
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
				
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		self::$sql = "select count(*) as count from {$table} {$where}";
		$result = mysql_query ( self::$sql, $this->conn );
		while ( $row = @mysql_fetch_assoc ( $result ) ) {
			return @$row['count'];
			//return array_shift($row);
		}
	}
	/**
	 * 添加一条记录
	 */
	public function create($table, $data) {
		$table=DB_PREFIX.$table;
		$values = '';
		$datas = '';
		foreach ( $data as $k => $v ) {
			$values .= $k . ',';
			$datas .= "'$v'" . ',';
		}
		$values = rtrim ( $values, ',' );
		$datas = rtrim ( $datas, ',' );
		self::$sql = "INSERT INTO  {$table} ({$values}) VALUES ({$datas})";
		if (mysql_query ( self::$sql )) {
			return mysql_insert_id ();
		} else {
			return false;
		}
		;
	}
	/**
	 * 修改一条记录
	 */
	public function update($table, $data, $condition = array()) {
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
			
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		$updatastr = '';
		if (! empty ( $data )) {
			foreach ( $data as $k => $v ) {
				$updatastr .= $k . "='" . $v . "',";
			}
			$updatastr = 'set ' . rtrim ( $updatastr, ',' );
		}
		self::$sql = "update {$table} {$updatastr} {$where}";
		return mysql_query ( self::$sql );
	}
	/**
	 * 删除记录
	 */
	public function delete($table, $condition) {
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
			
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		self::$sql = "delete from {$table} {$where}";
		return mysql_query ( self::$sql );
	}
	
	//用sql查询所有
	public function getAllBySql($sql) {
		self::$sql = $sql;
		$result = mysql_query ( self::$sql );
		$return = array ();
		while ( $row = @mysql_fetch_assoc ( $result ) ) {
			$return [] = @$row;
		}
		return $return;
	}
	
	//用sql查询一条记录
	public function getRowBySql($sql){
		self::$sql = $sql;
		$result = mysql_query ( self::$sql );
		while ( $row = @mysql_fetch_assoc ( $result ) ) {
			return @$row;
		}
	}
	public function getCountBySql($sql){
		self::$sql = "select count(*) as count from ($sql) s ";
		$result = mysql_query ( self::$sql);
		while ( $row = @mysql_fetch_assoc ( $result ) ) {
			return @$row['count'];
		}
	}
	
	
	public static function getLastSql() {
		echo self::$sql;
	}
	
}