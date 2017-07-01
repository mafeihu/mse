<?php
/**
 * 发送验证邮箱的邮件
 */
function sendActiveEmail($email) {
	$uuid = getUUIDFromMysql ();
	
	$U = D ( "Users" );
	
	$where ["email"] = $email;
	$user = $U->where ( $where )->find ();
	if (! $user) {
		$msg ["sta"] = 2;
		$msg ["msg"] = "邮箱不存在";
		return $msg;
	}
	if ($user ["esta"] == 1) {
		$msg ["sta"] = 2;
		$msg ["msg"] = "邮箱已激活，可以直接登录";
		return $msg;
	}
	$name = $user ["username"];
	if ($name == "") {
		$name = "无名";
	}
	
	$cre = getCheckcheck ( $email, $uuid );
	
	$allre = $cre && $name && $uuid;
	if ($allre) {
		$modeldata ["name"] = $name;
		$modeldata ["url"] = U ( "active", "email=" . $email . "&uuid=" . $uuid );
		$remail = sendSimpleEmail ( $email, C ( "MAIL_SEND_TITLE_active" ), getEmailContent ( "active", $modeldata ) );
	} else {
		$msg ["sta"] = 2;
		$msg ["msg"] = "系统繁忙，请再试";
		return $msg;
	}
	return $remail;
}
/**
 * 获取链接失效时间.以秒计数
 */
function getOutDateTime() {
	return 30 * 60;
}
/**
 * 时效汉字说明
 */
function getShiXiao() {
	$time = getOutDateTime ();
	if ($time < 60) {
		return $time . "秒";
	}
	if ($time >= 60 && $time < 3600) {
		return floor ( $time / 60 ) . "分钟";
	}
	if ($time >= 3600 && $time < 3600 * 24) {
		return floor ( $time / 3600 ) . "小时";
	}
	if ($time >= 3600 * 24 && $time < 3600 * 24 * 365) {
		return floor ( $time / 3600 / 24 ) . "天";
	}
}

// 发送邮件
/*
 * $address:邮箱 $revname：昵称 $title：标题 $content：发送内容
 */
function sendemail111($address, $sendname, $revname, $title, $content) {
	import ( "ORG.Net.PHPMailer" );
	$mail = new phpmailer ();
	$mail->IsSMTP (); // send via SMTP phperz~com
	$mail->Host = "smtp.qq.com"; // SMTP servers
	$mail->SMTPAuth = true; // turn on SMTP authentication
	$mail->Username = "2409374308"; // SMTP username 注意：普通邮件认证不需要加 @域名​
	$mail->Password = "mingchuang8765"; // SMTP password
	$mail->From = "2409374308@qq.com";
	$mail->CharSet = 'UTF-8';
	$mail->FromName = $sendname;
	$mail->AddAddress ( $address, $revname );
	$mail->Subject = $title; // 标题
	$mail->Body = html_entity_decode ( $content, ENT_NOQUOTES );
	$mail->ishtml ( true );
	// $mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
	if (! $mail->Send ()) {
		echo "邮件发送失败. <p>";
		echo "错误原因: " . $mail->ErrorInfo;
		exit ();
	}
	// echo "邮件发送成功";
}

/**
 * 邮箱激活操作
 */
function EmailActivive($userid) {
	$U = D ( "Users" );
	
	$where ["id"] = $userid;
	
	$user = $U->where ( $where )->find ();
	if ($user ["esta"] == 1) {
		$msg ["sta"] = 2;
		$msg ["msg"] = "邮箱已激活";
		return $msg;
	}
	
	$data ["esta"] = 1;
	
	$r = $U->where ( $where )->save ( $data );
	
	if ($r) {
		$user ["esta"] = 1;
		
		session ( "user", $user );
		$msg ["sta"] = 1;
		$msg ["msg"] = "邮箱激活成功";
		return $msg;
	} else {
		$msg ["sta"] = 2;
		$msg ["msg"] = "邮箱激活失败";
		return $msg;
	}
}
/**
 * 发送简单邮件
 * 
 * @param string $email        	
 * @param string $title        	
 * @param string $content        	
 * @return array $data["sta"]=1; $data["sta"]=2;
 *         $data["msg"]="发送失败".$r;
 */
