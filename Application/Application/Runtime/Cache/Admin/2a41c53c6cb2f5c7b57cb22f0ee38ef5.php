<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="en" class="no-js">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>梅塞尔后台登录系统</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="" name="description">
	<meta content="" name="author">
	<meta name="MobileOptimized" content="320">
	<!-- 主题样式 --> 
	<link href="/Public/admin/new/css/login-soft.css" rel="stylesheet" type="text/css">
	<!-- /主题样式 -->
	
<script language="javascript">
if(parent.window!=window){
	parent.window.location.href="/login";
}
	function freshVerify() {
		var timenow = new Date().getTime();
		$('#vdimgck').attr('src',
				'/App/CheckCode/getCode?t=' + timenow);
	}
	function checkcode() {
		if (checkCode())
			$("#form").submit();
	}
</script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login" style="">
	<!-- BEGIN LOGIN -->
	<div id="intro_loginform" class="content">
		<!-- BEGIN LOGIN FORM -->
		<!-- 登录表单 -->
		<form class="login-form" method="post" novalidate="novalidate" action="<?php echo U('Admin/Tourist/checkLogin');?>">
				<h3 class="form-title"><?php echo ($title); ?>登录系统</h3>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">帐号</label>
				<div class="input-icon">
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="请输入账号" name="username">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">密码</label>
				<div class="input-icon">
					<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="请输入密码" name="password">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">验证码</label>
				<div class="input-icon">
					<input class="form-control placeholder-no-fix"  
						style="width: 220px; display: inline; margin-right: 5px;"
						autocomplete="off" placeholder="请输入验证码" name="checkcode"> <img
						id="vdimgck" onclick="freshVerify();"
						src="/App/CheckCode/getCode"
						style="cursor: pointer; width: 100px; height: 34px;" />
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="dl_btn" id="form">登录系统<i class="m-icon-swapright"></i></button>            
			</div>
			</form>
		<!-- END LOGIN FORM -->        
		<!-- /登录表单 -->
	</div>
	<script src="/Public/admin/new/js/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script src="/Public/admin/new/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="/Public/admin/new/js/jquery.cookie.min.js" type="text/javascript"></script>
	<script src="/Public/admin/new/js/jquery.validate.min.js" type="text/javascript"></script>
	<script src="/Public/admin/new/js/jquery.backstretch.min.js" type="text/javascript"></script>
	<script src="/Public/admin/new/js/app.js" type="text/javascript"></script>
	 <script src="/Public/admin/new/js/login-soft.js" type="text/javascript"></script>    
  
	<!-- END PAGE LEVEL SCRIPTS --> 
	<script>
		jQuery(document).ready(function() {     
			App.init();
			Login.init();
			
			$.backstretch([
					"/Public/admin/new/images/judianlogin1.jpg",
					"/Public/admin/new/images/judianlogin2.jpg",
					"/Public/admin/new/images/judianlogin3.jpg"
					], {
					  fade: 1000,
					  duration: 5000
				});
						//tour.goTo(1);
		});
	</script>
	<!-- END JAVASCRIPTS -->


<!-- END BODY -->
<div class="backstretch" style="left: 0px; top: 0px; overflow: hidden; margin: 0px; padding: 0px; height: 745px; width: 1440px; z-index: -999999; position: fixed; "><img style="position: absolute; margin: 0px; padding: 0px; border: none; width: 1440px; height: 900px; max-width: none; z-index: -999999; left: 0px; top: -77.5px; " src="/Public/admin/new/images/judianlogin1.jpg"></div>
</body>
</html>