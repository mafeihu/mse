<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='Refresh' content='<?php echo ($waitSecond); ?>;URL=<?php echo ($jumpUrl); ?>'>
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body {background-color: #f1f1f1; margin: 0px; padding-bottom:50px; font-size:14px; line-height:25px;font-family: '微软雅黑'; }
.Prompt_top, .Prompt_btm, .Prompt_ok, .Prompt_x { background:url(/Public/admin/images/message.gif) no-repeat; display:inline-block }
.Prompt { width:640px;text-align:left; margin:100px auto; display:block; clear:both; }
.Prompt_top { background-position:0 0; height:15px; width:100%; }
.Prompt_con { border-left:1px solid #E7E7E7; border-right:1px solid #E7E7E7; background:#fff; overflow:hidden;}
.Prompt_btm { background-position:0 -27px; height:6px; width:100%; overflow:hidden; }
.Prompt_con dl { margin:0 30px; overflow:hidden;}
.Prompt_con dt { font-size:18px; padding:15px 0; border-bottom:1px solid #EEEEEE; font-weight: bold;_height:20px;}
.Prompt_con dd { float:left; display:block; padding:15px; }
.Prompt_con dd h2 { font-size:14px; line-height:30px; }
.Prompt_ok { background-position:-72px -39px; width:68px; height:68px; }
.Prompt_x { background-position:0 -39px; width:68px; height:68px; }
.Prompt_con a.a { color:#fff; padding:0 15px; line-height:30px; background-color:#307ba0; display:inline-block; font-size:14px; margin:20px 0px; }
</style>
<script> 
function Jump(){
    window.location.href = '<?php echo ($jumpUrl); ?>';
}
</script>
</head>
<body>
<?php if(($status) == "1"): ?><div class="Prompt">
  <div class="Prompt_top"></div>
  <div class="Prompt_con">
    <dl>
      <dt>提示信息</dt>
      <dd><span class="Prompt_ok"></span></dd>
      <dd>
        <h2 style="color:black"><?php echo ($message); ?></h2>
        <?php if(isset($closeWin)): ?><p>系统将在 <span style="color:blue;font-weight:bold" id="second"><?php echo ($waitSecond); ?></span> 秒后自动关闭，如果不想等待,直接点击 <A HREF="<?php echo ($jumpUrl); ?>"><font color="red">这里</font></A> 关闭</p><?php endif; ?>
        <?php if(!isset($closeWin)): ?><p>系统将在 <span style="color:blue;font-weight:bold" id="second"><?php echo ($waitSecond); ?></span> 秒后自动跳转,如果不想等待,直接点击 <A HREF="<?php echo ($jumpUrl); ?>"><font color="red">这里</font></A> 跳转<br/>
            或者 <a href="/">返回首页</a></p><?php endif; ?>
      </dd>
    </dl>
    <div class="c"></div>
    </div>
    <div class="Prompt_btm"></div>
  </div><?php endif; ?>
<?php if(($status) == "0"): ?><div class="Prompt">
    <div class="Prompt_top"></div>
  <div class="Prompt_con">
    <dl>
      <dt>提示信息</dt>
      <dd><span class="Prompt_x"></span></dd>
      <dd>
      <h2 style="color:red"><?php echo ($error); ?></h2>
        <?php if(isset($closeWin)): ?><p>系统将在 <span style="color:blue;font-weight:bold" id="second"><?php echo ($waitSecond); ?></span> 秒后自动关闭，如果不想等待,直接点击 <A HREF="<?php echo ($jumpUrl); ?>"><font color="red">这里</font></A> 关闭</p><?php endif; ?>
      <?php if(!isset($closeWin)): ?><p>系统将在 <span style="color:blue;font-weight:bold" id="second"><?php echo ($waitSecond); ?></span> 秒后自动跳转,如果不想等待,直接点击 <A HREF="<?php echo ($jumpUrl); ?>"><font color="red">这里</font></A> 跳转<br/>
          或者 <a href="/">返回首页</a></p><?php endif; ?>
      </dd>
    </dl>
    <div class="c"></div>
    </div>
    <div class="Prompt_btm"></div>
  </div><?php endif; ?>
<script>
var interval = setInterval(function(){
	var value = document.getElementById("second").innerHTML/1;
	if(value>0){
		value--;
		document.getElementById("second").innerHTML=value;
	}else{
		clearInterval(interval);
		Jump();
	}
},1000);
</script>
</body>
</html>