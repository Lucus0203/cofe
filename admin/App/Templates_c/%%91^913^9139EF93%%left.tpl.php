<?php /* Smarty version 2.6.18, created on 2014-11-11 14:13:55
         compiled from left.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'left.tpl', 3, false),)), $this); ?>
<td width="184" valign="top">
	<div class="main_l">
    	<div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'PublicEvent','action' => 'Index'), $this);?>
">官方活动</a></div>
    	<div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'PublicEvent','action' => 'Add'), $this);?>
">发起活动</a></div>
    	<div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Shop','action' => 'Index'), $this);?>
">咖啡店铺</a></div>
    	<div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Shop','action' => 'Add'), $this);?>
">添加店铺</a></div>
        <div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'User','action' => 'Index'), $this);?>
">用户管理</a></div>
    	<div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'UserEvent','action' => 'Index'), $this);?>
">用户活动</a></div>
        <div class="menu_b_box">
	        <div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'User','action' => 'Feedback'), $this);?>
">用户反馈</a></div>
	        <div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Index','action' => 'Banner'), $this);?>
">首页滚动图</a></div>
	        <div class="main_l_menu"><a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Admin','action' => 'ChangePass'), $this);?>
">管理员密码</a></div>
        </div>
    </div>
</td>