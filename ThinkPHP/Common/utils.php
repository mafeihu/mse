<?php
/**
 * 根据uniqid()生成唯一序列(可能有极小的概率出现相同数据),
 * $long为重复生成次数(默认为1),并以'-'连接
 * 生成的字符串长度为$long*14-1
 * $separate为分割符,默认为'-'
 */

function getUUID($long = 1, $separate = "-") {
	for($i = 0; $i < $long; $i ++) {
		$UUID [$i] = uniqid ("uu");
	}
	return implode ( $separate, $UUID );
}
function getUUIDFromMysql(){
	$uuidarray = D("User")->query("SELECT UUID() as uuid");
	if($uuidarray){
		return $uuidarray[0]["uuid"];
	}
	return false;
}
function getUUIDOfString(){
	$string = getUUIDFromMysql();
	$string = str_replace("-", "", $string);
	return $string;
}
function countArray($array) {
	if ($array == false) {
		return 0;
	} else {
		return count ( $array );
	}
}
/**
 * 字符串切割工具
 *
 * @param unknown $str_cut
 * @param unknown $length
 * @return string
 */
function string_cut($str, $n, $s = "...") {
	$length = mb_strlen ( $str, 'UTF8' );
	if ($n < $length) {
		return mb_substr ( $str, 0, $n, 'UTF-8' ) . $s;
	} else {
		return mb_substr ( $str, 0, $n, 'UTF-8' );
	}
}
/**
 *字符串切割工具,
 */
function string_cut_nohtml($str, $n,$s="..."){
	$str=html_entity_decode($str); 
	$str = strip_tags ( $str);
	$str = html($str,1);
	$str =  string_cut ($str , $n );
	//$str =  string_cut ( preg_replace ( "/\s/", "", $str ), $n );
	return string_cut($str, $n,$s);

}
function string_keyword($string, $keyword, $before = 80, $after = 80) {
	// $string = preg_replace("/\s/", "", $string);
	$length = mb_strlen ( $string, 'UTF8' );
	$pos = mb_strpos ( $string, $keyword, 0, "utf-8" );
	if ($pos - $before > 0 && $pos + $after < $length) {
		return "..." . mb_substr ( $string, $pos - $before, $before + $after, 'UTF-8' ) . "...";
	}
	if ($pos - $before < 0 && $pos + $after < $length) {
		return mb_substr ( $string, 0, $before + $after, 'UTF-8' ) . "...";
	}
	if ($pos - $before > 0 && $pos + $after > $length) {

		$before3 = $length - $after;
		if ($before3 < 0) {
			$before3 = 0;
		}
		return "..." . mb_substr ( $string, $before3, $before + $after, 'UTF-8' );
	}
	return $string;
}
function getPageTheme() {
	return "%first%  %upPage%   %prePage%  %linkPage%  %nextPage%  %downPage% %end%";
}
/**
 * 获取当前日期,或者以参数推后或者提交几天
 * 如果$laterday=-1表示昨天的日期
 * 如果$laterday=1表示明天的日期
 */
function getCurrentDate($laterday = 0) {
	$laterday = $laterday / 1;
	$date = date ( "Y-m-d", time () + 60 * 60 * 24 * $laterday );
	return $date;
}
/**
 * 获取当前日期格式为2014-05-06 18:12:00
 */
function getCurrentTime($latersecond = 0) {
	$latersecond = $latersecond / 1;
	$date = date ( "Y-m-d H:i:s", time () + $latersecond );
	return $date;
}
/**
 * 上传文件,反回文件数组
 * array("sta"=>false/true,"msg"=>"错误信息","files"=>array("savename"))
 *
 * $floder 例如 images/touxiang
 * $allow = array（"jpg","png"）
 */
