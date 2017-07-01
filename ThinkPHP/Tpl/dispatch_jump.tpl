<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='Refresh' content='{$waitSecond};URL={$jumpUrl}'>
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body {background-color: #f1f1f1; margin: 0px; padding-bottom:50px; font-size:14px; line-height:25px;font-family: '微软雅黑'; }
.Prompt_top, .Prompt_btm, .Prompt_ok, .Prompt_x { background:url(__PUBLIC__/admin/images/message.gif) no-repeat; display:inline-block }
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
    window.location.href = '{$jumpUrl}';
}
</script>
</head>
<body>
<eq name="status" value="1">
  <div class="Prompt">
  <div class="Prompt_top"></div>
  <div class="Prompt_con">
    <dl>
      <dt>提示信息</dt>
      <dd><span class="Prompt_ok"></span></dd>
      <dd>
        <h2 style="color:black">{$message}</h2>
        <present name="closeWin" >
          <p>系统将在 <span style="color:blue;font-weight:bold" id="second">{$waitSecond}</span> 秒后自动关闭，如果不想等待,直接点击 <A HREF="{$jumpUrl}"><font color="red">这里</font></A> 关闭</p>
        </present>
        <notpresent name="closeWin" >
          <p>系统将在 <span style="color:blue;font-weight:bold" id="second">{$waitSecond}</span> 秒后自动跳转,如果不想等待,直接点击 <A HREF="{$jumpUrl}"><font color="red">这里</font></A> 跳转<br/>
            或者 <a href="__ROOT__/">返回首页</a></p>
        </notpresent>
      </dd>
    </dl>
    <div class="c"></div>
    </div>
    <div class="Prompt_btm"></div>
  </div>
</eq>
<eq name="status" value="0">
  <div class="Prompt">
    <div class="Prompt_top"></div>
  <div class="Prompt_con">
    <dl>
      <dt>提示信息</dt>
      <dd><span class="Prompt_x"></span></dd>
      <dd>
      <h2 style="color:red">{$error}</h2>
        <present name="closeWin" >
      <p>系统将在 <span style="color:blue;font-weight:bold" id="second">{$waitSecond}</span> 秒后自动关闭，如果不想等待,直接点击 <A HREF="{$jumpUrl}"><font color="red">这里</font></A> 关闭</p>
      </present>
      <notpresent name="closeWin" >
        <p>系统将在 <span style="color:blue;font-weight:bold" id="second">{$waitSecond}</span> 秒后自动跳转,如果不想等待,直接点击 <A HREF="{$jumpUrl}"><font color="red">这里</font></A> 跳转<br/>
          或者 <a href="__ROOT__/">返回首页</a></p>
      </notpresent>
      </dd>
    </dl>
    <div class="c"></div>
    </div>
    <div class="Prompt_btm"></div>
  </div>
</eq>
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