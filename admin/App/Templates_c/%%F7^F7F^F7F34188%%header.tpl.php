<?php /* Smarty version 2.6.18, created on 2014-11-12 09:48:55
         compiled from header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'header.tpl', 22, false),)), $this); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo @DEFAUT_TITLE; ?>
</title>
<link href="<?php echo @SITE; ?>
resource/css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo @SITE; ?>
resource/js/jQuery.js"></script>
<script type="text/javascript">
<?php echo '
$(function(){
	$(window).resize(function(){
		$(\'.main_l\').height($(window).height()-160);
	});
	$(\'.main_l\').height($(window).height()-160);
});
'; ?>

</script>
</head>
<body>
<div class="top">
	<div class="fl"><img src="resource/images/login_01.jpg"></div>
    <div class="fr top_fr">欢迎您 admin 管理员<a href="<?php echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'Default','action' => 'LoginOut'), $this);?>
">[ 退出系统 ]</a></div>
</div>