function getUploadFiles($floder, $allow = array()) {
	if (! empty ( $_FILES )) {
		import ( "Org.Net.UploadFile" );
		$config = array (
				'saveRule' => 'time'
		);
		$config ["allowExts"] = $allow;
		// $config ["maxSize"] = $maxSize;
		$config ["savePath"] = './Public/upload/' . $floder . "/";
		// $msgtype = "上传成功 ";
		$upload = new UploadFile ( $config );
		$upload->imageClassPath = "Org.Util.Image";
		$upload->thumb = false;
		if (! $upload->upload ()) {
			$msg ["sta"] = 2;
			$msg ["msg"] = $upload->getErrorMsg ();
		} else {
			$info = $upload->getUploadFileInfo ();
			$key=array();
			for($i = 0; $i < countArray ( $info ); $i ++) {
				$key[$info[$i]["key"]]=$info[$i];
			}
			$msg ["sta"] = 1;
			$msg ["files"] = $info;
			$msg ["key"] = $key;
		}
		return $msg;
	} else {
		$msg ["sta"] = 2;
		$msg ["msg"] = "没有选择上传文件";
		return $msg;
	}
}
/**
 * 取得两个日期(Y-m-d)相差的天数$fromdate减$todate
 *
 * @param unknown $fromdate
 *        	开始的天数
 * @param unknown $todate
 *        	结束的天数
 * @return number $gt0 返回值是否取自然数(非负),true为取自然数
 */
function getDays($fromdate, $todate, $gt0 = true) {
	if (empty ( $todate )) {
		$todate = date ( "Y-m-d" );
	}
	$Date_List_a1 = explode ( "-", $fromdate );

	$Date_List_a2 = explode ( "-", $todate );

	$d1 = mktime ( 0, 0, 0, $Date_List_a1 [1], $Date_List_a1 [2], $Date_List_a1 [0] );

	$d2 = mktime ( 0, 0, 0, $Date_List_a2 [1], $Date_List_a2 [2], $Date_List_a2 [0] );

	$Days = round ( ($d1 - $d2) / 3600 / 24 );

	if ($gt0 && $Days < 0) {
		$Days = $Days - 2 * $Days;
	}
	return $Days;
}
/**
 * 取得两个日期(Y-m-d)相差的天数$fromdate减$todate
 *
 * @param unknown $fromdate
 *        	开始的天数
 * @param unknown $todate
 *        	结束的天数
 * @return number $gt0 返回值是否取自然数(非负),true为取自然数
 */
function getDaysFromDayToDay($fromdate, $todate, $gt0 = true) {
	$fromdates = explode ( " ", $fromdate );
	$fromdate = $fromdates [0];
	$todates = explode ( " ", $todate );
	$todate = $todates [0];
	if (empty ( $todate )) {
		$todate = date ( "Y-m-d" );
	}
	$Date_List_a1 = explode ( "-", $fromdate );

	$Date_List_a2 = explode ( "-", $todate );

	$d1 = mktime ( 0, 0, 0, $Date_List_a1 [1], $Date_List_a1 [2], $Date_List_a1 [0] );

	$d2 = mktime ( 0, 0, 0, $Date_List_a2 [1], $Date_List_a2 [2], $Date_List_a2 [0] );

	$Days = round ( ($d1 - $d2) / 3600 / 24 );

	if ($gt0 && $Days < 0) {
		$Days = $Days - 2 * $Days;
	}
	return $Days;
}
/**
 * 取得两个日期(Y-m-d)相差的天数$fromdate减$todate
 *
 * @param unknown $fromdate
 *        	开始的天数
 * @param unknown $todate
 *        	结束的天数
 * @return number $gt0 返回值是否取自然数(非负),true为取自然数
 */
function getRestDays($fromdate) {
	$fromdates = explode ( " ", $fromdate );
	$fromdate = $fromdates [0];
	$todate = getCurrentDate ();
	$Date_List_a1 = explode ( "-", $fromdate );

	$Date_List_a2 = explode ( "-", $todate );

	$d1 = mktime ( 0, 0, 0, $Date_List_a1 [1], $Date_List_a1 [2], $Date_List_a1 [0] );

	$d2 = mktime ( 0, 0, 0, $Date_List_a2 [1], $Date_List_a2 [2], $Date_List_a2 [0] );

	$Days = round ( ($d1 - $d2) / 3600 / 24 );

	if ($Days < 0) {
		$Days = $Days - 2 * $Days;
	}
	return 30 - $Days;
}
/**
 * 删除图片默认为 upload下面的
 * 例如删除upload下images下,touxiang文件夹里的1.jpg
 * deletePicture("images/touxiang/1.jpg");
 *
 * @param unknown $url
 */
function deletePicture($url, $pfloder = "./Public/upload/") {
	unlink ( $pfloder . $url );
}
/**
 * 根据给出的字符串,找出img标签,并返回,$default默认图片路径
 */
