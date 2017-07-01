<?php
return array(

	/**
	 * 网站全局配置
	 */
	//'配置项'=>'配置值'
	   'DB_TYPE'=> 'mysql', // 数据库类型
	
		'DB_HOST'=>'139.196.178.64',
		//'DB_HOST'=> '192.168.1.2', // 数据库朋务器地址
		'DB_NAME'=>'mse', // 数据库名称
		'DB_USER'=>'root', // 数据库用户名
		'DB_PWD'=>'Zha54321', // 数据库密码
		'DB_PORT'=>'3306', // 数据库端口
		'DB_PREFIX'=>'m_', // 数据表前缀
		'DB_CHAESET'=>'utf8',


		/*'DB_CONFIG1' => array(
	    'db_type'  => 'mysql',
        'db_host'  => 'localhost',
	    'db_user'  => 'root',
	    'db_pwd'   => '123456',
	    
	    'db_port'  => '3306',
	    'db_name'  => 'mcbns',
	    ),*/
		
		'MAIL_HOST' =>'smtp.163.com',//smtp服务器的名称
		'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
		'MAIL_USERNAME' =>'mcbntest@163.com',//你的邮箱名
		'MAIL_FROM' =>'mcbntest@163.com',//发件人地址
		'MAIL_FROMNAME'=>'管理员',//发件人姓名
		'MAIL_PASSWORD' =>'abc123',//邮箱密码
		'MAIL_CHARSET' =>'utf-8',//设置邮件编码
		'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
		'MAIL_SEND_NAME'=>"管理员",//发送者名称
		'MAIL_SEND_TITLE_active'=>"XX网邮箱激活",//发送激活邮件时候的标题
		'MAIL_SEND_TITLE_sendcode'=>"XX网邮件验证码",//发送邮件验证码时候的标题
		'MAIL_SEND_JIANGE'=>"20",//邮件发送间隔，单位秒
		'MAIL_SEND_SHIXIAO'=>"1800",//邮件uuid生效时间
		
	
	'TOKEN_ON'=>false,  
	'HTML_CACHE_ON'     =>  false,
	'HTML_CACHE_TIME'   =>  60,
	'HTML_CACHE_RULES'  =>  array(),
		
	'__PUBLIC__'=>"./Public",	
		
	'URL_MODEL' => 2,
	'URL_HTML_SUFFIX'=>'html',
    'IMG_PREFIX'	=> 'http://mse.tstmobile.com'
		
);
?>