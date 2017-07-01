<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>BLUSECMS</title>

<meta name="GENERATOR" content="MCBN">

<link rel="stylesheet" type="text/css" href="/mse/Public/admin/css/style.css" />
<link rel="stylesheet" type="text/css" href="/mse/Public/common/mypage.css" /><!-- 分页样式css -->

<script type="text/javascript" src="/mse/Public/admin/js/jquery.js"></script>
<script type="text/javascript" src="/mse/Public/common/js/zczy-UI.js"></script>
<script type="text/javascript" src="/mse/Public/admin/js/common.js"></script>
<script type="text/javascript" src="/mse/Public/common/kindeditor/kindeditor.js"></script>

	<link rel="stylesheet" type="text/css" href="/mse/Public/layui/css/layui.css" />
	<script type="text/javascript" src="/mse/Public/layui/layui.js"></script>

	<script type="text/javascript" src="/mse/Public/admin/layer/layer.js"></script>

	<script src="/mse/Public/admin/player/sewise.player.min.js"></script>


<link href="/mse/Public/home/css/qikoo.css" type="text/css" rel="stylesheet" />
<link href="/mse/Public/home/css/store.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="/mse/Public/home/js/qikoo.js"></script>
</head>
<body>
	<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
		<TBODY>
			<TR>
				<TD vAlign=top background="/mse/Public/admin/images/mail_leftbg.gif"
					width="17"><IMG
					src="/mse/Public/admin/images/left-top-right.gif" width="17"
					height="29"></TD>
				<TD vAlign="top" background="/mse/Public/admin/images/content-bg.gif">
					<TABLE id="table2" class="left_topbg" border="0" cellSpacing="0"
						cellPadding="0" width="100%" height="31">
						<TBODY>
							<TR>
								<TD height="31">
									<DIV class="titlebt">

										<?php if($pagetitle == ''): ?>系统基本信息 <?php else: ?>
										<?php echo ($pagetitle); endif; ?>

									</DIV>
								</TD>
							</TR>
						</TBODY>
					</TABLE>
				</TD>
				<TD vAlign="top"
					background="/mse/Public/admin/images/mail_rightbg.gif" width="16">
					<IMG src="/mse/Public/admin/images/nav-right-bg.gif" width="16"
					height="29">
				</TD>
			</TR>
			<TR>
				<TD vAlign="center"
					background="/mse/Public/admin/images/mail_leftbg.gif">&nbsp;</TD>
				<TD align="left" vAlign="top" bgColor="#f7f8f9">

<?php echo W("Easyui");?>

<link rel="stylesheet" type="text/css" href="/mse/Public/admin/js/uploadify.css" />
<script type="text/javascript" src="/mse/Public/admin/js/swfobject.js"></script>
<script type="text/javascript" src="/mse/Public/admin/js/jquery.uploadify.v2.1.4.min.js"></script>

<script type="text/javascript">
 $(function($) {
	$("#file_upload").uploadify({
	 		'uploader'       : '/mse/Public/admin/js/uploadify.swf',
	 		'script'         : '/mse/Public/admin/js/uploadify.php',
	 		'cancelImg'      : '/mse/Public/admin/images/cancel.png',
	 		'folder'         : '/mse/Public/admin/Uploads',
	 		'queueID'        : 'fileQueue',
	 		'sizeLimit'      :	10 * 1000 * 1024,
			'buttonImg'      : '/mse/Public/admin/images/llsc.jpg',
			'width'          :  85,
			'height'          :  28,
	 		'fileExt'        : '*.jpg;*.gif;*.png;', //允许文件上传类型,和fileDesc一起使用.
	 		'fileDesc'       : '*.jpg;*.gif;*.png;',  //选择文件对话框中的提示文本.
	 		'auto'           : true,
	 		'multi'          : false,	
	 		'onComplete':function(event,queueId,fileObj,response,data){
	 			$('input[name="logo1"]').val(response);
	 			$('#pic1').attr('src', response);
	 		}
	 	});

	 });
    $(function($) {
		$("#file_upload2").uploadify({
		 		'uploader'       : '/mse/Public/admin/js/uploadify.swf',
		 		'script'         : '/mse/Public/admin/js/uploadify.php',
		 		'cancelImg'      : '/mse/Public/admin/images/cancel.png',
		 		'folder'         : '/mse/Public/admin/Uploads',
		 		'queueID'        : 'fileQueue2',
		 		'sizeLimit'      :	10 * 1000 * 1024,
				'buttonImg'      : '/mse/Public/admin/images/llsc.jpg',
				'width'          :  85,
				'height'          :  28,
		 		'fileExt'        : '*.jpg;*.gif;*.png;', //允许文件上传类型,和fileDesc一起使用.
		 		'fileDesc'       : '*.jpg;*.gif;*.png;',  //选择文件对话框中的提示文本.
		 		'auto'           : true,
		 		'multi'          : false,	
		 		'onComplete':function(event,queueId,fileObj,response,data){
		 			$('input[name="logo2"]').val(response);
		 			$('#pic2').attr('src', response);
		 		}
		 	});

		 });
    
