<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>BLUSECMS</title>

<meta name="GENERATOR" content="MCBN">

<link rel="stylesheet" type="text/css" href="/Public/admin/css/style.css" />
<link rel="stylesheet" type="text/css" href="/Public/common/mypage.css" /><!-- 分页样式css -->

<script type="text/javascript" src="/Public/admin/js/jquery.js"></script>
<script type="text/javascript" src="/Public/common/js/zczy-UI.js"></script>
<script type="text/javascript" src="/Public/admin/js/common.js"></script>
<script type="text/javascript" src="/Public/common/kindeditor/kindeditor.js"></script>

	<link rel="stylesheet" type="text/css" href="/Public/layui/css/layui.css" />
	<script type="text/javascript" src="/Public/layui/layui.js"></script>

	<script type="text/javascript" src="/Public/admin/layer/layer.js"></script>

	<script src="/Public/admin/player/sewise.player.min.js"></script>


<link href="/Public/home/css/qikoo.css" type="text/css" rel="stylesheet" />
<link href="/Public/home/css/store.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="/Public/home/js/qikoo.js"></script>
</head>
<body>
	<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
		<TBODY>
			<TR>
				<TD vAlign=top background="/Public/admin/images/mail_leftbg.gif"
					width="17"><IMG
					src="/Public/admin/images/left-top-right.gif" width="17"
					height="29"></TD>
				<TD vAlign="top" background="/Public/admin/images/content-bg.gif">
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
					background="/Public/admin/images/mail_rightbg.gif" width="16">
					<IMG src="/Public/admin/images/nav-right-bg.gif" width="16"
					height="29">
				</TD>
			</TR>
			<TR>
				<TD vAlign="center"
					background="/Public/admin/images/mail_leftbg.gif">&nbsp;</TD>
				<TD align="left" vAlign="top" bgColor="#f7f8f9">

<link rel="stylesheet" type="text/css" href="/Public/admin/js/uploadify.css" />
<script type="text/javascript" src="/Public/admin/js/swfobject.js"></script>
<script type="text/javascript" src="/Public/admin/js/jquery.uploadify.v2.1.4.min.js"></script>

<script type="text/javascript">
 $(function($) {
	$("#file_upload2").uploadify({
	 		'uploader'       : '/Public/admin/js/uploadify.swf',
	 		'script'         : '/Public/admin/js/uploadify.php',
	 		'cancelImg'      : '/Public/admin/images/cancel.png',
	 		'folder'         : '/Public/admin/Uploads/touxiang',  //保存路径
	 		'queueID'        : 'fileQueue2',
	 		'sizeLimit'      :	10 * 1000 * 1024,
			'buttonImg'      : '/Public/admin/images/llsc.jpg',
			'width'          :  85,
			'height'          :  28,
	 		'fileExt'        : '*.jpg;*.gif;*.png;', //允许文件上传类型,和fileDesc一起使用.
	 		'fileDesc'       : '*.jpg;*.gif;*.png;',  //选择文件对话框中的提示文本.
	 		'auto'           : true,
	 		'multi'          : false,	
	 		'onComplete':function(event,queueId,fileObj,response,data){
	 			$('input[name="logo"]').val(response);
	 			$('#pic2').attr('src', response);
	 		}
	 	});

	 });
</script>
<SCRIPT language=JavaScript>
	     function checkss(){
             var id = $("#id").val();
	    	 var phone    = $("#phone").val();
	    	 if(phone==''){
                 $(".yzphone").html('填写账号！');
                 $("#phone").focus();
                 return false;
	    	 }else {
                 $(".yzphone").html('');
                 var result=false;
                 $.ajax({async:false//要设置为同步的，要不CheckUserName的返回值永远为false
                     ,url:'<?php echo U("yzmobile");?>',data:{id:id,mobile:phone}
                     ,success:function(data){
                         if(data == 1){
                             $(".yzphone").html('账号已注册');
                             $("#phone").focus();
                             result = false;
                         } else {
                             result = true;
                         }
                     }});
                 return result;
             }
	    	 
	     }

         function area_linke1(value){
             $.post("<?php echo U('get_area');?>", {value:value,type:1}, function(v){

                 $("#shi").html(v);

             });
         }
         function area_linke2(value){
             $.post("<?php echo U('get_area');?>", {value:value,type:2}, function(v){

                 $("#qu").html(v);

             });
         }
	</script>