function selectImgHtml($content, $default = "__PUBLIC__/home/default/zanwutupian.jpg") {
	preg_match_all ( "/img((?!<img).)*\/>/is", $content, $matchs );
	for($i = 0; $i < countArray ( $matchs [0] ); $i ++) {
		$matchs [0] [$i] = string_cut ( $matchs [0] [$i], mb_strlen ( $matchs [0] [$i], "utf-8" ) - 2, " onerror=\"src='" . $default . "'\" >" );
		$matchs [0] [$i] = str_replace ( "width=", "widthno", $matchs [0] [$i] );
		$matchs [0] [$i] = str_replace ( "height=", "heightno", $matchs [0] [$i] );
		$matchs [0] [$i] = "<" . $matchs [0] [$i];
	}
	return $matchs [0];
}
/**
 * 根据给出的字符串,找出img的路径,并返回,$default默认图片路径
 */
function selectImgSrc($content, $default = "__PUBLIC__/home/default/zanwutupian.jpg") {
	preg_match_all ( "/img((?!<img).)*\/>/is", $content, $matchs );
	for($i = 0; $i < countArray ( $matchs [0] ); $i ++) {
		preg_match_all ( "/src=\"(.*)\"/", $matchs [0] [$i], $src );
		$matchs [0] [$i] = $src [0] [0];
		$matchs [0] [$i] = str_replace ( "src=\"", "", string_cut ( $matchs [0] [$i], mb_strlen ( $matchs [0] [$i], "utf-8" ) - 1, "" ) );
	}
	if (countArray ( $matchs [0] ) == 0) {
		$matchs [0] [0] = $default;
	}
	return $matchs [0];
}
function qqfaceReplace($str) {
	$str = str_replace ( "<", '&lt;', $str );
	$str = str_replace ( ">", '&gt;', $str );
	$str = str_replace ( "\n", '<br/>', $str );
	$str = preg_replace ( "[\[/表情([0-9]*)\]]", "<img src=\"__PUBLIC__/home/QQbiaoqing/face/$1.gif\" />", $str );
	return $str;
}
/**
 * 得到文字首字母
 *
 * @param unknown $s0
 * @return unknown string NULL
 */
function getFirstChar($s0) {
	$s0 = preg_replace ( "/\s/", "", $s0 );

	$fchar = ord ( $s0 {0} );

	if ($fchar >= ord ( "A" ) and $fchar <= ord ( "z" ))
		return strtoupper ( $s0 {0} );

	$s1 = iconv ( "UTF-8", "gb2312", $s0 );

	$s2 = iconv ( "gb2312", "UTF-8", $s1 );

	if ($s2 == $s0) {
		$s = $s1;
	} else {
		$s = $s0;
	}

	$asc = ord ( $s {0} ) * 256 + ord ( $s {1} ) - 65536;

	if ($asc >= - 20319 and $asc <= - 20284)
		return "A";

	if ($asc >= - 20283 and $asc <= - 19776)
		return "B";

	if ($asc >= - 19775 and $asc <= - 19219)
		return "C";

	if ($asc >= - 19218 and $asc <= - 18711)
		return "D";

	if ($asc >= - 18710 and $asc <= - 18527)
		return "E";

	if ($asc >= - 18526 and $asc <= - 18240)
		return "F";

	if ($asc >= - 18239 and $asc <= - 17923)
		return "G";

	if ($asc >= - 17922 and $asc <= - 17418)
		return "I";

	if ($asc >= - 17417 and $asc <= - 16475)
		return "J";

	if ($asc >= - 16474 and $asc <= - 16213)
		return "K";

	if ($asc >= - 16212 and $asc <= - 15641)
		return "L";

	if ($asc >= - 15640 and $asc <= - 15166)
		return "M";

	if ($asc >= - 15165 and $asc <= - 14923)
		return "N";

	if ($asc >= - 14922 and $asc <= - 14915)
		return "O";

	if ($asc >= - 14914 and $asc <= - 14631)
		return "P";

	if ($asc >= - 14630 and $asc <= - 14150)
		return "Q";

	if ($asc >= - 14149 and $asc <= - 14091)
		return "R";

	if ($asc >= - 14090 and $asc <= - 13319)
		return "S";

	if ($asc >= - 13318 and $asc <= - 12839)
		return "T";

	if ($asc >= - 12838 and $asc <= - 12557)
		return "W";

	if ($asc >= - 12556 and $asc <= - 11848)
		return "X";

	if ($asc >= - 11847 and $asc <= - 11056)
		return "Y";

	if ($asc >= - 11055 and $asc <= - 10247)
		return "Z";

	return null;
}