function sendSimpleEmail($email, $title, $content) {
	Vendor ( 'PHPMailer.PHPMailerAutoload' );
	$mail = new PHPMailer (); // 实例化
	$mail->IsSMTP (); // 启用SMTP
	$mail->Host = C ( 'MAIL_HOST' ); // smtp服务器的名称（这里以QQ邮箱为例）
	$mail->SMTPAuth = C ( 'MAIL_SMTPAUTH' ); // 启用smtp认证
	$mail->Username = C ( 'MAIL_USERNAME' ); // 你的邮箱名
	$mail->Password = C ( 'MAIL_PASSWORD' ); // 邮箱密码
	$mail->From = C ( 'MAIL_FROM' ); // 发件人地址（也就是你的邮箱地址）
	$mail->FromName = C ( 'MAIL_SEND_NAME' ); // 发件人姓名
	$mail->AddAddress ( $email, "尊敬的客户" );
	$mail->WordWrap = 50; // 设置每行字符长度
	$mail->IsHTML ( C ( 'MAIL_ISHTML' ) ); // 是否HTML格式邮件
	$mail->CharSet = C ( 'MAIL_CHARSET' ); // 设置邮件编码
	$mail->Subject = $title; // 邮件主题
	$mail->Body = $content; // 邮件内容
	                        // $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
	$r = $mail->Send ();
	if ($r) {
		$data ["sta"] = 1;
		$data ["msg"] = "邮件已送到您的邮箱";
	} else {
		$data ["sta"] = 2;
		$data ["msg"] = "发送失败";
	}
	return $data;
}

/**
 * 修改密码的操作,并返回修改操作的返回值
 */
function changePassword($userid, $password) {
	$U = D ( "Users" );
	
	$where ["id"] = $userid;
	
	$data ["password"] = md5 ( $password );
	
	$r = $U->where ( $where )->save ( $data );
	
	return $r;
}
function changePasswordzijin($userid, $password) {
	$U = D ( "Users" );
	
	$where ["id"] = $userid;
	
	$data ["zijinpassword"] = md5 ( $password );
	
	$r = $U->where ( $where )->save ( $data );
	
	return $r;
}

/**
 * 发送者名称
 * 
 * @return string
 */
function getEmailSender() {
	return C ( "MAIL_SEND_NAME" );
}

/**
 *
 *
 * 找回密码或者验证邮箱时，邮件的内容的html
 *
 * @param string $model
 *        	要使用的模板文件名称如activeEmailContent
 *        	
 * @param array $data
 *        	要替换的变量如array('name'=>'张三')
 *        	
 * @return string
 *
 *
 */
function getEmailContent($model, $data) {
	$data ["_domain"] = getDomain (); // 根目录
	$data ["_root"] = __ROOT__; // 根目录
	$data ["_shixiao"] = getShiXiao (); // 时效
	$path = getcwd () . "/Application/App/View/EmailContent/";
	$data ["_bottom"] = file_get_contents ( $path . "bottom.html" );
	$file = file_get_contents ( $path . $model . ".html" );
	foreach ( $data as $k => $v ) {
		$file = str_replace ( "{" . "$" . $k . "}", $v, $file );
	}
	return $file;
}

/**
 *
 *
 * 获取当前域名，带http://
 *
 * @return string
 *
 *
 */
function getDomain() {
	return "http://" . str_replace ( "http://", "", $_SERVER ['SERVER_NAME'] );
}

/**
 *
 *
 * 根据邮箱获取用户id
 *
 * @return string $email 参数为登陆用的邮箱
 *        
 *        
 */
function getUseridByEmail($emai) {
	$U = D ( "Users" );
	
	$where ["email"] = $emai;
	
	return $U->where ( $where )->getField ( "id" );
}

/**
 * 根据用户id获取姓名
 *
 * string $userid 用户id
 */
function getNameByUserid($userid) {
	$U = D ( "Users" );
	
	$where ["id"] = $userid;
	
	return $U->where ( $where )->getField ( "name" );
}
/**
 * 根据用户邮箱获取姓名
 *
 * string $email 用户邮箱
 */
function getNameByEmail($email) {
	$U = D ( "Users" );
	
	$where ["email"] = $email;
	$user = $U->where ( $where )->find ();
	if ($user) {
		if ($user ["name"] != "") {
			return $user ["name"];
		}
		return "无名";
	} else {
		return false;
	}
}
/**
 * 根据用户id获取邮箱
 *
 * string $userid 用户id
 */
