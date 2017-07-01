<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml">
<head>
<META content="text/html; charset=utf-8" http-equiv=Content-Type />
<TITLE>后台管理中心</TITLE>

<script type="text/javascript" src="/Public/admin/js/jquery.js"></script>
<STYLE>
BODY {
	BACKGROUND-COLOR: #E6EDF2;
	MARGIN: 0px;
	FONT: 12px Arial, Helvetica, sans-serif;
	COLOR: #000;
	overflow-x : hidden;   
}

#container {
	WIDTH: 182px;
	margin-left: 1px;
}

H1 {
	LINE-HEIGHT: 20px;
	MARGIN: 0px;
	WIDTH: 182px;
	HEIGHT: 30px;
	FONT-SIZE: 12px;
	CURSOR: pointer
}

H1 A {
	BACKGROUND-IMAGE: url(/Public/admin/images/menu_bgS.gif);
	TEXT-ALIGN: center;
	PADDING-BOTTOM: 0px;
	LINE-HEIGHT: 30px;
	MARGIN: 0px;
	PADDING-LEFT: 0px;
	WIDTH: 182px;
	PADDING-RIGHT: 0px;
	DISPLAY: block;
	BACKGROUND-REPEAT: no-repeat;
	HEIGHT: 30px;
	COLOR: #000;
	TEXT-DECORATION: none;
	PADDING-TOP: 0px;
	moz-outline-style: none
}

.content {
	WIDTH: 182px;
	overflow: hidden;
}

.MM UL {
	PADDING-BOTTOM: 0px;
	LIST-STYLE-TYPE: none;
	MARGIN: 0px;
	PADDING-LEFT: 0px;
	PADDING-RIGHT: 0px;
	DISPLAY: block;
	PADDING-TOP: 0px
}

.MM LI {
	LINE-HEIGHT: 26px;
	LIST-STYLE-TYPE: none;
	PADDING-LEFT: 0px;
	WIDTH: 182px;
	DISPLAY: block;
	FONT-FAMILY: Arial, Helvetica, sans-serif;
	HEIGHT: 26px;
	COLOR: #333333;
	FONT-SIZE: 12px;
	TEXT-DECORATION: none
}

.MM {
	PADDING-BOTTOM: 0px;
	MARGIN: 0px;
	PADDING-LEFT: 0px;
	WIDTH: 182px;
	BOTTOM: 0px;
	PADDING-RIGHT: 0px;
	TOP: 0px;
	RIGHT: 0px;
	PADDING-TOP: 0px;
	LEFT: 0px
}

.MM A:link {
	BACKGROUND-IMAGE: url(/Public/admin/images/menu_bg1.gif);
	TEXT-ALIGN: center;
	PADDING-BOTTOM: 0px;
	LINE-HEIGHT: 26px;
	MARGIN: 0px;
	PADDING-LEFT: 0px;
	WIDTH: 182px;
	PADDING-RIGHT: 0px;
	DISPLAY: block;
	BACKGROUND-REPEAT: no-repeat;
	FONT-FAMILY: Arial, Helvetica, sans-serif;
	HEIGHT: 26px;
	COLOR: #333333;
	FONT-SIZE: 12px;
	OVERFLOW: hidden;
	TEXT-DECORATION: none;
	PADDING-TOP: 0px
}

.MM A:visited {
	BACKGROUND-IMAGE: url(/Public/admin/images/menu_bg1.gif);
	TEXT-ALIGN: center;
	PADDING-BOTTOM: 0px;
	LINE-HEIGHT: 26px;
	MARGIN: 0px;
	PADDING-LEFT: 0px;
	WIDTH: 182px;
	PADDING-RIGHT: 0px;
	DISPLAY: block;
	BACKGROUND-REPEAT: no-repeat;
	FONT-FAMILY: Arial, Helvetica, sans-serif;
	HEIGHT: 26px;
	COLOR: #333333;
	FONT-SIZE: 12px;
	TEXT-DECORATION: none;
	PADDING-TOP: 0px
}

.MM A:active {
	BACKGROUND-IMAGE: url(/Public/admin/images/menu_bg1.gif);
	TEXT-ALIGN: center;
	PADDING-BOTTOM: 0px;
	LINE-HEIGHT: 26px;
	MARGIN: 0px;
	PADDING-LEFT: 0px;
	WIDTH: 182px;
	PADDING-RIGHT: 0px;
	DISPLAY: block;
	BACKGROUND-REPEAT: no-repeat;
	FONT-FAMILY: Arial, Helvetica, sans-serif;
	HEIGHT: 26px;
	COLOR: #333333;
	FONT-SIZE: 12px;
	OVERFLOW: hidden;
	TEXT-DECORATION: none;
	PADDING-TOP: 0px
}

.MM A:hover {
	BACKGROUND-IMAGE: url(/Public/admin/images/menu_bg2.gif);
	TEXT-ALIGN: center;
	PADDING-BOTTOM: 0px;
	LINE-HEIGHT: 26px;
	MARGIN: 0px;
	PADDING-LEFT: 0px;
	WIDTH: 182px;
	PADDING-RIGHT: 0px;
	DISPLAY: block;
	BACKGROUND-REPEAT: no-repeat;
	FONT-FAMILY: Arial, Helvetica, sans-serif;
	HEIGHT: 26px;
	COLOR: #cc0000;
	FONT-SIZE: 12px;
	FONT-WEIGHT: bold;
	TEXT-DECORATION: none;
	PADDING-TOP: 0px
}

.MM A.on {
	BACKGROUND-IMAGE: url(/Public/admin/images/menu_bg2.gif);
	TEXT-ALIGN: center;
	PADDING-BOTTOM: 0px;
	LINE-HEIGHT: 26px;
	MARGIN: 0px;
	PADDING-LEFT: 0px;
	WIDTH: 182px;
	PADDING-RIGHT: 0px;
	DISPLAY: block;
	BACKGROUND-REPEAT: no-repeat;
	FONT-FAMILY: Arial, Helvetica, sans-serif;
	HEIGHT: 26px;
	COLOR: #cc0000;
	FONT-SIZE: 12px;
	FONT-WEIGHT: bold;
	TEXT-DECORATION: none;
	PADDING-TOP: 0px
}
</STYLE>

</head>
<body>
	<div id="container">
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="menu_mcbn">
			<H1 class="type">
				<A href="javascript:void(0)"><?php echo ($row["title"]); ?></A>
			</H1>
			<DIV class="content">
				<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%">
					<TR>
						<TD><IMG src="/Public/admin/images/menu_topline.gif"
							width="182" height="5"></TD>
					</TR>
				</TABLE>
				<UL class=MM>
					<?php if(is_array($row['xjmenus'])): $i = 0; $__LIST__ = $row['xjmenus'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row2): $mod = ($i % 2 );++$i;?><LI><A href="<?php echo ($row2["url"]); ?>/ids/<?php echo ($row2["id"]); ?>" target="main"><?php echo ($row2["title"]); ?></A></LI><?php endforeach; endif; else: echo "" ;endif; ?>
				</UL>
			</DIV>


		</div><?php endforeach; endif; else: echo "" ;endif; ?>

	</div>

	<script>
		var con_height_min = "5px";
		$(".type").click(function() {
			var con = $(this).parent().find(".content");
			if (con.css("height") == con_height_min) {
				con.animate({
					height : (con.find(".MM").find("li").length * 28+5) + "px"
				});
			} else {
				con.animate({
					height : con_height_min
				});
			}

		});
	</script>
</BODY>
</HTML>