/**
 * 发送http请求
 *
 * @param unknown $url
 * @param unknown $param
 * @param string $httpMethod
 * @return mixed boolean
 */
function sendHttpRequest($url, $param, $httpMethod = 'GET') {
	$oCurl = curl_init ();
	if (stripos ( $url, "https://" ) !== FALSE) {
		curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYHOST, FALSE );
	}
	if ($httpMethod == 'GET') {
		curl_setopt ( $oCurl, CURLOPT_URL, $url . "?" . http_build_query ( $param ) );
		curl_setopt ( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
	} else {
		curl_setopt ( $oCurl, CURLOPT_URL, $url );
		curl_setopt ( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $oCurl, CURLOPT_POST, 1 );
		curl_setopt ( $oCurl, CURLOPT_POSTFIELDS, http_build_query ( $param ) );
	}
	$sContent = curl_exec ( $oCurl );
	$aStatus = curl_getinfo ( $oCurl );
	curl_close ( $oCurl );
	if (intval ( $aStatus ["http_code"] ) == 200) {
		return $sContent;
	} else {
		return FALSE;
	}
}
/**
 * 获取客户端ip
 *
 * @return Ambigous <unknown, boolean>
 */
function getClientIp() {
	$ip = false;
	if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] )) {
		$ip = $_SERVER ["HTTP_CLIENT_IP"];
	}
	if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
		$ips = explode ( ", ", $_SERVER ['HTTP_X_FORWARDED_FOR'] );
		if ($ip) {
			array_unshift ( $ips, $ip );
			$ip = FALSE;
		}
		for($i = 0; $i < count ( $ips ); $i ++) {
			if (! eregi ( "^(10|172\.16|192\.168)\.", $ips [$i] )) {
				$ip = $ips [$i];
				break;
			}
		}
	}
	return ($ip ? $ip : $_SERVER ['REMOTE_ADDR']);
}
/**
 * 获取ip在世界的真实地址
 *
 * @param unknown $ip
 * @return string
 */
function getIpAddress($ip) {
	$address = sendHttpRequest ( "http://whois.pconline.com.cn/ip.jsp", array (
			"ip" => $ip
	) );
	$address = mb_convert_encoding ( $address, "utf-8", "gbk" );
	return $address;
}
/**
 * 根据省id查找省name
 *
 * @param unknown $ip
 * @return string
 */
function getProvinceName($shengid) {
	$P = D ( "Province" );
	$where ["id"] = $shengid;
	$r = $P->where ( $where )->getField ( "name" );
	if ($r) {
		return $r;
	} else {
		return "未知地区";
	}
}
function getCityName($cityid) {
	$P = D ( "City" );
	$where ["id"] = $cityid;
	$r = $P->where ( $where )->getField ( "name" );
	if ($r) {
		return $r;
	} else {
		return "未知地区";
	}
}
function getAreaName($areaid) {
	$P = D ( "Area" );
	$where ["id"] = $areaid;
	$r = $P->where ( $where )->getField ( "name" );
	if ($r) {
		return $r;
	} else {
		return "未知地区";
	}
}
/**
 * 在页面上alert一下
 *
 * @param unknown $msg
 */
