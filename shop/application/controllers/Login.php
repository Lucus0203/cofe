<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Login extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('session','sms'));
		$this->load->helper(array('url'));
		$this->load->model(array('user_model'));
		
	}
	
	
	public function index() {
		$user = $this->input->post('username');
		$pass = $this->input->post('password');
		$data=array();
		if ($user != "" && $pass != "") {
			$userinfo = $this->user_model->get_user(array('user_name'=>$user));
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
	
	//注册
	public function register(){
		$res=array();
		$user = $this->input->post('username');
		$pass = $this->input->post('password');
		$mobile = $this->input->post('mobile');
		$code = $this->input->post('captcha_code');
		$act = $this->input->post('act');
		if(!empty($act)){
			$userinfo = $this->user_model->get_user(array('user_name'=>$user));
			if(!empty($userinfo)){
				$res['msg']='账号已被使用';
			}else{
				$userinfo = $this->user_model->get_user(array('mobile'=>$mobile));
				if(!empty($userinfo)&&$userinfo['captcha_code']==$code){
					$code=rand(100000, 999999);
					$this->user_model->update_user(array('user_name'=>$user,'user_password'=>md5($pass),'captcha_code'=>$code),$userinfo['id']);
					redirect(base_url('login'));
				}else{
					$res['msg']='验证码错误';
				}
			}
		}
		
		$this->load->view ( 'register',$res );
	}
	
	//获取验证码
	public function getcode(){
		$user = $this->input->post('username');
		$mobile = $this->input->post('mobile');
		$code=rand(100000, 999999);
		$userinfo = $this->user_model->get_user(array('user_name'=>$user));
		if(!empty($userinfo)){
			echo '账号已被使用';
		}else{
			$userinfo = $this->user_model->get_user(array('mobile'=>$mobile));
			if(!empty($userinfo['id'])){
				$this->user_model->update_user(array('captcha_code'=>$code),$userinfo['id']);
			}else{
				$this->user_model->create_user(array('mobile'=>$mobile,'captcha_code'=>$code,'created'=>date("Y-m-d H:i:s")));
			}
			echo '验证码已发送，请注意查收';
		}
		//$this->sms->sendMsg($msg,$mobile);
	}
	
	public function loginout(){
		$this->session->sess_destroy();
		redirect(base_url('login'));
	}
	
}
