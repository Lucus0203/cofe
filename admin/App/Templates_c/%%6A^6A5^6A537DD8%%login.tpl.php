<?php /* Smarty version 2.6.18, created on 2014-10-16 11:23:19
         compiled from login.tpl */ ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo @DEFAUT_TITLE; ?>
</title>
<link href="<?php echo @SITE; ?>
resource/css/style.css" rel="stylesheet" type="text/css">
</head>
<body style="background:#977342;">
<div class="login_box">
	<div class="login_t"><img src="resource/images/login_t_03.jpg"></div>
   <form action="" method="post">
    <div class="login_in">
	 <?php if ($this->_tpl_vars['error_msg'] != ""): ?>
    	<p style="color:red;margin-left:50px;"><?php echo $this->_tpl_vars['error_msg']; ?>
</p>
     <?php endif; ?>
    	<ul>
        	<li>账号：<input id="admname" name="admname" type="text"></li>
            <li>密码：<input id="pass" name="password" type="password"></li>
        </ul>
    </div>
    <div class="login_btn"><input type="image" src="resource/images/login_btn_03.jpg" /><input onclick="document.getElementById('admname').value='';document.getElementById('pass').value='';return false;" type="image" src="resource/images/login_btn_05.jpg" /></div>
    </form>
</div>

</body>
</html>