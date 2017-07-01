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
	 		'folder'         : '/Public/admin/Uploads/touxiang',
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
<form name="form" action="<?php echo U('doadd');?>"  method="post" onsubmit="return checkss();">
    <div style="width:100%;margin-top: 30px;">
        <div style="width:100%;height:160px;float:left">
            <div style="float:left;height:150px;width:113px"><img src="<?php echo ($view["img"]); ?>" style="width:113px;height:111px;margin-bottom:10px"><span style="padding-left:15px"><?php echo ($mem["nickname"]); ?></span>&nbsp;&nbsp;<span><?php if($mem["sex"] == 1): ?>女<?php elseif($mem["sex"] == 2): ?>男<?php else: endif; ?></span></div>
            <div style="float:left;padding-left:50px">
                <ul style="line-height:30px">
                    <li>昵称：<?php echo ($view["username"]); ?></li>
                    <li>性别：<?php echo ($view['sex'] == 1 ? '男' : ($view['sex'] == 2 ? '女' : '未知')); ?></li>
                    <li>账号：<?php echo ($view["phone"]); ?></li>
                    <li>地址：<?php echo ($view["province"]); echo ($view["city"]); echo ($view["area"]); ?>&nbsp;<?php echo ($view["address"]); ?></li>
                </ul>
            </div>
            <div style="float:left;padding-left:50px">
                <ul style="line-height:30px">
                    <li>账户余额：<?php echo ((isset($view['money']) && ($view['money'] !== ""))?($view['money']):0); ?> </li>
                    <li>收益：<?php echo ((isset($view["get_money"]) && ($view["get_money"] !== ""))?($view["get_money"]):0); ?> </li>
                    <li>总消费：<?php echo ((isset($view['xiaofei']) && ($view['xiaofei'] !== ""))?($view['xiaofei']):0); ?> </li>
                    <li>已提现总额：<?php echo ((isset($view["withdraw_count"]) && ($view["withdraw_count"] !== ""))?($view["withdraw_count"]):0); ?> </li>
                </ul>
            </div>
        </div>
    </div>
    <div style="align-content: center;">
        <li>php_sdk：<?php echo ($user_live["php_sdk"]); ?></li>
        <li>play_rtmp：<?php echo ($user_live["play_rtmp"]); ?></li>
    </div>
    <div class="tab">
        <div class="tab_menu">
            <ul>
                <!--<li class="selected">时事</li>-->
                <!--<li>体育</li>-->
                <!--<li>娱乐</li>-->
                <a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>1));?>"><li <?php if( $state == 1 ): ?>class="selected"<?php else: endif; ?>>充值记录</li></a>
                <!--<a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>2));?>"><li <?php if( $state == 2 ): ?>class="selected"<?php else: endif; ?>>兑换记录</li></a>-->
                <a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>3));?>"><li <?php if( $state == 3 ): ?>class="selected"<?php else: endif; ?>>提现记录</li></a>
                <a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>4));?>"><li <?php if( $state == 4 ): ?>class="selected"<?php else: endif; ?>>收益记录</li></a>
                <a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>5));?>"><li <?php if( $state == 5 ): ?>class="selected"<?php else: endif; ?>>消费记录</li></a>
                <a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>6));?>"><li <?php if( $state == 6 ): ?>class="selected"<?php else: endif; ?>>关注列表</li></a>
                <a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>7));?>"><li <?php if( $state == 7 ): ?>class="selected"<?php else: endif; ?>>粉丝列表</li></a>
                <a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>8));?>"><li <?php if( $state == 8 ): ?>class="selected"<?php else: endif; ?>>直播列表</li></a>
                <a href="<?php echo U('details',array('id'=>$view['user_id'],'state'=>9));?>"><li <?php if( $state == 9 ): ?>class="selected"<?php else: endif; ?>>录播列表</li></a>
            </ul>
        </div>
        <div class="tab_box">
            <div <?php if( $state != 1 ): ?>class="hide"<?php endif; ?>>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
                    <tr class="tabTitleMain">
                        <td width="15%" align="center">充值编号</td>
                        <td width="10%" align="center">金额</td>
                        <td width="10%" align="center">平米</td>
                        <td width="12%" align="center">时间</td>
                        <td width="10%" align="center">支付类型</td>
                    </tr>


                    <?php if(is_array($re)): $i = 0; $__LIST__ = $re;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                            <td align="center" class="onerow"><?php echo ($l["pay_number"]); ?></td>
                            <td align="center" class="onerow"><?php echo ($l["amount"]); ?></td>
                            <td align="center" class="onerow"><?php echo ($l["meters"]); ?></td>
                            <td align="center" class="onerow"><?php echo (date("Y-m-d H:i",$l["intime"])); ?></td>
                            <td align="center" class="onerow"><?php echo ($l["pay_type"]); ?></td>

                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
            <div class="pages"><?php echo ($show); ?></div>
            </div>
            <div <?php if( $state != 2 ): ?>class="hide"<?php endif; ?>>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
                    <tr class="tabTitleMain">
                        <td width="15%" align="center">兑换的k币</td>
                        <td width="10%" align="center">兑换平米数</td>
                        <td width="12%" align="center">时间</td>
                    </tr>


                    <?php if(is_array($con)): $i = 0; $__LIST__ = $con;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                            <td align="center" class="onerow"><?php echo ($l["k"]); ?></td>
                            <td align="center" class="onerow"><?php echo ($l["meters"]); ?></td>
                            <td align="center" class="onerow"><?php echo (date("Y-m-d H:i",$l["intime"])); ?></td>

                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
                <div class="pages"><?php echo ($show); ?></div>
            </div>
            <div <?php if( $state != 3 ): ?>class="hide"<?php endif; ?>>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
                    <tr class="tabTitleMain">
                        <td width="15%" align="center">提现K币</td>
                        <td width="10%" align="center">金额</td>
                        <td width="10%" align="center">账户</td>
                        <td width="10%" align="center">类型</td>
                        <td width="10%" align="center">状态</td>
                        <td width="12%" align="center">申请时间</td>
                    </tr>


                    <?php if(is_array($w)): $i = 0; $__LIST__ = $w;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                            <td align="center" class="onerow"><?php echo ($l["k"]); ?></td>
                            <td align="center" class="onerow"><?php echo ($l["money"]); ?></td>
                            <td align="center" class="onerow"><?php echo ($l["withdraw_way"]); ?></td>
                            <td align="center" class="onerow"><?php echo ($l["withdraw_type"]); ?></td>
                            <td align="center" class="onerow"><?php if( $l["status"] == 1 ): ?>等待审核<?php elseif( $l["status"] == 2 ): ?>驳回<?php else: ?>已返现<?php endif; ?></td>
                            <td align="center" class="onerow"><?php echo (date("Y-m-d H:i",$l["intime"])); ?></td>

                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
                <div class="pages"><?php echo ($show); ?></div>
            </div>
            <div <?php if( $state != 4 ): ?>class="hide"<?php endif; ?>>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
                <tr class="tabTitleMain">
                    <td width="15%" align="center">直播标题</td>
                    <td width="10%" align="center">用户</td>
                    <td width="10%" align="center">礼物</td>
                    <td width="10%" align="center">价格</td>
                    <td width="12%" align="center">时间</td>
                </tr>

                <?php if(is_array($g)): $i = 0; $__LIST__ = $g;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                        <td align="center" class="onerow"><?php echo ($l["title"]); ?></td>
                        <td align="center" class="onerow"><?php echo ($l["username"]); ?></td>
                        <td align="center" class="onerow"><?php echo ($l["name"]); ?></td>
                        <td align="center" class="onerow"><?php echo ($l["jewel"]); ?></td>
                        <td align="center" class="onerow"><?php echo (date("Y-m-d H:i",$l["intime"])); ?></td>

                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </table>
            <div class="pages"><?php echo ($show); ?></div>
        </div>
        <div <?php if( $state != 5 ): ?>class="hide"<?php endif; ?>>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
            <tr class="tabTitleMain">
                <td width="15%" align="center">直播标题</td>
                <td width="10%" align="center">主播</td>
                <td width="10%" align="center">礼物</td>
                <td width="10%" align="center">价格</td>
                <td width="12%" align="center">时间</td>
            </tr>


            <?php if(is_array($give)): $i = 0; $__LIST__ = $give;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                    <td align="center" class="onerow"><?php echo ($l["title"]); ?></td>
                    <td align="center" class="onerow"><?php echo ($l["username"]); ?></td>
                    <td align="center" class="onerow"><?php echo ($l["name"]); ?></td>
                    <td align="center" class="onerow"><?php echo ($l["jewel"]); ?></td>
                    <td align="center" class="onerow"><?php echo (date("Y-m-d H:i",$l["intime"])); ?></td>

                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
        <div class="pages"><?php echo ($show); ?></div>
        </div>
        <div <?php if( $state != 6 ): ?>class="hide"<?php endif; ?>>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
            <tr class="tabTitleMain">
                <td width="15%" align="center">被关注用户</td>
                <td width="12%" align="center">关注时间</td>
            </tr>


            <?php if(is_array($f)): $i = 0; $__LIST__ = $f;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                    <td align="center" class="onerow"><?php echo ($l["username"]); ?>(员工编号:<?php echo ($l["employee_id"]); ?>)</td>
                    <td align="center" class="onerow"><?php echo (date("Y-m-d H:i",$l["intime"])); ?></td>

                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
        <div class="pages"><?php echo ($show); ?></div>
        </div>
        <div <?php if( $state != 7 ): ?>class="hide"<?php endif; ?>>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
            <tr class="tabTitleMain">
                <td width="15%" align="center">关注用户</td>
                <td width="12%" align="center">关注时间</td>
            </tr>


            <?php if(is_array($fo)): $i = 0; $__LIST__ = $fo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                    <td align="center" class="onerow"><?php echo ($l["username"]); ?>(员工编号:<?php echo ($l["employee_id"]); ?>)</td>
                    <td align="center" class="onerow"><?php echo (date("Y-m-d H:i",$l["intime"])); ?></td>

                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
        <div class="pages"><?php echo ($show); ?></div>
        </div>
        <div <?php if( $state != 8 ): ?>class="hide"<?php endif; ?>>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
            <tr class="tabTitleMain">
                <td width="8%" align="center">标题</td>
                <td width="8%" align="center">封面</td>
                <td width="10%" align="center">开始时间</td>
                <td width="10%" align="center">结束时间</td>
                <td width="7%" align="center">总人数</td>
                <td width="7%" align="center">观看人数</td>
                <td width="7%" align="center">点亮数</td>
                <td width="7%" align="center">收礼</td>
                <td width="10%" align="center">操作</td>
            </tr>


            <?php if(is_array($live)): $i = 0; $__LIST__ = $live;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                    <td align="center" class="onerow"><?php echo ($l["title"]); ?></td>
                    <td align="center" class="onerow"><img src="<?php echo ($l["play_img"]); ?>" style="width: 50px;height: 50px;border-radius:50%"></td>
                    <td align="center" class="onerow"><?php echo (date("Y-m-d H:i:s",$l["start_time"])); ?></td>
                    <td align="center" class="onerow"><?php if( $l["end_time"] == 0 ): else: echo (date("Y-m-d H:i:s",$l["end_time"])); endif; ?></td>
                    <td align="center" class="onerow"><?php echo ($l["nums"]); ?></td>
                    <td align="center" class="onerow"><?php echo ($l["watch_nums"]); ?></td>
                    <td align="center" class="onerow"><?php echo ($l["light_up_count"]); ?></td>
                    <td align="center" class="onerow"><?php echo ($l["gift_count"]); ?></td>
                    <td align="center">
                        <?php if( $l["live_status"] == 1 ): ?><a href="javascript:;" onclick="offline(<?php echo ($l["live_id"]); ?>);" style="color: #0a83cd">强制下线</a><?php else: ?>已结束<?php endif; ?>
                    </td>

                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
        <div class="pages"><?php echo ($show); ?></div>
        </div>
        <div <?php if( $state != 9 ): ?>class="hide"<?php endif; ?>>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="">
            <tr class="tabTitleMain">
                <td width="8%" align="center">封面</td>
                <td width="30%" align="center">地址</td>
                <td width="10%" align="center">时间</td>
                <td width="15%" align="center">操作</td>
            </tr>


            <?php if(is_array($live_store)): $i = 0; $__LIST__ = $live_store;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$l): $mod = ($i % 2 );++$i;?><tr class="tabTextMain" id="f_<?php echo ($l["id"]); ?>" onmouseout="this.style.background='#FFFFFF';" onmouseover="this.style.background='#fbf435';">
                    <td align="center" class="onerow"><img src="<?php echo ($l["play_img"]); ?>" style="width: 50px;height: 50px;border-radius:50%"></td>
                    <td align="center" class="onerow"><?php echo ($l["url"]); ?></td>
                    <td align="center" class="onerow"><?php echo (date("Y-m-d H:i:s",$l["intime"])); ?></td>
                    <td align="center">
                        <a href="javascript:;" onclick="sel(<?php echo ($l["live_store_id"]); ?>);" style="color: #0a83cd">查看</a>
                        |&nbsp;&nbsp;
                        <a href="javascript:;"  onclick="del(<?php echo ($l["live_store_id"]); ?>);" style="color: #0a83cd">删除</a>

                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
        <div class="pages"><?php echo ($show); ?></div>
        </div>
        </div>
    </div>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="infoBoxTd">&nbsp;</td>