</script>
<div class="content">
	<!-----------------------------------------内容开始--------------------------------------------------->
	<div class="infoBox">
		<form name="form" action="/mse/Admin/Base/doupdate" method="post" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<?php echo ($base["id"]); ?>">
			<table width="100%" border="0" cellpadding="0" cellspacing="0"
				id="basic">
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">网站名称:</td>
					<td colspan="2"> <input  id="title" name="title" size="60" value="<?php echo ($base["title"]); ?>"></td>
				</tr>
				<!--<tr bgcolor="white">-->
					<!--<td width="18%" class="infoBoxTd" bgcolor="white" style="line-height:5;">网站logo:</td>-->
					<!--<td colspan="2">-->
                    <!--<div class="upimg-box"><div id="fileQueue"></div>-->
                       <!--<input id="file_upload" type="file" />-->
                       <!--<input name="logo1" type="hidden" id="fileDoc" value="<?php echo ($base["logo1"]); ?>" />-->
                       <!--&nbsp;&nbsp;<img  id="pic1" src="<?php if($base["logo1"] == "" ): ?>/mse/Public/admin/images/nopic.gif<?php else: echo ($base["logo1"]); endif; ?>" width="110"  height="60"/>-->
                       <!--</div>-->
  <!---->
                    <!--</td>-->
				<!--</tr>-->

				<!--<tr bgcolor="white">-->
					<!--<td width="18%" class="infoBoxTd" bgcolor="white">微信appid:</td>-->
					<!--<td colspan="2"> <input  id="appid" name="appid" size="60" value="<?php echo ($base["appid"]); ?>"></td>-->
				<!--</tr>-->
				<!--<tr bgcolor="white">-->
					<!--<td width="18%" class="infoBoxTd" bgcolor="white">微信appsecret:</td>-->
                    <!--<td colspan="2"> <input  id="appsecret" name="appsecret" size="60" value="<?php echo ($base["appsecret"]); ?>"></td>-->
				<!--</tr>-->
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">ping++~apiid：</td>
					<td colspan="2"><input type="text" id="apiid" name="apiid" size="60" value="<?php echo ($base["apiid"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">ping++~appsecret：</td>
					<td colspan="2"><input type="text" id="secretkey" name="secretkey" size="60" value="<?php echo ($base["secretkey"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">极光appkey：</td>
					<td colspan="2"><input type="text" id="jg_appkey" name="jg_appkey" size="60" value="<?php echo ($base["jg_appkey"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">极光secret：</td>
					<td colspan="2"><input type="text" id="jg_secret" name="jg_secret" size="60" value="<?php echo ($base["jg_secret"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">环信client_id：</td>
					<td colspan="2"><input type="text" id="hx_client_id" name="hx_client_id" size="60" value="<?php echo ($base["hx_client_id"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">环信secret：</td>
					<td colspan="2"><input type="text" id="hx_secret" name="hx_secret" size="60" value="<?php echo ($base["hx_secret"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">环信appkey_1：</td>
					<td colspan="2"><input type="text" id="hx_appkey_1" name="hx_appkey_1" size="60" value="<?php echo ($base["hx_appkey_1"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">环信appkey_2：</td>
					<td colspan="2"><input type="text" id="hx_appkey_2" name="hx_appkey_2" size="60" value="<?php echo ($base["hx_appkey_2"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">百度地图api：</td>
					<td colspan="2"><input type="text" id="baidu_apikey" name="baidu_apikey" size="60" value="<?php echo ($base["baidu_apikey"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">七牛appkey：</td>
					<td colspan="2"><input type="text" id="ak" name="ak" size="60" value="<?php echo ($base["ak"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">七牛secret：</td>
					<td colspan="2"><input type="text" id="sk" name="sk" size="60" value="<?php echo ($base["sk"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">七牛推流地址：</td>
					<td colspan="2"><input type="text" id="publishurl" name="publishurl" size="60" value="<?php echo ($base["publishurl"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">七牛播放地址：</td>
					<td colspan="2"><input type="text" id="playurl" name="playurl" size="60" value="<?php echo ($base["playurl"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">助通账号：</td>
					<td colspan="2"><input type="text" id="zhutong_username" name="zhutong_username" size="60" value="<?php echo ($base["zhutong_username"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">助通密码：</td>
					<td colspan="2"><input type="text" id="zhutong_password" name="zhutong_password" size="60" value="<?php echo ($base["zhutong_password"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">短信有效期：</td>
					<td colspan="2"><input type="text" id="code_volidity" name="code_volidity" size="40" value="<?php echo ($base["code_volidity"]); ?>"><span>&nbsp;分钟</span></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">弹幕价格：</td>
					<td colspan="2"><input type="text" id="screen_price" name="screen_price" size="60" value="<?php echo ($base["screen_price"]); ?>">&nbsp;平米</td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">提现最低限制：</td>
					<td colspan="2"><input type="text" id="lowest_limit" name="lowest_limit" size="60" value="<?php echo ($base["lowest_limit"]); ?>">&nbsp;注:用户少于此值,不能提现(K币)</td>
				</tr>
				<!--<tr bgcolor="white">-->
					<!--<td width="18%" class="infoBoxTd" bgcolor="white">兑换比例：</td>-->
					<!--<td colspan="2"><input type="text" id="convert_scale1" name="convert_scale1" size="25" value="<?php echo ($base["convert_scale1"]); ?>">&nbsp;:&nbsp;<input type="text" id="convert_scale2" name="convert_scale2" size="25" value="<?php echo ($base["convert_scale2"]); ?>"> 注:K币兑换平米</td>-->
				<!--</tr>-->
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">提现比例：</td>
					<td colspan="2"><input type="text" id="convert_scale3" name="convert_scale3" size="25" value="<?php echo ($base["convert_scale3"]); ?>">&nbsp;:&nbsp;<input type="text" id="convert_scale4" name="convert_scale4" size="25" value="<?php echo ($base["convert_scale4"]); ?>"> 注:K币提现支付宝</td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">IOS版本号：</td>
					<td colspan="2"><input type="text" id="ios_version" name="ios_version" size="60" value="<?php echo ($base["ios_version"]); ?>"></td>
				</tr>
				<tr bgcolor="white">
					<td width="18%" class="infoBoxTd" bgcolor="white">Android版本号：</td>
					<td colspan="2"><input type="text" id="android_version" name="android_version" size="60" value="<?php echo ($base["android_version"]); ?>"></td>
				</tr>

				
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="infoBoxTd">&nbsp;</td>
					<td align="left"><input type="submit" name="submit" value="保存"
						class="formInput01" /></td>
				</tr>
			</table>
		</form>
		
		<script type="text/javascript">
				$(function() {
					$(".zhifustatus[value=<?php echo ($base["zhifustatus"]); ?>]").attr("checked","checked");
				})
				function check1(e){
					if($("#yongjin").val()>100){
						alert("佣金抽取不能超过100！");
						return false;
					}
				}
				function check2(e){
					if($("#sunshibai").val()>100){
						alert("外汇损失率不能超过100！");
						return false;
					}
				}
			</script>
	</div>
</div>
    
    
    
    
    </TD><TD background="/mse/Public/admin/images/mail_rightbg.gif">&nbsp;</TD>
</TR>
<TR>
    <TD vAlign="bottom" background="/mse/Public/admin/images/mail_leftbg.gif">
    <IMG src="/mse/Public/admin/images/buttom_left2.gif" width="17" height="17"></TD>
    <TD background="/mse/Public/admin/images/buttom_bgs.gif">
    <IMG src="/mse/Public/admin/images/buttom_bgs.gif" width="17" height="17"></TD>
    <TD vAlign="bottom" background="/mse/Public/admin/images/mail_rightbg.gif">
    <IMG src="/mse/Public/admin/images/buttom_right2.gif" width="16" height="17">
    </TD>
</TR>

</TBODY>
</TABLE>
</body>
</html>