function alertMsg($msg, $url = "") {
	header ( 'Content-Type:text/html; charset=utf-8' );
	if ($url != "") {
		$location = "window.location.href='" . $url . "';";
	}
	echo "<script>alert('" . $msg . "');" . $location . "</script>";
}
function httpGet($url, $param = array()) {
	if (! is_array ( $param )) {
		throw new Exception ( "参数必须为array" );
	}
	$p = '';
	foreach ( $param as $key => $value ) {
		$p = $p . $key . '=' . $value . '&';
	}
	if (preg_match ( '/\?[\d\D]+/', $url )) { // matched ?c
		$p = '&' . $p;
	} else if (preg_match ( '/\?$/', $url )) { // matched ?$
	} else {
		$p = '?' . $p;
	}
	$p = preg_replace ( '/&$/', '', $p );
	$url = $url . $p;
	// echo $url;
	$httph = curl_init ( $url );
	curl_setopt ( $httph, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt ( $httph, CURLOPT_SSL_VERIFYHOST, 1 );
	curl_setopt ( $httph, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)" );

	curl_setopt ( $httph, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $httph, CURLOPT_HEADER, 1 );
	$rst = curl_exec ( $httph );
	curl_close ( $httph );
	return $rst;
}
/*
 * post method
*/
function httpPost($url, $param = array()) {
	if (! is_array ( $param )) {
		throw new Exception ( "参数必须为array" );
	}
	$httph = curl_init ( $url );
	curl_setopt ( $httph, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt ( $httph, CURLOPT_SSL_VERIFYHOST, 1 );
	curl_setopt ( $httph, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)" );
	curl_setopt ( $httph, CURLOPT_POST, 1 ); // 设置为POST方式
	curl_setopt ( $httph, CURLOPT_POSTFIELDS, $param );
	curl_setopt ( $httph, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $httph, CURLOPT_HEADER, 1 );
	$rst = curl_exec ( $httph );
	curl_close ( $httph );
	return $rst;
}
/**
 * 得到qq状态,1在线2离线,-1号码错误
 */
function getQQStatus($qq) {
	$return = httpGet ( "http://webpresence.qq.com/getonline?Type=1&" . $qq . ":" );
	$index = mb_strpos ( $return, "online[0]=" );
	if (! $index) {
		return - 1;
	}
	$status = mb_substr ( $return, $index + 10, 1, 'UTF-8' );
	return $status; // 1在线2离线
}

function getWeekDay(){
	$day = mktime(0,0,0,10,31,2014);
	$w=date('w',$day);
	$week=array(
			"0"=>"星期日",
			"1"=>"星期一",
			"2"=>"星期二",
			"3"=>"星期三",
			"4"=>"星期四",
			"5"=>"星期五",
			"6"=>"星期六"
	);
	echo $w;
	echo '今天是11'.$week[$w];
}
//这个星期的星期一
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式
function this_monday($timestamp=0,$is_return_timestamp=true){
	static $cache ;
	$id = $timestamp.$is_return_timestamp;
	if(!isset($cache[$id])){
		if(!$timestamp) $timestamp = time();
		$monday_date = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400));
		if($is_return_timestamp){
			$cache[$id] = strtotime($monday_date);
		}else{
			$cache[$id] = $monday_date;
		}
	}
	return $cache[$id];

}
//这个星期的星期天
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式
function this_sunday($timestamp=0,$is_return_timestamp=true){
	static $cache ;
	$id = $timestamp.$is_return_timestamp;
	if(!isset($cache[$id])){
		if(!$timestamp) $timestamp = time();
		$sunday = this_monday($timestamp) + /*6*86400*/518400;
		if($is_return_timestamp){
			$cache[$id] = $sunday;
		}else{
			$cache[$id] = date('Y-m-d',$sunday);
		}
	}
	return $cache[$id];
}
//上周一
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式
function last_monday($timestamp=0,$is_return_timestamp=true){
	static $cache ;
	$id = $timestamp.$is_return_timestamp;
	if(!isset($cache[$id])){
		if(!$timestamp) $timestamp = time();
		$thismonday = this_monday($timestamp) - /*7*86400*/604800;
		if($is_return_timestamp){
			$cache[$id] = $thismonday;
		}else{
			$cache[$id] = date('Y-m-d',$thismonday);
		}
	}
	return $cache[$id];
}

//上个星期天
// @$timestamp ，某个星期的某一个时间戳，默认为当前时间
// @is_return_timestamp ,是否返回时间戳，否则返回时间格式
function last_sunday($timestamp=0,$is_return_timestamp=true){
	static $cache ;
	$id = $timestamp.$is_return_timestamp;
	if(!isset($cache[$id])){
		if(!$timestamp) $timestamp = time();
		$thissunday = this_sunday($timestamp) - /*7*86400*/604800;
		if($is_return_timestamp){
			$cache[$id] = $thissunday;
		}else{
			$cache[$id] = date('Y-m-d',$thissunday);
		}
	}
	return $cache[$id];

}

