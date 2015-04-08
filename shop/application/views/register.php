<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>咖啡约我店家后台</title>
<link href="<?php echo base_url() ?>css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url() ?>js/jQuery.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/common.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/register.js"></script>
</head>
<body style="background:#977342;">
<div class="login_box register_box">
	<div class="login_t">店家注册</div>
   <form action="" method="post">
   <input type="hidden" id="baseUrl" value="<?php echo base_url() ?>" />
   <input type="hidden" name="act" value="add" />
    <div class="login_in">
	 <?php if (!empty($msg)){ ?>
    	<p style="color:red;margin-left:61px;"><?php echo $msg ?></p>
     <?php } ?>
    	<ul>
        	<li>账&nbsp;&nbsp;&nbsp;号：<input id="username" name="username" type="text"></li>
        	<li>密&nbsp;&nbsp;&nbsp;码：<input id="pass" name="password" type="password"></li>
        	<li>手机号：<input id="mobile" name="mobile" type="text"> <a id="getCode" href="javascript:void(0)" >获取验证码</a></li>
            <li>验证码：<input id="pass" name="captcha_code" type="text"></li>
        </ul>
    </div>
    <div class="login_btn"><input class="register_btn" type="submit" value="确认注册" /></div>
    </form>
</div>

</body>
</html>