function getEmailByUserid($userid) {
	$U = D ( "Users" );
	
	$where ["id"] = $userid;
	
	return $U->where ( $where )->getField ( "email" );
}
/**
 * 检查邮件里的uuid和email是不是跟数据库里的匹配
 * 
 * @param string $email        	
 * @param string $uuid        	
 * @return boolean $delete 如果验证成功，是否删除数据,默认true
 */
function checkEmailUuid($email, $uuid, $delete = true) {
	if ($email == "") {
		$msg ["sta"] = 2;
		$msg ["msg"] = "缺少email参数";
		return $msg;
	}
	if ($email == "") {
		$msg ["sta"] = 2;
		$msg ["msg"] = "缺少uuid参数";
		return $msg;
	}
	$where ["email"] = $email;
	$where ["uuid"] = $uuid;
	$where ["type"] = 1;
	$where ["ip"] = get_client_ip ();
	$CC = D ( "Checkcheck" );
	$check = $CC->where ( $where )->find ();
	if (! $check) {
		$msg ["sta"] = 2;
		$msg ["msg"] = "验证码错误";
		return $msg;
	}
	if (time () - $check ["createtime"] > C ( "MAIL_SEND_SHIXIAO" )) {
		$msg ["sta"] = 2;
		$msg ["msg"] = "验证码已失效";
		return $msg;
	}
	$msg ["sta"] = 1;
	if ($delete) {
		$CC->where ( array (
				"email" => $email 
		) )->delete ();
	}
	return $msg;
}
/**
 * 指定模板发送邮件
 * 
 * @param string $email        	
 * @param string $title
 *        	邮件标题
 * @param array $modeldata
 *        	绑定数据,如果用到uuid也要用$modeldata传入(如array("name"=>"名字"，"uuid"=>"2c4102fc-d9c9-11e4-9516-00e066ab54d7"))
 * @param string $model
 *        	模板名称(/Application/App/View/EmailContent目录下面，如active)
 */
function sendModelEmail($email, $title, $modeldata, $model) {
	$uuid = $modeldata ["uuid"];
	
	$U = D ( "Users" );
	
	$where ["email"] = $email;
	$user = $U->where ( $where )->find ();
	if (! $user) {
		$msg ["sta"] = 2;
		$msg ["msg"] = "邮箱不存在";
		return $msg;
	}
	
	$name = $user ["username"];
	if ($name == "") {
		$name = "无名";
	}
	
	$cre = getCheckcheck ( $email, $uuid );
	
	$allre = $cre && $name && $uuid;
	if ($allre) {
		$remail = sendSimpleEmail ( $email, $title, getEmailContent ( $model, $modeldata ) );
	} else {
		$msg ["sta"] = 2;
		$msg ["msg"] = "系统繁忙，请再试";
		return $msg;
	}
	return $remail;
}
/**
 * 添加一条checkcheck记录，并删除之前的同名邮箱的数据
 * 
 * @param string $email        	
 * @param string $uuid        	
 * @return false或者$checkcheck
 */
function getCheckcheck($email, $uuid) {
	$CC = D ( "Checkcheck" );
	$thistime = time ();
	$cdata ["email"] = $email;
	$CC->where ( $cdata )->delete ();
	$cdata ["createtime"] = $thistime;
	$cdata ["createtimestr"] = date ( "Y-m-d H:i:s", $thistime );
	$cdata ["uuid"] = $uuid;
	$cdata ["type"] = 1;
	$cdata ["ip"] = get_client_ip ();
	$cre = $CC->add ( $cdata );
	if (! $cre) {
		return false;
	}
	$cdata ["id"] = $cre;
	return $cdata;
}


function sendMobileMessage($mobile,$content){
	$username='mingchuang';//短信用户名
	$password  ='mingchuang002'; //短信密码
	$sendto =$mobile; //接收手机号
	$message =urlencode($content);//内容解码

	$url="http://124.173.70.59:8081/SmsAndMms/mt?"; //短信发送接口地址
	$curlPost = 'Sn='.$username.'&Pwd='.$password.'&mobile='.$sendto.'&content='.$message.'';

	$ch = curl_init();//初始化curl
	curl_setopt($ch,CURLOPT_URL,$url);//抓取指定网页
	curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //允许curl提交后,网页重定向
	curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	$data = curl_exec($ch);//运行curl
	curl_close($ch);
	$stringdata = string_cut_nohtml($data, 2,"");
	if ($stringdata==0){
		$msg["sta"]=1;
	}else{
		$msg["sta"]=2;
		$msg["msg"]="发送失败";
		$msg["error"]=$data;
	}
	return $msg;
}