function shouji($phone){
	if (mb_strlen($phone)!=11) {
		return $phone;
	}else{
		return mb_strcut($phone, 0,4)."XXXX".mb_strcut($phone, 8,11);
	}
}
/*
 *
* 	$result = getWebService("http://112.124.12.28:8029/WebService.asmx?wsdl", array('tmYdNo'=>'80025203'));

$dom = new DOMDocument();
$dom->loadXML($result->GetInteriorByYdNoResult->any);
$re = getArray($dom->documentElement);

dump($re["NewDataSet"]["Table"][0]["name"][0]["#text"]);
*
*
*/
function getWebService($url,$param){
	header ( "Content-Type: text/html; charset=utf8" );
	/*
	 * 指定WebService路径并初始化一个WebService客户端  80025203 tmYdNo
	*/
	$ws =$url;// "http://112.124.12.28:8029/WebService.asmx?wsdl";//webservice服务的地址
	$client = new SoapClient ($ws);
	/*
	 * 获取SoapClient对象引用的服务所提供的所有方法

	echo ("SOAP服务器提供的开放函数:");
	echo ('<pre>');
	var_dump ( $client->__getFunctions () );//获取服务器上提供的方法
	echo ('</pre>');
	echo ("SOAP服务器提供的Type:");
	echo ('<pre>');
	var_dump ( $client->__getTypes () );//获取服务器上数据类型
	echo ('</pre>');
	echo ("执行GetGUIDNode的结果:");*/
	$result=$client->GetInteriorByYdNo ($param);//查询中国郑州的天气，返回的是一个结构体
	return $result;
}
function getArray($node) {
	$array = false;

	if ($node->hasAttributes()) {
		foreach ($node->attributes as $attr) {
			$array[$attr->nodeName] = $attr->nodeValue;
		}
	}

	if ($node->hasChildNodes()) {
		if ($node->childNodes->length == 1) {
			$array[$node->firstChild->nodeName] = getArray($node->firstChild);
		} else {
			foreach ($node->childNodes as $childNode) {
				if ($childNode->nodeType != XML_TEXT_NODE) {
					$array[$childNode->nodeName][] = getArray($childNode);
				}
			}
		}
	} else {
		return $node->nodeValue;
	}
	return $array;
}
function  getALink($content,$url){
	return "<a target=\"_blank\" href=\"".getDomain().$url."\">".$content."</a>";
}

function getSid(){
	return "";
}
function getToken(){
	return "";
}
function getAppid(){
	return "";
}
function getServerIp(){
	return "sandboxapp.cloopen.com";
}

/**
 * 找回密码或者验证手机时，邮件的内容html
 * @param unknown $name
 * @return string
 */
function getPhoneContent($name) {
	$html = '';
	return $html;
}

/**
 * 根据手机号获取用户id
 * @return string $phone 参数为登陆用的手机号
 */
function getUseridByPhone($phone){
	$U = D("Users");
	$where["phone"]=$phone;
	return $U->where($where)->getField("id");
}


/**
 * 根据用户id获取手机
 *  string $userid 用户id
 */
function getPhoneByUserid($userid){
	$U = D("Users");
	$where["id"]=$userid;
	return $U->where($where)->getField("phone");
}

/**
 * 获取链接失效时间.以秒计数
 */
function  getPhoneOutDateTime(){
	return 60;
}
/**
 * 获取手机验证码的时间间隔.以秒计数
 */
function  getPhoneNextTime(){
	return 60;
}
/**
 * 手机激活操作
 */
function phoneActivive($userid){
	$U=D("Users");
	$where["id"]=$userid;
	$data["pstatus"]=1;
	$U->where($where)->save($data);
}
/**
 * 验证码不可以短时间内多次发送
 * true可以发送,false不可以,未使用
 */
function canPhoneSendNext($userid){
	$EC = D("Checkcheck");
	$where["uid"] = $userid;
	$checkcheck = $EC->where($where)->order("id desc")->find();

}
function getCode(){
	ob_clean ();
	$Verify = new \Think\Verify ();
	$Verify->fontSize = 14;
	$Verify->length = 4;
	$Verify->useNoise = false;
	$Verify->codeSet = '0123456789';
	$Verify->imageW = 100;
	$Verify->imageH = 34;
	$Verify->entry ();
}
/**
 * 判断验证码正确性
 */
function checkCode($code){
	$Verify = new \Think\Verify ();
	$r = $Verify->check($code);
	return $r;
}
function html($val,$sta=0){
	if($sta==1){
		
	}else{
		$val = html_entity_decode($val);
	}
  
   $val = str_replace("&nbsp;", "", $val);
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); 
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); 
   }

   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link',  'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound',  'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);

   $found = true; 
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); 
         $val = preg_replace($pattern, $replacement, $val); 
         if ($val_before == $val) {
            $found = false;
         }
      }
   }
   return $val;
}
/**
 * 得到请求的action/function
 * @return Ambigous <string, mixed>
 */
