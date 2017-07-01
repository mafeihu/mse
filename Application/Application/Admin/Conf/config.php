<?php
$array = array(
	//'配置项'=>'配置值'
	'quanxian'=>true,
);
$Global = require './Conf/Global_config.php';
$temp = array_merge($Global,$array);
return array_merge($temp);
?>