<td align="left"><input type="" name="button" onclick="javascript:window.location.href='<?php echo U('index');?>'" value="返回" class="formInput01" /></td>
</tr>
</table>
</form>
    <style>
        .tab { width:100%;}
        .tab_menu { clear:both;}
        .tab_menu li { width:8%;float:left; text-align:center; cursor:pointer; list-style:none;  margin-right:4px; background:#F1F1F1; border:1px solid #898989; border-bottom:none;padding:1px 6px;}
        .tab_menu li.hover { background:#DFDFDF;}
        .tab_menu li.selected { color:#FFF; background:#6D84B4;}
        .tab_menu li.selected  a{ color:#fff}
        .tab_box { clear:both; border:1px solid #898989;}
        .hide{display:none}
        .tab_menu li a#current{}
    </style>
    <script type="text/javascript" >
        //<![CDATA[
        $(function(){
            var $div_li =$("div.tab_menu ul li");
            $div_li.click(function(){
                $(this).addClass("selected")            //当前<li>元素高亮
                        .siblings().removeClass("selected");  //去掉其它同辈<li>元素的高亮
                var index =  $div_li.index(this);  // 获取当前点击的<li>元素 在 全部li元素中的索引。
                $("div.tab_box > div")   	//选取子节点。不选取子节点的话，会引起错误。如果里面还有div
                        .eq(index).show()   //显示 <li>元素对应的<div>元素
                        .siblings().hide(); //隐藏其它几个同辈的<div>元素
            })
        })
        //]]>
    </script>
</div>
<script>
    function sel(id) {
        layer.open({
            type: 2,
            title: false,
            area: ['1020px', '587px'],
            shade: 0.1,
            closeBtn: 1,
            shadeClose: false,
            content: "/Admin/User/play?id="+id,
        });
    }
    function offline(id) {
        if(!confirm('确定强制下线？'))
            return false;
        $.post("<?php echo U('Live/offline');?>", {id:id}, function(v){
            if( v == 1 ){
                alert('已强制下线！');
                location.reload("<?php echo U('details',array('id'=>$view['user_id'],'state'=>9));?>");
            }else{
                alert('强制下线失败！');
            }
        });
    }
    function del(kid){
        kid = kid ? kid : getChecked();
        kid = kid.toString();
        if(kid == ''){
            alert("请选择要删除的选项");
            return false;
        }
        if(!confirm('确定删除？'))
            return false;
        $.post("<?php echo U('Live/del');?>", {ids:kid}, function(v){
            if( v == 1 ){
                alert('删除成功！');
                location.reload("<?php echo U('details',array('id'=>$view['user_id'],'state'=>9));?>");
            }else{
                alert('删除失败！');
            }
        });
    }
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