function getCurrentAction(){
	$actiom =__ACTION__;
	$app =  __APP__;
		
	if(!empty($app)){
		$actiom = str_replace($app, "", $actiom);
	}
	return $actiom;
}
/**
 * 判断有没有权限
 */
function hasQuanxian(){
		$mymenu = 	getCurrentAction();
		$has = false;
		if(session("roleid")!="admin"){
			$M = D("Menus");
			$where["url"] = $mymenu;
			$menu = $M->where($where)->getField("id");
			$R = D("Res");
			$res = $R->where($where)->getField("id");
				
			$RM = D("RoleMenu");
			$wherem["menuid"]=$menu;
			$wherem["roleid"]=session("roleid");
			$r1 =$RM->where($wherem)->count();
			$RR = D("RoleRes");
			$wherer["resid"]=$res;
			$wherer["roleid"]=session("roleid");
			$r2 =$RR->where($wherer)->count();
// 						dump($menu);dump($r1);
// 						dump($res);dump($r2);
			if(($menu&&$r1)||($res&&$r2)){
				return true;
			}else{
				return false;
		
			}
		}else{
			return true;
		}
		
}
/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author KUOER <hi@kuoer.cn>
 */
function encode($data, $key = '', $expire = 0) {
	$key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data = base64_encode($data);
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x = 0;
		$char .= substr($key, $x, 1);
		$x++;
	}

	$str = sprintf('%010d', $expire ? $expire + time():0);

	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
	}
	return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author KUOER <hi@kuoer.cn>
 */
function decode($data, $key = ''){
	$key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
	$data   = str_replace(array('-','_'),array('+','/'),$data);
	$mod4   = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	$data   = base64_decode($data);
	$expire = substr($data,0,10);
	$data   = substr($data,10);

	if($expire > 0 && $expire < time()) {
		return '';
	}
	$x      = 0;
	$len    = strlen($data);
	$l      = strlen($key);
	$char   = $str = '';

	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x = 0;
		$char .= substr($key, $x, 1);
		$x++;
	}

	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		}else{
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}
/**
 * 判断是不是正数
 * @param unknown $number 3位以内小数或者正数
 * @return boolean
 */
function zczy_check_zhengshu($number){
	if($number==0){
		return false;
	}
	if(preg_match('/^[\d]{1,6}$/', $number)){
		return true;
	}
	if(preg_match('/^[\d]{1,6}\.[\d]{1,3}$/', $number)){
		return	true;
	}
	return	false;
}
/**
 * 判断email格式，正常返回True
 */
	function zczy_check_email($email){
		if(preg_match('/^\w+@\w+\.\w+$/', $email)){
			return	true;
		}
		return false;
	}
	/**
	 * 判断时间格式如2012-02-02 02::02:55返回true
	 */
	function zczy_check_time($time_str){
		return preg_match("/^[\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2}$/", $time_str);
	}
	/**
	 * 判断日期格式如2012-02-02返回true
	 */
	function zczy_checkd_date($date_str){
		return preg_match("/^[\d]{4}-[\d]{2}-[\d]{2}$/", $date_str);
	}
	/**
	 * 获取随机字符串
	 * @param int $length 默认长度为4
	 * @param string $codeset 从$codeset中选取字母拼成随机字符串
	 */
	function zczy_random_string($length=4,$codeset="QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789"){
		$all_length = mb_strlen($codeset,"utf8");
		if($all_length==0){
			return false;
		}
		$str="";
		for ($i=0;$i<$length;$i++){
			$str.=mb_substr($codeset, mt_rand(0, $all_length-1),1);
			echo $str."<br/>";
		}
		return $str;
	}
	
	function getLoginUser(){
		return $_SESSION["member"];
	}
	function getLoginUserId(){
		return $_SESSION["member"]["id"];
	}
	function setLoginUser($user){
		$_SESSION["user"]=$user;
	}
	function shuaXinSessionUser(){
		$U = D("Users");
		$user = $U->where(array("id"=>getLoginUserId()))->find();
		setLoginUser($user);
	}
	function getProjectUrl(){
		return getDomain();
	}
	