<div class="content">
<!-----------------------------------------内容开始--------------------------------------------------->
<div class="infoBox">
<form name="form" action="<?php echo U('doadd');?>"  method="post" onsubmit="return checkss();" enctype="multipart/form-data">
<table width="90%" border="0" cellpadding="0" cellspacing="0" id="basic">
               <input type="hidden" value="<?php echo ($u["user_id"]); ?>" name="id" id="id">
                <tr>
                    <td  width="10%" class="infoBoxTd"><div style="width:55px;">账号:</div></td>
                    <td ><input type="text" id="phone"  name="phone" value="<?php echo ($u["phone"]); ?>" style="width:250px;" placeholder="手机号">&nbsp;&nbsp;<span class="yzphone" style="color:red"></span>
                    </td>
                </tr>
                <tr>
                  <td  width="10%" class="infoBoxTd"><div style="width:55px;">昵称:</div></td>
		  		  <td ><input type="text" id="username"  name="username" value="<?php echo ($u["username"]); ?>" style="width:250px;">&nbsp;&nbsp;&nbsp;
                     <?php if( $u["user_id"] == '' ): else: ?>ID: <input type="text" id="ID"  name="ID" value="<?php echo ($u["id"]); ?>" readonly style="width:180px;background-color: #DDDDDD">&nbsp;&nbsp;&nbsp;<?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td  width="2%"  style="text-align:right;color:#2d52a5"><div style="width:55px;">头像:</div></td>
		  		  <td colspan="2">
		  		  <div class="upimg-box"><div id="fileQueue2"></div>
                  <input id="file_upload2" type="file"/>
                  <input name="logo" type="hidden" id="fileDoc" value="<?php echo ($u["img"]); ?>" />
                  &nbsp;&nbsp;<img  id="pic2" src="<?php if($u["img"] == "" ): ?>/Public/admin/images/nopic.gif<?php else: echo ($u["img"]); endif; ?>" width="110"  height="60"/>
                 </div>
		  		  </td>
                </tr>
                 <tr>
                  <td  width="10%" class="infoBoxTd"><div style="width:55px;">性别:</div></td>
		  		  <td colspan="2">
                      <input type="radio" name="sex" value="1" <?php if( $u["sex"] == 1 ): ?>checked=checked<?php else: endif; ?>>男&nbsp;&nbsp;
                      <input type="radio" name="sex" value="2" <?php if( $u["sex"] == 2 ): ?>checked=checked<?php else: endif; ?>>女
                  </td>
                </tr>
                 <tr>
                  <td style="text-align:right;color:#2d52a5">地区:</td>
		  		  <td>
                      <select name="sheng" onchange="area_linke1(this.value)">
                          <option value="">请选择</option>
                          <?php if(is_array($sheng)): $i = 0; $__LIST__ = $sheng;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><option value="<?php echo ($l["id"]); ?>" <?php if( $u["province"] == $l["name"] ): ?>selected<?php else: endif; ?>><?php echo ($l["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                      </select>
                      <select name="shi" id="shi" onchange="area_linke2(this.value)">
                          <?php if( $u["shi"] == null ): else: ?>
                              <option value=''>请选择（市）</option>
                              <?php if(is_array($u["shi"])): $i = 0; $__LIST__ = $u["shi"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;?><option value="<?php echo ($s["id"]); ?>" <?php if( $u["city_id"] == $s["id"] ): ?>selected<?php else: endif; ?>><?php echo ($s["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                      </select>
                      <select name="qu" id="qu">
                          <?php if( $u["qu"] == null ): else: ?>
                              <option value=''>请选择（区/县）</option>
                              <?php if(is_array($u["qu"])): $i = 0; $__LIST__ = $u["qu"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;?><option value="<?php echo ($s["id"]); ?>" <?php if( $u["area_id"] == $s["id"] ): ?>selected<?php else: endif; ?>><?php echo ($s["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                      </select>
                      <input value="<?php echo ($u['address']); ?>" name="address" placeholder="具体地址" type="text" size="50">
		  		  </td>
                </tr>
                 <tr>
                  <td class="infoBoxTd">个性签名:</td>
		  		  <td colspan="2">
		  		  <textarea id="personalized_signature" name="personalized_signature" style="width: 450px;height: 80px"><?php echo ($u["personalized_signature"]); ?></textarea>
		  		  </td>
                </tr>
                
              
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="infoBoxTd">&nbsp;</td>
<td align="left"><input type="submit" name="submit" value="保存" class="formInput01" /></td>
</tr>
</table>
</form>
</div>
<script>
KindEditor.ready(function(K) {
    k1 = K.create('#content', {});
    k2 = K.create('#content2', {});
    k3 = K.create('#content3', {});

});
///var ue = UE.getEditor('content');
///var ue = UE.getEditor('content2');
///var ue = UE.getEditor('content3');
</script>
<!-----------------------------------------内容结束--------------------------------------------------->
</div>
    
    
    
    
    </TD><TD background="/Public/admin/images/mail_rightbg.gif">&nbsp;</TD>
</TR>
<TR>
    <TD vAlign="bottom" background="/Public/admin/images/mail_leftbg.gif">
    <IMG src="/Public/admin/images/buttom_left2.gif" width="17" height="17"></TD>
    <TD background="/Public/admin/images/buttom_bgs.gif">
    <IMG src="/Public/admin/images/buttom_bgs.gif" width="17" height="17"></TD>
    <TD vAlign="bottom" background="/Public/admin/images/mail_rightbg.gif">
    <IMG src="/Public/admin/images/buttom_right2.gif" width="16" height="17">
    </TD>
</TR>

</TBODY>
</TABLE>
</body>
</html>