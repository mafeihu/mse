<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META content=text/html;charset=utf-8 http-equiv=Content-Type>
<TITLE>梅塞尔后台</TITLE>


<SCRIPT language="javascript">
function logout(){
	if (confirm("确定要退出管理面板吗？"))
	top.location = "<?php echo U('Tourist/logout');?>";
	return false;
}
function showsubmenu(sid) {
	var whichEl = eval("submenu" + sid);
	var menuTitle = eval("menuTitle" + sid);
	if (whichEl.style.display == "none"){
		eval("submenu" + sid + ".style.display=\"\";");
	}else{
		eval("submenu" + sid + ".style.display=\"none\";");
	}
}
function showsubmenu(sid) {
	var whichEl = eval("submenu" + sid);
	var menuTitle = eval("menuTitle" + sid);
	if (whichEl.style.display == "none"){
		eval("submenu" + sid + ".style.display=\"\";");
	}else{
		eval("submenu" + sid + ".style.display=\"none\";");
	}
}
function chpass(){
	window.parent.window.document.getElementById('main').contentWindow.chpasswd();
}
</SCRIPT>
<link rel="stylesheet" type="text/css" href="/mse/Public/admin/css/header.css" />
<STYLE media=screen>IMG {
	BORDER-RIGHT-WIDTH: 0px; BORDER-TOP-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px
}
</STYLE>

<META name="GENERATOR" content="MCBN">
</HEAD>
<BODY leftMargin="0" topMargin="0">
<TABLE class="admin_topbg" border="0" cellSpacing="0" cellPadding="0" width="100%" height="40">
  <TBODY>
  <TR>
     <TD height=40 width="61%">
    	<h2><font color="white"><?php echo ($title); ?>后台</font></h2>
    </A></TD>
    <TD vAlign=top width="39%">
      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
        <TBODY>
        <TR>
          <TD height=38 align="right" class=admin_txt>&nbsp;&nbsp; 用户 ：<B><?php echo ($_SESSION["user"]["name"]); ?></B> 您好！
            <!--|&nbsp; <a href="/mse/Index/index" target="_blank" style="color:#FFF;">返回前台</a>&nbsp;&nbsp;-->
           <a href="/cleancache.php" target="main"><IMG style="margin-top:5px;position: relative ;top:8px;" border="0" alt="安全退"出 src="/mse/Public/admin/images/clearcache_bnt.jpg"></a>
		    <A onClick="logout();" href="javascript:void(0);"><IMG style="margin-top:5px;position: relative ;top:8px;" border="0" alt="安全退"出 src="/mse/Public/admin/images/tuichu_bnt.jpg"></A>
		  </TD>
          </TR>
        <TR>
        <TD width="3%">&nbsp;</TD>
</TR></TBODY></TABLE></TD></TR></TBODY></TABLE></BODY></HTML>