<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Login extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('url'));
		$this->load->model('user_model');
		
	}
	
	
	public function index() {
		$post=$this->input->post();
		$user = $this->input->post('username');
		$pass = $this->input->post('password');
		$data=array();
		if ($user != "" && $pass != "") {
			$userinfo = $this->user_model->get_user($user);
			if (count ( $userinfo ) > 0 && is_array ( $userinfo )) {
				$pwd = $userinfo ['user_password'];
				if ($pwd == md5($pass)) {
					$this->session->set_userdata('loginInfo',$userinfo);
					redirect ( 'index','index' );
				}else{
					$data=array('error_msg'=>"密码错误");
				}
			}else{
				$data=array('error_msg'=>"账号或密码错误");
			}
		}
		
		$this->load->view ( 'login' ,$data );
	}
	
	public function register(){
		$this->load->view ( 'register' );
	}
}
