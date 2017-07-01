<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * Think 系统函数库
 */

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/3 0003
 * Time: 下午 2:44
 */
/*
 * @$multi_array    数组
 * @$sort_key   字段
 */
function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
    if(is_array($multi_array)){
        foreach ($multi_array as $row_array){
            if(is_array($row_array)){
                $key_array[] = $row_array[$sort_key];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($key_array,$sort,$multi_array);
    return $multi_array;
}

function huanxin_get_client_id(){
    return M('System')->where(array('id'=>1))->getField('hx_client_id');//
}
function huanxin_get_client_secret(){
    return M('System')->where(array('id'=>1))->getField('hx_secret');//
}
function huanxin_get_org_name(){
    return M('System')->where(array('id'=>1))->getField('hx_appkey_1');//
}
function huanxin_get_app_name(){
    return M('System')->where(array('id'=>1))->getField('hx_appkey_2');//
}
//环信注册
function huanxin_zhuce($username,$password){
    $param = array (
        "username" => $username,
        "password" => $password
    );
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users";
    $res = huanxin_curl_request($url, json_encode($param));
    $tokenResult =  json_decode($res, true);
    $tokenResult["password"]=$param["password"];
    $huanxin_uuid = $tokenResult["entities"][0]["uuid"];
    $huanxin_username = $tokenResult["entities"][0]["username"];
    $huanxin_password=$param["password"];
    if(!($huanxin_uuid&&$huanxin_username)){
        return false;
    }else{
        return true;
    }
}

function huanxin_curl_request($url, $body, $header = array(), $method = "POST") {
    array_push ( $header, 'Accept:application/json' );
    array_push ( $header, 'Content-Type:application/json' );
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 60 );
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    // curl_setopt($ch, $method, 1);

    switch (strtoupper($method)) {
        case "GET" :
            curl_setopt ( $ch, CURLOPT_HTTPGET, true );
            break;
        case "POST" :
            curl_setopt ( $ch, CURLOPT_POST, true );
            break;
        case "PUT" :
            curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "PUT" );
            break;
        case "DELETE" :
            curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
            break;
    }

    curl_setopt ( $ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0' );
    curl_setopt ( $ch, CURLOPT_ENCODING, 'gzip' );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 1 );
    if (isset ( $body {3} ) > 0) {
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $body );
    }
    if (count ( $header ) > 0) {
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
    }
    $ret = curl_exec ( $ch );
    $err = curl_error ( $ch );
    curl_close ( $ch );
    // clear_object($ch);
    // clear_object($body);
    // clear_object($header);
    if ($err) {
        return $err;
    }
    return $ret;
}
function huanxin_get_access_token($force = false) {
    if(!$force){
        $token = S("huanxin_access_token");
        if($token){
            return $token["access_token"];
        }
    }

    $param = array (
        "grant_type" => "client_credentials",
        "client_id" => huanxin_get_client_id(),
        "client_secret" => huanxin_get_client_secret()
    );
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/token";
    $res = $this->huanxin_curl_request ( $url, json_encode($param) );
    $tokenResult =  json_decode($res, true);
    S("huanxin_access_token",$tokenResult,$tokenResult["expires_in"]*0.9);
    return $tokenResult["access_token"] ;
}

function huanxin_xiugainicheng($nicheng,$huanxin_username){
    $access_token = huanxin_get_access_token();
    $param = array (
        "nickname" => $nicheng
    );

    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users/$huanxin_username";
    $header = "Authorization:Bearer ".$access_token;
    $r = huanxin_curl_request($url, json_encode($param),array($header),"PUT");
    return  $r;
}

/**
 *获取token
 */
function getTokens()
{

    $options=array(
        "grant_type"=>"client_credentials",
        "client_id"=>huanxin_get_client_id(),
        "client_secret"=>huanxin_get_client_secret()
    );
    //json_encode()函数，可将PHP数组或对象转成json字符串，使用json_decode()函数，可以将json字符串转换为PHP数组或对象
    $body=json_encode($options);
    //使用 $GLOBALS 替代 global
    $url="https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/token";
    //$url=$base_url.'token';
    $tokenResult = postCurl($url,$body);
    //var_dump($tokenResult['expires_in']);
    //return $tokenResult;
    return "Authorization:Bearer ". $tokenResult["access_token"];


    //return "Authorization:Bearer YWMtG_u2OH1tEeWK7IWc3Nx2ygAAAVHjWllhTpavYYyhaI_WzIcHIQ9uitTvsmw";
}



function postCurl($url,$body,$header,$type="POST"){

    //1.创建一个curl资源
    $ch = curl_init();
    //2.设置URL和相应的选项
    curl_setopt($ch,CURLOPT_URL,$url);//设置url
    //1)设置请求头
    //array_push($header, 'Accept:application/json');
    //array_push($header,'Content-Type:application/json');
    //array_push($header, 'http:multipart/form-data');
    //设置为false,只会获得响应的正文(true的话会连响应头一并获取到)
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt ( $ch, CURLOPT_TIMEOUT,5); // 设置超时限制防止死循环
    //设置发起连接前的等待时间，如果设置为0，则无限等待。
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
    //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //2)设备请求体
    if (count($body)>0) {
        //$b=json_encode($body,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);//全部数据使用HTTP协议中的"POST"操作来发送。
    }
    //设置请求头
    if(count($header)>0){
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    }
    //上传文件相关设置
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算

    //3)设置提交方式
    switch($type){
        case "GET":
            curl_setopt($ch,CURLOPT_HTTPGET,true);
            break;
        case "POST":
            curl_setopt($ch,CURLOPT_POST,true);
            break;
        case "PUT"://使用一个自定义的请求信息来代替"GET"或"HEAD"作为HTTP请									                     求。这对于执行"DELETE" 或者其他更隐蔽的HTT
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
            break;
        case "DELETE":
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
            break;
    }


    //4)在HTTP请求中包含一个"User-Agent: "头的字符串。-----必设
    curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
    //5)


    //3.抓取URL并把它传递给浏览器
    $res=curl_exec($ch);
    $result=json_decode($res,true);
    //4.关闭curl资源，并且释放系统资源
    curl_close($ch);
    if(empty($result))
        return $res;
    else
        return $result;
}


/*
 创建聊天室
 */
function createChatRoom($options){
    $url="https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/chatrooms";
    $header=array(getTokens());
    $body=json_encode($options);
    $result=postCurl($url,$body,$header);
    return $result;
}

/*
 获取单个用户
 */
function getUsers($username){
    $url="https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users/$username";
    $header=array(getTokens());
    $result=postCurl($url,'',$header,"GET");
    return $result;
}
/*
查看用户黑名单
*/

function getBlacklist($username){
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users/$username/blocks/users";
    //$url=$this->url.'users/'.$username.'/blocks/users';
    $header=array(getTokens());
    $result=postCurl($url,'',$header,'GET');
    return $result;

}

/*
		往黑名单中加人
	*/
function addUserForBlacklist($username,$usernames){
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users/$username/blocks/users";
    $body=json_encode(['usernames'=>[$usernames]]);
    $header=array(getTokens());
    $result=postCurl($url,$body,$header,'POST');
    return $result;
}



/*
		从黑名单中减人
	*/
function deleteUserFromBlacklist($username,$blocked_name){
    $url = "https://a1.easemob.com/".huanxin_get_org_name()."/".huanxin_get_app_name()."/users/$username/blocks/users/".$blocked_name;
    //$url=$this->url.'users/'.$username.'/blocks/users/'.$blocked_name;
    $header=array(getTokens());
    $result=postCurl($url,'',$header,'DELETE');
    return $result;

}





/**
 * 验证验证码
 * @param $code
 * @param string $id
 * @return bool
 */
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
/**
+----------------------------------------------------------
 * 加密密码
+----------------------------------------------------------
 * @param string    $data   待加密字符串
+----------------------------------------------------------
 * @return string 返回加密后的字符串
 */
function encrypt($data) {
    return md5(C("AUTH_CODE") . md5($data));
}

/**
+----------------------------------------------------------
 * 将一个字符串转换成数组，支持中文
+----------------------------------------------------------
 * @param string    $string   待转换成数组的字符串
+----------------------------------------------------------
 * @return string   转换后的数组
+----------------------------------------------------------
 */
function strToArray($string) {
    $strlen = mb_strlen($string);
    while ($strlen) {
        $array[] = mb_substr($string, 0, 1, "utf8");
        $string = mb_substr($string, 1, $strlen, "utf8");
        $strlen = mb_strlen($string);
    }
    return $array;
}
/**
+----------------------------------------------------------
 * 生成随机字符串
+----------------------------------------------------------
 * @param int       $length  要生成的随机字符串长度
 * @param string    $type    随机码类型：0，数字+大写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function randCode($length = 5, $type = 0) {
    $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
    $code='';
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    } else if ($type == "-1") {
        $string = implode("", $arr);
    } else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str[$i] = $string[rand(0, $count)];
        $code .= $str[$i];
    }
    return $code;
}
/**
+----------------------------------------------------------
 * 将一个字符串部分字符用*替代隐藏
+----------------------------------------------------------
 * @param string    $string   待转换的字符串
 * @param int       $bengin   起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int       $len      需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int       $type     转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string    $glue     分割符
+----------------------------------------------------------
 * @return string   处理后的字符串
+----------------------------------------------------------
 */
function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = "@") {
    if (empty($string))
        return false;
    $array = array();
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, "utf8");
            $string = mb_substr($string, 1, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
    }
    switch ($type) {
        case 1:
            $array = array_reverse($array);
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = "*";
            }
            $string = implode("", array_reverse($array));
            break;
        case 2:
            $array = explode($glue, $string);
            $array[0] = hideStr($array[0], $bengin, $len, 1);
            $string = implode($glue, $array);
            break;
        case 3:
            $array = explode($glue, $string);
            $array[1] = hideStr($array[1], $bengin, $len, 0);
            $string = implode($glue, $array);
            break;
        case 4:
            $left = $bengin;
            $right = $len;
            $tem = array();
            for ($i = 0; $i < ($length - $right); $i++) {
                if (isset($array[$i]))
                    $tem[] = $i >= $left ? "*" : $array[$i];
            }
            $array = array_chunk(array_reverse($array), $right);
            $array = array_reverse($array[0]);
            for ($i = 0; $i < $right; $i++) {
                $tem[] = $array[$i];
            }
            $string = implode("", $tem);
            break;
        default:
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = "*";
            }
            $string = implode("", $array);
            break;
    }
    return $string;
}
/**
+----------------------------------------------------------
 * 功能：字符串截取指定长度
 * leo.li hengqin2008@qq.com
+----------------------------------------------------------
 * @param string    $string      待截取的字符串
 * @param int       $len         截取的长度
 * @param int       $start       从第几个字符开始截取
 * @param boolean   $suffix      是否在截取后的字符串后跟上省略号
+----------------------------------------------------------
 * @return string               返回截取后的字符串
+----------------------------------------------------------
 */
function cutStr($str, $len = 100, $start = 0, $suffix = 1) {
    $str = strip_tags(trim(strip_tags($str)));
    $str = str_replace(array("\n", "\t"), "", $str);
    $strlen = mb_strlen($str);
    while ($strlen) {
        $array[] = mb_substr($str, 0, 1, "utf8");
        $str = mb_substr($str, 1, $strlen, "utf8");
        $strlen = mb_strlen($str);
    }
    $end = $len + $start;
    $str = '';
    for ($i = $start; $i < $end; $i++) {
        $str.=$array[$i];
    }
    return count($array) > $len ? ($suffix == 1 ? $str . "&hellip;" : $str) : $str;
}
/**
+----------------------------------------------------------
 * 功能：检测一个目录是否存在，不存在则创建它
+----------------------------------------------------------
 * @param string    $path      待检测的目录
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function makeDir($path) {
    return is_dir($path) or (makeDir(dirname($path)) and @mkdir($path, 0777));
}
/**
+----------------------------------------------------------
 * 功能：检测一个字符串是否是邮件地址格式
+----------------------------------------------------------
 * @param string $value    待检测字符串
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function is_email($value) {
    return preg_match("/^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $value);
}

/**
+----------------------------------------------------------
 * 功能：系统邮件发送函数
+----------------------------------------------------------
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表namespace Org\Util\PHPMailer;
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function send_mail($to, $name, $subject = '', $body = '', $attachment = null, $config = '') {
    $config = is_array($config) ? $config : C('SYSTEM_EMAIL');
    //import('PHPMailer.phpmailer', VENDOR_PATH);         //从PHPMailer目录导class.phpmailer.php类文件
    $mail = new \Org\Util\PHPMailer\PHPMailer();                           //PHPMailer对象
    $mail->CharSet = 'UTF-8';                         //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                                   // 设定使用SMTP服务
//    $mail->IsHTML(true);
    $mail->SMTPDebug = 0;                             // 关闭SMTP调试功能 1 = errors and messages2 = messages only
    $mail->SMTPAuth = true;                           // 启用 SMTP 验证功能
    if ($config['smtp_port'] == 465)
        $mail->SMTPSecure = 'ssl';                    // 使用安全协议
    $mail->Host = $config['smtp_host'];                // SMTP 服务器
    $mail->Port = $config['smtp_port'];                // SMTP服务器的端口号
    $mail->Username = $config['smtp_user'];           // SMTP服务器用户名
    $mail->Password = $config['smtp_pass'];           // SMTP服务器密码
    $mail->SetFrom($config['from_email'], $config['from_name']);
    $replyEmail = $config['reply_email'] ? $config['reply_email'] : $config['reply_email'];
    $replyName = $config['reply_name'] ? $config['reply_name'] : $config['reply_name'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            if (is_array($file)) {
                is_file($file['path']) && $mail->AddAttachment($file['path'], $file['name']);
            } else {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
    } else {
        is_file($attachment) && $mail->AddAttachment($attachment);
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}
/**
+----------------------------------------------------------
 * 功能：剔除危险的字符信息
+----------------------------------------------------------
 * @param string $val
+----------------------------------------------------------
 * @return string 返回处理后的字符串
+----------------------------------------------------------
 */
function remove_xss($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
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
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

/**
+----------------------------------------------------------
 * 功能：计算文件大小
+----------------------------------------------------------
 * @param int $bytes
+----------------------------------------------------------
 * @return string 转换后的字符串
+----------------------------------------------------------
 */
function byteFormat($bytes) {
    $sizetext = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $sizetext[$i];
}

function checkCharset($string, $charset = "UTF-8") {
    if ($string == '')
        return;
    $check = preg_match('%^(?:
                                [\x09\x0A\x0D\x20-\x7E] # ASCII
                                | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
                                | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
                                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
                                | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
                                | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
                                | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
                                | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
                                )*$%xs', $string);

    return $charset == "UTF-8" ? ($check == 1 ? $string : iconv('gb2312', 'utf-8', $string)) : ($check == 0 ? $string : iconv('utf-8', 'gb2312', $string));
}
//模拟post提交
function doCurlPostRequest($url,$data)
{
    if(empty($url)||empty($data))
    {
        return false;
    }
    $ch=curl_init();//初始化
    curl_setopt($ch, CURLOPT_HEADER,false);
    curl_setopt($ch,CURLOPT_URL,$url);//设置选项
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    $output =curl_exec($ch);//执行
    curl_close($ch);//释放句柄
    return $output;

}
/**
+----------------------------------------------------------
 * 功能：检测一个字符串是否是手机地址格式
+----------------------------------------------------------
 * @param string $value    待检测字符串
+----------------------------------------------------------
 * @return boolean
+----------------------------------------------------------
 */
function is_mobile($value) {
    return preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}|^17[0-9]\d{8}$#', $value);
}

//成功处理函数
function success($arr){
    $d = [
        'status' 	=> 'ok',
        'data'		=> $arr
    ];
    echo  json_encode($d);
    exit;
}

function error($arr){
    echo  json_encode([
        'status'=> 'error',
        'error'=> $arr
    ]);
    exit();
}

function pending($arr){
    echo  json_encode([
        'status'=> 'pending',
        'error'=> $arr
    ]);
    exit();
}

function checklogin(){
    $uid = I('uid');
    $token = I('token');
    $data["user_id"] = $uid;
    $data["token"] = $token;
    $rel = M("User")->where($data)->find();

    if (!$rel) {
        pending("token failed");
    }else{
        if($rel['is_del']==2){
            pending("账号被删除!");
        }else{
//            //如果是会员,判断会员是否到期,到期变成普通会员
//            if ($rel['type']==2){
//                if ($rel['expiration_time']<time()){
//                    M('User')->where(['user_id'=>$uid])->save(['type'=>1,'uptime'=>time()]);
//                }
//            }
            return $rel;
        }
    }
}

/**
 * @点10个赞,就可以获取7天vip
 * @param $user_id
 */
function get_vip($user_id,$user_use_zan){
    $system = M('System')->where(['id'=>1])->find();
    if ($user_use_zan==$system['zan_count']){
        $user = M('User')->where(['user_id'=>$user_id])->find();
        if (empty($user['expiration_time']) || empty($user['expiration_time_date'])){
            M('User')->where(['user_id'=>$user_id])->save(['expiration_time'=>time(),'expiration_time_date'=>date('Y-m-d',time()),'uptime'=>time()]);
            $user['expiration_time'] = time();
            $user['expiration_time_date'] = date('Y-m-d',time());
        }
        $endtime = $user['expiration_time']+($system['vip_day']*24*60*60);
        $endtime_date = date('Y-m-d',strtotime($user['expiration_time_date'])+($system['vip_day']*24*60*60));
        M('User')->where(['user_id'=>$user_id])->save(['type'=>2,'expiration_time'=>$endtime,'expiration_time_date'=>$endtime_date,'use_zan'=>0,'uptime'=>time()]);
    }
}


/**
 * @将一个字符串部分字符用*替代隐藏
 */
function replace_string($content){
    $str = M('System')->getFieldById(1,'sensitive_word');
    $rs = explode(',',$str);
    $arr = str_replace($rs,'**',$content);
    return $arr;
}
/**
 * @判断提交的字符串中是否有敏感词
 */
function is_sensitive_word($content){
    $str = M('System')->getFieldById(1,'sensitive_word');
    $rs = explode($str);
    $arr = [];
    foreach ($rs as $k=>$v) {
        if (strpos($content,$v)){
            $arr[] = $v;
        }else{
            $arr[] = "none";
        }
    }
    foreach ($arr as $k=>$v){
        if ($v=='none'){
            unset($arr[$k]);
        }
    }
    if (!$arr){
        $arr = [];
    }
    return $arr;
}
/**
 * @下载图片
 */
function GrabImage($headUrl){
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt ( $ch, CURLOPT_URL, $headUrl );
    ob_start ();
    curl_exec ( $ch );
    $info = ob_get_contents ();
    ob_end_clean ();

    $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
    curl_close($ch);

    return $info;
}
/**
 * 图片高斯模糊（适用于png/jpg/gif格式）
 * @param $srcImg 原图片
 * @param $savepath 保存路径
 * @param $savename 保存名字
 * @param $positon 模糊程度
 *
 *基于Martijn Frazer代码的扩充， 感谢 Martijn Frazer
 */
function gaussian_blur($srcImg,$savepath=null,$savename=null,$blurFactor=5){
    $gdImageResource=image_create_from_ext($srcImg);
    $srcImgObj=blur($gdImageResource,$blurFactor);
    $temp = pathinfo($srcImg);
    $name = $temp['basename'];
    $path = $temp['dirname'];
    $exte = $temp['extension'];
    $savename = $savename ? $savename : $name;
    $savepath = $savepath ? $savepath : $path;
    $savefile = $savepath .'/'. $savename;
    $srcinfo = @getimagesize($srcImg);
    switch ($srcinfo[2]) {
        case 1: imagegif($srcImgObj, $savefile); break;
        case 2: imagejpeg($srcImgObj, $savefile); break;
        case 3: imagepng($srcImgObj, $savefile); break;
        default: return '保存失败'; //保存失败
    }

    return $savefile;
    imagedestroy($srcImgObj);
}

/**
 * Strong Blur
 *
 * @param  $gdImageResource  图片资源
 * @param  $blurFactor          可选择的模糊程度
 *  可选择的模糊程度  0使用   3默认   超过5时 极其模糊
 * @return GD image 图片资源类型
 * @author Martijn Frazer, idea based on http://stackoverflow.com/a/20264482
 */
function blur($gdImageResource, $blurFactor = 3)
{
    // blurFactor has to be an integer
    $blurFactor = round($blurFactor);

    $originalWidth = imagesx($gdImageResource);
    $originalHeight = imagesy($gdImageResource);

    $smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
    $smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));

    // for the first run, the previous image is the original input
    $prevImage = $gdImageResource;
    $prevWidth = $originalWidth;
    $prevHeight = $originalHeight;

    // scale way down and gradually scale back up, blurring all the way
    for($i = 0; $i < $blurFactor; $i += 1)
    {
        // determine dimensions of next image
        $nextWidth = $smallestWidth * pow(2, $i);
        $nextHeight = $smallestHeight * pow(2, $i);

        // resize previous image to next size
        $nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
        imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
            $nextWidth, $nextHeight, $prevWidth, $prevHeight);

        // apply blur filter
        imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);

        // now the new image becomes the previous image for the next step
        $prevImage = $nextImage;
        $prevWidth = $nextWidth;
        $prevHeight = $nextHeight;
    }

    // scale back to original size and blur one more time
    imagecopyresized($gdImageResource, $nextImage,
        0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
    imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);

    // clean up
    imagedestroy($prevImage);

    // return result
    return $gdImageResource;
}

function image_create_from_ext($imgfile)
{
    $info = getimagesize($imgfile);
    $im = null;
    switch ($info[2]) {
        case 1: $im=imagecreatefromgif($imgfile); break;
        case 2: $im=imagecreatefromjpeg($imgfile); break;
        case 3: $im=imagecreatefrompng($imgfile); break;
    }
    return $im;
}
function checklogin2(){
    $data["company_id"] = I("company_id");
    $data["token"] = I("token");
    $rel = M("Company")->where($data)->find();

    if (!$rel) pending("token failed");

    return $rel;
}

function logistics($type,$number){
    $apikey = 'af0132b43796f9cf';
    $url = "http://api.jisuapi.com/express/query?appkey=$apikey&type=$type&number=".$number;
    $xml = json_decode(file_get_contents($url),true);
    return $xml;
}
/**
 *@等级提升
 */
function grade($user_id){
    $sum = M('Recharge_record')->where(['user_id'=>$user_id,'pay_on'=>['neq',''],'pay_return'=>['neq','']])->sum('amount');
    $sum ? $sum = $sum : $sum = 0;
    if($sum<=100){
        $grade = 'Lv0';
    }elseif($sum>100 && $sum<1000){
        $grade = 'Lv1';
    }elseif($sum>1000 && $sum<2000){
        $grade = 'Lv1';
    }elseif($sum>2000 && $sum<3000){
        $grade = 'Lv2';
    }elseif($sum>3000 && $sum<4000){
        $grade = 'Lv3';
    }elseif($sum>4000 && $sum<5000){
        $grade = 'Lv4';
    }elseif($sum>5000 && $sum<6000){
        $grade = 'Lv5';
    }elseif($sum>6000 && $sum<7000){
        $grade = 'Lv6';
    }elseif($sum>7000 && $sum<8000){
        $grade = 'Lv7';
    }elseif($sum>8000 && $sum<9000){
        $grade = 'Lv8';
    }elseif($sum>9000 && $sum<10000){
        $grade = 'Lv9';
    }elseif($sum>10000 && $sum<11000){
        $grade = 'Lv10';
    }elseif($sum>11000 && $sum<12000){
        $grade = 'Lv11';
    }elseif($sum>12000 && $sum<13000){
        $grade = 'Lv12';
    }elseif($sum>13000 && $sum<14000){
        $grade = 'Lv13';
    }elseif($sum>14000 && $sum<15000){
        $grade = 'Lv14';
    }elseif($sum>15000 && $sum<16000){
        $grade = 'Lv15';
    }elseif($sum>16000 && $sum<17000){
        $grade = 'Lv16';
    }elseif($sum>17000 && $sum<18000){
        $grade = 'Lv17';
    }elseif($sum>18000 && $sum<19000){
        $grade = 'Lv18';
    }elseif($sum>19000 && $sum<20000){
        $grade = 'Lv19';
    }elseif($sum>20000){
        $grade = 'Lv20';
    }
    $now_grade = M('User')->getFieldByUser_id($user_id,'grade');
    if($now_grade!=$grade){
        M('User')->where(['user_id'=>$user_id])->save(['grade'=>$grade,'uptime'=>time()]);
        M('Message')->add(['type'=>1,'user_id2'=>$user_id,'content'=>'恭喜您等级升到'.$grade.'!','intime'=>time(),'date'=>date('Y-m-d',time())]);
    }
}
/**
 * @直播推流地址、播放地址
 */
function push_address(){
    $system = M('System')->where(['id'=>1])->find();
    import('Vendor.Qiniu.Pili');
    $ak = $system['ak'];
    $sk = $system['sk'];
    $hubName = "vxiu1";
    //创建hub
    $mac = new \Qiniu\Pili\Mac($ak, $sk);
    $client = new \Qiniu\Pili\Client($mac);
    $hub = $client->hub($hubName);
    //获取stream
    $streamKey = "php-sdk-test" . time();
    $stream = $hub->stream($streamKey);
    try {
        //创建stream
        $resp = $hub->create($streamKey);
        //获取stream info
        $resp = $stream->info();
        //列出所有流
        $resp = $hub->listStreams("php-sdk-test", 1, "");
        //列出正在直播的流
        $resp = $hub->listLiveStreams("php-sdk-test", 1, "");
    } catch (\Exception $e) {
        echo "Error:", $e, "\n";
    }



    try {
        //启用流
        $stream->enable();
        $status = $stream->liveStatus();
    } catch (\Exception $e) {
        echo "Error:", $e, "\n";
    }
    //RTMP 推流地址
    $url = \Qiniu\Pili\RTMPPublishURL($system['publishurl'], $hubName, $streamKey, 3600, $ak, $sk);
    //RTMP 直播放址
    $url2 = \Qiniu\Pili\RTMPPlayURL($system['playurl'], $hubName, $streamKey);
    //HLS 直播地址
    $url3 = \Qiniu\Pili\HLSPlayURL($system['playurl'], $hubName, $streamKey);   //m3u8格式
    $url4 = \Qiniu\Pili\HDLPlayURL($system['playurl'], $hubName, $streamKey);     //flv格式
    $url5 = \Qiniu\Pili\RTMPPlayURL($system['playurl'], $hubName, $streamKey);    //rtmp格式
    $result = array('url'=>$url,'url2'=>$url2,'m3u8'=>$url3,'streamKey'=>$streamKey,'flv'=>$url4,'rtmp'=>$url5);
    return $result;
}
//百度地图获取距离
function getDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6367000; //approximate radius of earth in meters
    /*
    Convert these degrees to radians
    to work with the formula
    */

    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
    Using the
    Haversine formula

    http://en.wikipedia.org/wiki/Haversine_formula

    calculate the distance
    */

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}
/**
 * @二维数组根据其中一个字段排序(正序)
 * @$array:数组    $orderby:需要重新排序的字段
 */
function wpjam_array_multisort($array, $orderby, $order = SORT_ASC, $sort_flags = SORT_NUMERIC){
    $refer = array();

    foreach ($array as $key => $value) {
        $refer[$key] = $value[$orderby];
    }

    array_multisort($refer, $order, $sort_flags, $array);

    return $array;
}
/**
 * @二维数组根据其中一个字段排序(倒序序)
 * @$array:数组    $orderby:需要重新排序的字段
 */
function wpjam_array_desc($array, $orderby, $order = SORT_DEST){
    $refer = array();

    foreach ($array as $key => $value) {
        $refer[$key] = $value[$orderby];
    }

    array_multisort($refer, $order, $array);

    return $array;
}

/**
 * @根据时间，返回计算结果
 */
function get_times($intime){
    $time = time()-$intime;
    if($time<=60){
        $day = '刚刚';
    }elseif($time>60 && $time<3600){
        $day = floor($time/60).'分钟前';
    }elseif($time>=3600 && $time<86400){
        $day = floor($time/3600).'小时前';
    }elseif($time>=86400 && $time<2592000){
        $day = floor($time/86400).'天前';
    }elseif($time>=2592000 && $time<31104000){
        $day = floor($time/2592000).'个月前';
    }elseif(date('Y',time())-date('Y',$intime)>=1){
        $day = date('Y',time())-date('Y',$intime).'年前';
    }
    return $day;
}
/**
 * 生成验证码
 * @param int $length
 * @param bool $numeric 是否为数字字符串
 * @return string
 */
function random($length=6, $numeric=true)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}
/**
 * 把xml数据转成数组
 * @param $xml
 * @return mixed
 */
function xml_to_array($xml)
{
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if (preg_match_all($reg, $xml, $matches)) {
        $count = count($matches[0]);
        for ($i = 0; $i < $count; $i++) {
            $subxml = $matches[2][$i];
            $key = $matches[1][$i];
            if (preg_match($reg, $subxml)) {
                $arr[$key] = xml_to_array($subxml);
            } else {
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}
/**
 * 获取二维数组中的某个字段形成一维数组
 * @param $arr
 * @param $key
 * @return array
 */
function get_linear_array($arr, $key){
    $data = array();
    foreach($arr as $v){
        $data[] = $v[$key];
    }

    return $data;
}

function check_planar_array($arr, $k1, $v1, $k2, $v2){
    foreach($arr as $v){
        if($v[$k1] == $v1 && $v[$k2] == $v2){
            return true;
            break;
        }
    }

    return false;
}

/**
 * 浮点数减一法
 * @param float $value 数字
 * @param int $places 保留小数
 * @param string $separator 分隔符
 * @return string|float
 */
function floor_down($value, $places=4, $separator = "."){
    $arr =  explode($separator, $value?:0);

    if(count($arr) != 2) {
        return $value;
    }

    $len = strlen($arr[1]);

    if ($len <= $places) {
        return $value;
    }

    if($places <= 0) return $arr[0];

    $str = $arr[0] . $separator;
    for ($i=0; $i<$places; $i++) {
        $str .= $arr[1][$i];
    }
    return $str;
}

function _cut($begin,$end,$str){
    $b = strpos($str,$begin) + strlen($begin);
    $e = strpos($str,$end) - $b;

    return substr($str,$b,$e);
}

/**
 * 等比缩放
 *
 * $srcImage   源图片路径
 * $toFile     目标图片路径
 * $max        最大值
 * $min        最小值
 * $get_max    是否获取宽高中的最大值
 * $del_oldimg 是否删除原图片
 * $size       不删除原图片时的新图片名称(原图片名称+'_S')
 * @return unknown
 */
function img_resize($old_src, $new_src, $max, $min, $get_max=true, $del_oldimg=true, $size='S'){
    $srcImage =rtrim(BASE_PATH, '/').$old_src;

    list($width, $height, $type, $attr) = getimagesize($srcImage);

    //根据最大值，算出另一个边的长度，得到缩放后的图片宽度和高度
    if($get_max){
        $old_max = $width > $height ? $width : $height;
        if ($old_max <= $max && $old_max >= $min) return $old_src;

        if($width > $height){
            $w = $width > $max ? $max : ($width < $min ? $min : $width);
            $h = $height*($w/$width);
        }else{
            $h = $height > $max ? $max : ($height < $min ? $min : $height);
            $w = $width*($h/$height);
        }
    }else{
        if ($width <= $max && $width >= $min) return $old_src;

        $w = $width > $max ? $max : ($width < $min ? $min : $width);
        $h = $height*($w/$width);
    }

    switch ($type) {
        case 1: $img = imagecreatefromgif($srcImage); break;
        case 2: $img = imagecreatefromjpeg($srcImage); break;
        case 3: $img = imagecreatefrompng($srcImage); break;
        default: return $old_src;
    }

    $newImg = imagecreatetruecolor($w, $h);

    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $w, $h, $width, $height);

    $new_src = preg_replace("/(.gif|.jpg|.jpeg|.png)/i","",$new_src);
    if(!$del_oldimg) $new_src .= '_'.$size;
    $toFile = rtrim(BASE_PATH, '/').$new_src;

    if($del_oldimg) @unlink($srcImage);//删除原图片

    //生成新图片
    switch($type) {
        case 1:
            if(imagegif($newImg, "{$toFile}.gif")) return $new_src.'.gif';
            break;
        case 2:
            $src = "{$toFile}.jpg";
            if(imagejpeg($newImg, $src)) return $new_src.'.jpg';
            break;
        case 3:
            $src = "{$toFile}.png";
            if(imagepng($newImg, $src)) return $new_src.'.png';
            break;
        default:
            return $old_src;
    }

    //销毁图片资源
    imagedestroy($newImg);
    imagedestroy($img);
    return false;
}

function utf8_substr($str, $start = 0, $length)
{
    if (function_exists('utf8_substr')) {
        return mb_substr($str, $start, $length, 'UTF-8');
    }
    preg_match_all("/./u", $str, $arr);
    return implode("", array_slice($arr[0], $start, $length));
}

//对象转数组,使用get_object_vars返回对象属性组成的数组
function objectToArray($obj){
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if(is_array($arr)){
        return array_map(__FUNCTION__, $arr);
    }else{
        return $arr;
    }
}

//数组转对象
function arrayToObject($arr){
    if(is_array($arr)){
        return (object) array_map(__FUNCTION__, $arr);
    }else{
        return $arr;
    }
}
use JPush\Client as JPush;
/**
 * @消息推送（最新版本）
 */
function push5($uid,$content,$alias,$type){
    $jg = M('System')->where(array('id'=>1))->find();
    $app_key = $jg['jg_appkey'];
    $master_secret = $jg['jg_secret'];
    //$registration_id = '1a1018970aaf15c3160';
    $client = new JPush($app_key, $master_secret);
    try {
        $response = $client->push()
            ->setPlatform(array('ios', 'android'))
            ->addAlias($alias)
            // ->addRegistrationId($registration_id)
            ->setNotificationAlert($content)
            ->iosNotification($content,array(
                    'sound'=>'default',
                    'badge' => 2,
                    'content-available' => true,
                    'category' => 'jiguang',
                    'extras' => ['user_id'=>$uid,'type'=>$type,'alias'=>$alias]
                )
            )
            ->androidNotification($content,array(
                    'title' => $content,
                    'build_id' => 2,
                    'extras' => array(
                        "user_id"=>$uid,"alias"=>$alias,"type"=>$type),
                )
            )
            ->options([
                'sendno' => 100,
                'time_to_live' => 86400,
                'apns_production' => false,
                'big_push_duration' => 0
            ])
            ->send();
        return 1;
    }catch (APIRequestException $e) {
        return 2;
    } catch (APIConnectionException $e) {
        return 3;
    }

}
/**
 * @消息推送(ios,生产环境)
 */
function push($uid,$title,$content,$img,$nickname,$hx_username,$alias,$is_follow,$apply_show_id,$url){
    $jg = M('System')->where(array('id'=>1))->find();
    $app_key = $jg['jg_appkey'];
    $master_secret = $jg['jg_secret'];
    // 初始化
    import('Vendor.JPush.jpush');
    $client = new \JPush($app_key, $master_secret);
    //return $client->push()->setPlatform('android')->addAlias($alias)->addAndroidNotification($content, $title, 1, array("user_type"=>$type,"message_code"=>$staus,"is_global"=>$global))->setOptions(100000, 86400, null, false)->send();
    try {
        $result = $client->push()->setPlatform(['ios','android'])->addAlias($alias)->
        addAndroidNotification($content, $title, 1, array("user_id"=>$uid,"img"=>$img,"nickname"=>$nickname,"hx_username"=>$hx_username,"alias"=>$alias,"is_follow"=>$is_follow,"apply_show_id"=>$apply_show_id,"url"=>$url))->
        addIosNotification(['alert'=>$content,'sound'=>'','available'=>true,'extras'=>['user_id'=>$uid,'img'=>$img,'nickname'=>$nickname,'hx_username'=>$hx_username,'alias'=>$alias,'is_follow'=>$is_follow,'apply_show_id'=>$apply_show_id,'url'=>$url]])->
        setOptions(100000, 86400, null, true)->send();
        return 1;
    } catch (APIRequestException $e) {
        return 2;
    } catch (APIConnectionException $e) {
        return 3;
    }
}
/**
 * @消息推送(ios,开发环境)
 */
function push3($uid,$title,$content,$img,$nickname,$hx_username,$alias,$is_follow,$apply_show_id,$url){
    $jg = M('System')->where(array('id'=>1))->find();
    $app_key = $jg['jg_appkey'];
    $master_secret = $jg['jg_secret'];
    // 初始化
    import('Vendor.JPush.jpush');
    $client = new \JPush($app_key, $master_secret);
    //return $client->push()->setPlatform('android')->addAlias($alias)->addAndroidNotification($content, $title, 1, array("user_type"=>$type,"message_code"=>$staus,"is_global"=>$global))->setOptions(100000, 86400, null, false)->send();
    try {
        $result = $client->push()->setPlatform(['ios','android'])->addAlias($alias)->
        addAndroidNotification($content, $title, 1, array("user_id"=>$uid,"img"=>$img,"nickname"=>$nickname,"hx_username"=>$hx_username,"alias"=>$alias,"is_follow"=>$is_follow,"apply_show_id"=>$apply_show_id,"url"=>$url))->
        addIosNotification(['alert'=>$content,'sound'=>'','available'=>true,'extras'=>['user_id'=>$uid,'img'=>$img,'nickname'=>$nickname,'hx_username'=>$hx_username,'alias'=>$alias,'is_follow'=>$is_follow,'apply_show_id'=>$apply_show_id,'url'=>$url]])->
        setOptions(100000, 86400, null, false)->send();
        return 1;
    } catch (APIRequestException $e) {
        return 2;
    } catch (APIConnectionException $e) {
        return 3;
    }
}
/**
 * @消息推送2
 */
function push2($alias,$title,$content,$type,$staus,$global,$log,$lag){
    $jg = M('System')->where(array('id'=>1))->find();
    $app_key = $jg['jg_appkey'];
    $master_secret = $jg['jg_secret'];
    // 初始化
    import('Vendor.JPush.jpush');
    $client = new \JPush($app_key, $master_secret);
    //return $client->push()->setPlatform('android')->addAlias($alias)->addAndroidNotification($content, $title, 1, array("user_type"=>$type,"message_code"=>$staus,"is_global"=>$global))->setOptions(100000, 86400, null, false)->send();
    try {
        $result = $client->push()->setPlatform('android')->addAlias($alias)->addAndroidNotification($content, $title, 1, array("user_type"=>$type,"message_code"=>$staus,"is_global"=>$global,"log"=>$log,"lag"=>$lag))->setOptions(100000, 86400, null, false)->send();
        return 1;
    } catch (APIRequestException $e) {
        return 2;
    } catch (APIConnectionException $e) {
        return 3;
    }
}
//生成二维码
function qrcode($url,$filepath, $level=3,$size=4){
    if(!$url) return false;

    Vendor('phpqrcode.phpqrcode');
    //容错级别
    $errorCorrectionLevel =intval($level) ;
    $matrixPointSize = intval($size);//生成图片大小
    //生成二维码图片
    $object = new QRcode();

    $result = $object->png($url, $filepath, $errorCorrectionLevel, $matrixPointSize, 2, true);

}

/**
 * @助通短信发送
 */
function zhutong_sendSMS($content,$mobile){
    $system = M('System')->where(['id'=>1])->field('zhutong_username,zhutong_password')->find();
    $url 		= "http://www.api.zthysms.com/sendSmsBatch.do";//提交地址
    $username 	= $system['zhutong_username'];//用户名
    $password 	= $system['zhutong_password'];//原密码
    $data = array(
        'content'  => $content,//短信内容
        'mobile'   => $mobile,//手机号码
    );
    // 初始化
    import('Vendor.zhutong.zhutong');
    $sendAPI = new \sendAPI($url, $username, $password);
    $sendAPI->data = $data;//初始化数据包
    $return = $sendAPI->sendSMS('POST');//GET or POST
    return $return;
}

/**
 * @融云注册
 */
function get_cloud($userId,$username,$img){
    $ry = M('System')->where(['id'=>1])->find();
    import('Vendor.Rongyun.rongcloud');
    $appKey = $ry['ry_appkey'];
    $appSecret = $ry['ry_secret'];
    //$jsonPath = "jsonsource/";
    $RongCloud = new \RongCloud($appKey,$appSecret);
    // 获取 Token 方法
    $result = $RongCloud->user()->getToken($userId, $username,$img);
    $rs = json_decode($result,true);
    return $rs['token'];
}
/**
 * @更新融云名称、头像
 */
function update_cloud($userId,$username,$img){
    $ry = M('System')->where(['id'=>1])->find();
    import('Vendor.Rongyun.rongcloud');
    $appKey = $ry['ry_appkey'];
    $appSecret = $ry['ry_secret'];
    //$jsonPath = "jsonsource/";
    $RongCloud = new \RongCloud($appKey,$appSecret);
    // 获取 Token 方法
    $result = $RongCloud->user()->refresh($userId, $username,$img);
    $rs = json_decode($result,true);
    return $rs['code'];
}

/**
 * @随机生成8位数字
 */
function get_number(){
    $a = range(0,9);
    for($i=0;$i<8;$i++){
        $b[] = array_rand($a);
    }
    $rs=join("",$b);
    return $rs;
}
/**
 * @随机生成7位数字
 */
function get_number7(){
    $a = range(0,9);
    for($i=0;$i<7;$i++){
        $b[] = array_rand($a);
    }
    $rs=join("",$b);
    return $rs;
}

/**
 * @随机获取汉字
 */
function get_hz($giveStr="",$num=10){
    $str = "北京澜声科技有限公司的主要产品是听见啦金玉良缘冰清玉洁继往开来锦绣山河冰雪聪明功成名就桃花潭水深千尺不及汪伦送我情先帝创业未半而中道今天下三分益州疲弊此诚危急存亡之秋也然侍卫之臣不懈于内忠志之士忘身于外者盖追先帝之殊遇欲报之于陛下也诚宜开张圣听";# 字库
    $newStr  = "";       # 随机生成的包含答案的字符串
    $anLo    = array();  # 设定的答案所在的位置。
    $bit     = 1;        # 位数，在本系统中是utf-8编码，一个中文长度为3
    $anLenth = floor(strlen($giveStr)/$bit); # 答案长度,在UTF编码中，

    # 这些汉字在18个汉字中的位置
    $i = 0;
    while ( $i<$anLenth ) {
        $rd = rand( 0, $num-1 );
        if(in_array($rd,$anLo)) continue; # 保证了不重复。
        $anLo[] = $rd;
        $i++;
    }

    for( $j=0; $j<$num;$j++ ){
        if(in_array($j,$anLo)){
            $k = array_search($j,$anLo);
            $newStr .= mb_substr($giveStr,$k*$bit,$bit); #echo $newStr."<br>";

        } else {
            $rd  = rand(0,(strlen($str)-1)/$bit);
            $wd  = mb_substr($str,$rd*$bit,$bit);
            $str = str_replace($wd, '', $str);
            $newStr .= $wd;
        }
    }
    return $newStr;
}


function GetfourStr($len){
    $chars_array = array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    $charsLen = count($chars_array) - 1;
    $outputstr = "";
    for ($i=0; $i<$len; $i++)
    {
        $outputstr .= $chars_array[mt_rand(0, $charsLen)];
    }
    return $outputstr;
}


/**
 * @获取乐橙token
 */
function get_lecheng_token($id,$phone,$app_id,$app_secret){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "phone:$phone,time:$time,nonce:$rand,appSecret:$app_secret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$app_id,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'phone'=>$phone
        ],
        'id'=>$id
    ]);


    $ch = curl_init('https://openapi.lechange.cn/openapi/accessToken');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    $arr = json_decode($result,true);
    return $arr;
    //return $arr['result']['data']['accessToken'];
}

/**
 * @设备绑定
 */
function bindDevice($id,$token,$deviceId,$appid,$appsecret){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "deviceId:$deviceId,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'deviceId'=>$deviceId,
            'token'=>$token
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/bindDevice');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    $arr = json_decode($result,true);
    return $arr;
}
/**
 * @单个设备信息获取
 */
function deviceInfo($id,$token,$deviceid){
    $rand = md5(time() . mt_rand(0,1000));
    //$token = $this->get_token();
    // $token = 'At_53ca80d360764faca1a73fef479c307a';
    // $deviceid = '2J0198EPAM00191';
    $time = time();
    $arr = "deviceId:$deviceid,token:$token,time:$time,nonce:$rand,appSecret:ea7b837cfa8144a9b08c3533cab23c";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>'lc3ae25ee198a648e7',
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/bindDeviceInfo');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    //print_r($result);
    return json_decode($result,true);
}
/**
 * @获取呼吸灯状态
 */
function breathingLightStatus($id,$deviceid,$token,$appid,$appsecret){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "deviceId:$deviceid,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/breathingLightStatus');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}

/**
 * @设置呼吸灯开关
 * @status   on:开启  off:关闭
 */
function modifyBreathingLight($id,$deviceid,$token,$appid,$appsecret,$status){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "deviceId:$deviceid,status:$status,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid,
            'status'=>$status
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/modifyBreathingLight');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}

/**
 * @获取设备翻转状态
 * normal：正常；reverse：翻转
 */
function frameReverseStatus($id,$deviceid,$token,$appid,$appsecret,$channelid){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "channelId:$channelid,deviceId:$deviceid,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid,
            'channelId'=>$channelid
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/frameReverseStatus');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}
/**
 * @设置设备翻转状态
 * @$direction   normal：正常；reverse：翻转
 */
function modifyFrameReverseStatus($id,$deviceid,$token,$appid,$appsecret,$channelid,$direction){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "channelId:$channelid,deviceId:$deviceid,direction:$direction,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid,
            'channelId'=>$channelid,
            'direction'=>$direction
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/modifyFrameReverseStatus');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}
/**
 * @云台控制
 * @当Operation为move时，表示移动：

H 水平移动速度：范围-8~8，负数向左，正数向右。
V 垂直移动速度：范围-8~8，负数向下，正数向上。（20151207修改成相反，与实际实现保持一致）
Z 变倍倍数：范围0~正无穷，小于1表示缩小，大于1表示放大。

注：三个参数为 0,0,1 时表示立即停止。

Duration表示移动的持续时间，单位毫秒。没有Duration字段或Duration字段填“last”表示一直运动下去。

当Operation为locate时，表示定位：

H 水平位置：归一化到-1~1
V 垂直位置：归一化到-1~1
Z 变倍倍数：归一化到0~1

Duration参数无意义，可省略Duration字段。
 */
function controlPTZ($id,$deviceid,$token,$appid,$appsecret,$channelid,$operation,$h,$v,$z){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "channelId:$channelid,deviceId:$deviceid,duration:2000,h:$h,operation:$operation,token:$token,v:$v,z:$z,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid,
            'channelId'=>$channelid,
            'operation'=>$operation,
            'h'=>$h,
            'v'=>$v,
            'z'=>$z,
            'duration'=>2000
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/controlPTZ');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}
/**
 * @摄像头抓图
 * @$encrypt    true：加密；false：不加密

 */
function setDeviceSnap($id,$deviceid,$token,$appid,$appsecret,$channelid){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "channelId:$channelid,deviceId:$deviceid,encrypt:false,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid,
            'channelId'=>$channelid,
            'encrypt'=>false
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/setDeviceSnap');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}

/**
 * @设备SD卡格式化
 */
function recoverSDCard($id,$deviceid,$token,$appid,$appsecret,$channelid){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "channelId:$channelid,deviceId:$deviceid,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid,
            'channelId'=>$channelid
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/recoverSDCard');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}
/**
 * @设备升级
 */
function upgradeDevice($id,$deviceid,$token,$appid,$appsecret){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "deviceId:$deviceid,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid,
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/upgradeDevice');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}
/**
 * @设备当前连接热点信
 */
function currentDeviceWifi($id,$deviceid,$token,$appid,$appsecret){
    $rand = md5(time() . mt_rand(0,1000));
    $time = time();
    $arr = "deviceId:$deviceid,token:$token,time:$time,nonce:$rand,appSecret:$appsecret";
    $sign = md5($arr);
    $content = json_encode([
        'system'=>
            [
                'ver'=>'1.0',
                'sign'=>$sign,
                'appId'=>$appid,
                'time'=>$time,
                'nonce'=>$rand
            ],
        'params'=>[
            'token'=>$token,
            'deviceId'=>$deviceid,
        ],
        'id'=>$id
    ]);

    //$url = 'https://openapi.lechange.cn/openapi/userToken/'.$content;

    $ch = curl_init('https://openapi.lechange.cn/openapi/currentDeviceWifi');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,20); //超时
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($ch);
    curl_close($ch);//释放句柄
    return json_decode($result,true);
}












/**
 *
 +--------------------------------------------------
 * 切割字符
 +--------------------------------------------------
 * @param $str	字符
 * @param $n	需要留下的字数
 */
function cut($str, $n){
	$length = mb_strlen($str,'UTF8');
	if($n < $length){
		return mb_substr($str, 0, $n, 'UTF-8').'...';
	}else{
		return mb_substr($str, 0, $n, 'UTF-8');
	}
}
//获取表的名称
function list_tables($database)
{
	$rs = mysql_list_tables($database);
	$tables = array();
	while ($row = mysql_fetch_row($rs)) {
		$tables[] = $row[0];
	}
	mysql_free_result($rs);
	return $tables;
}
//导出数据库
function dump_table($table, $fp = null)
{
	$need_close = false;
	if (is_null($fp)) {
		$fp = fopen($table . '.sql', 'w');
		$need_close = true;
	}
	$a=mysql_query("show create table `{$table}`");
	$row=mysql_fetch_assoc($a);fwrite($fp,$row['Create Table'].';');//导出表结构
	$rs = mysql_query("SELECT * FROM `{$table}`");
	while ($row = mysql_fetch_row($rs)) {
		fwrite($fp, get_insert_sql($table, $row));
	}
	mysql_free_result($rs);
	if ($need_close) {
		fclose($fp);
	}
}
//导出表数据
function get_insert_sql($table, $row)
{
	$sql = "INSERT INTO `{$table}` VALUES (";
	$values = array();
	foreach ($row as $value) {
		$values[] = "'" . mysql_real_escape_string($value) . "'";
	}
	$sql .= implode(', ', $values) . ");";
	return $sql;
}

/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}

/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name=null, $value=null,$default=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)){
        $_config = array_merge($_config, array_change_key_case($name,CASE_UPPER));
        return null;
    }
    return null; // 避免非法参数
}

/**
 * 加载配置文件 支持格式转换 仅支持一级配置
 * @param string $file 配置文件名
 * @param string $parse 配置解析方法 有些格式需要用户自己解析
 * @return array
 */
function load_config($file,$parse=CONF_PARSE){
    $ext  = pathinfo($file,PATHINFO_EXTENSION);
    switch($ext){
        case 'php':
            return include $file;
        case 'ini':
            return parse_ini_file($file);
        case 'yaml':
            return yaml_parse_file($file);
        case 'xml': 
            return (array)simplexml_load_file($file);
        case 'json':
            return json_decode(file_get_contents($file), true);
        default:
            if(function_exists($parse)){
                return $parse($file);
            }else{
                E(L('_NOT_SUPPORT_').':'.$ext);
            }
    }
}

/**
 * 解析yaml文件返回一个数组
 * @param string $file 配置文件名
 * @return array
 */
if (!function_exists('yaml_parse_file')) {
    function yaml_parse_file($file) {
        vendor('spyc.Spyc');
        return Spyc::YAMLLoad($file);
    }
}

/**
 * 抛出异常处理
 * @param string $msg 异常消息
 * @param integer $code 异常代码 默认为0
 * @throws Think\Exception
 * @return void
 */
function E($msg, $code=0) {
    throw new Think\Exception($msg, $code);
}

/**
 * 记录和统计时间（微秒）和内存使用情况
 * 使用方法:
 * <code>
 * G('begin'); // 记录开始标记位
 * // ... 区间运行代码
 * G('end'); // 记录结束标签位
 * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
 * echo G('begin','end','m'); // 统计区间内存使用情况
 * 如果end标记位没有定义，则会自动以当前作为标记位
 * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
 * </code>
 * @param string $start 开始标签
 * @param string $end 结束标签
 * @param integer|string $dec 小数位或者m
 * @return mixed
 */
function G($start,$end='',$dec=4) {
    static $_info       =   array();
    static $_mem        =   array();
    if(is_float($end)) { // 记录时间
        $_info[$start]  =   $end;
    }elseif(!empty($end)){ // 统计时间和内存使用
        if(!isset($_info[$end])) $_info[$end]       =  microtime(TRUE);
        if(MEMORY_LIMIT_ON && $dec=='m'){
            if(!isset($_mem[$end])) $_mem[$end]     =  memory_get_usage();
            return number_format(($_mem[$end]-$_mem[$start])/1024);
        }else{
            return number_format(($_info[$end]-$_info[$start]),$dec);
        }

    }else{ // 记录时间和内存使用
        $_info[$start]  =  microtime(TRUE);
        if(MEMORY_LIMIT_ON) $_mem[$start]           =  memory_get_usage();
    }
    return null;
}

/**
 * 获取和设置语言定义(不区分大小写)
 * @param string|array $name 语言变量
 * @param mixed $value 语言值或者变量
 * @return mixed
 */
function L($name=null, $value=null) {
    static $_lang = array();
    // 空参数返回所有定义
    if (empty($name))
        return $_lang;
    // 判断语言获取(或设置)
    // 若不存在,直接返回全大写$name
    if (is_string($name)) {
        $name   =   strtoupper($name);
        if (is_null($value)){
            return isset($_lang[$name]) ? $_lang[$name] : $name;
        }elseif(is_array($value)){
            // 支持变量
            $replace = array_keys($value);
            foreach($replace as &$v){
                $v = '{$'.$v.'}';
            }
            return str_replace($replace,$value,isset($_lang[$name]) ? $_lang[$name] : $name);        
        }
        $_lang[$name] = $value; // 语言定义
        return null;
    }
    // 批量定义
    if (is_array($name))
        $_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
    return null;
}

/**
 * 添加和获取页面Trace记录
 * @param string $value 变量
 * @param string $label 标签
 * @param string $level 日志级别
 * @param boolean $record 是否记录日志
 * @return void|array
 */
function trace($value='[think]',$label='',$level='DEBUG',$record=false) {
    return Think\Think::trace($value,$label,$level,$record);
}

/**
 * 编译文件
 * @param string $filename 文件名
 * @return string
 */
function compile($filename) {
    $content    =   php_strip_whitespace($filename);
    $content    =   trim(substr($content, 5));
    // 替换预编译指令
    $content    =   preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s', '', $content);
    if(0===strpos($content,'namespace')){
        $content    =   preg_replace('/namespace\s(.*?);/','namespace \\1{',$content,1);
    }else{
        $content    =   'namespace {'.$content;
    }
    if ('?>' == substr($content, -2))
        $content    = substr($content, 0, -2);
    return $content.'}';
}

/**
 * 获取模版文件 格式 资源://模块@主题/控制器/操作
 * @param string $template 模版资源地址
 * @param string $layer 视图层（目录）名称
 * @return string
 */
function T($template='',$layer=''){

    // 解析模版资源地址
    if(false === strpos($template,'://')){
        $template   =   'http://'.str_replace(':', '/',$template);
    }
    $info   =   parse_url($template);
    $file   =   $info['host'].(isset($info['path'])?$info['path']:'');
    $module =   isset($info['user'])?$info['user'].'/':MODULE_NAME.'/';
    $extend =   $info['scheme'];
    $layer  =   $layer?$layer:C('DEFAULT_V_LAYER');

    // 获取当前主题的模版路径
    $auto   =   C('AUTOLOAD_NAMESPACE');
    if($auto && isset($auto[$extend])){ // 扩展资源
        $baseUrl    =   $auto[$extend].$module.$layer.'/';
    }elseif(C('VIEW_PATH')){ 
        // 改变模块视图目录
        $baseUrl    =   C('VIEW_PATH');
    }elseif(defined('TMPL_PATH')){ 
        // 指定全局视图目录
        $baseUrl    =   TMPL_PATH.$module;
    }else{
        $baseUrl    =   APP_PATH.$module.$layer.'/';
    }

    // 获取主题
    $theme  =   substr_count($file,'/')<2 ? C('DEFAULT_THEME') : '';

    // 分析模板文件规则
    $depr   =   C('TMPL_FILE_DEPR');
    if('' == $file) {
        // 如果模板文件名为空 按照默认规则定位
        $file = CONTROLLER_NAME . $depr . ACTION_NAME;
    }elseif(false === strpos($file, '/')){
        $file = CONTROLLER_NAME . $depr . $file;
    }elseif('/' != $depr){
        $file   =   substr_count($file,'/')>1 ? substr_replace($file,$depr,strrpos($file,'/'),1) : str_replace('/', $depr, $file);
    }
    return $baseUrl.($theme?$theme.'/':'').$file.C('TMPL_TEMPLATE_SUFFIX');
}

/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name,$default='',$filter=null,$datas=null) {
	static $_PUT	=	null;
	if(strpos($name,'/')){ // 指定修饰符
		list($name,$type) 	=	explode('/',$name,2);
	}elseif(C('VAR_AUTO_STRING')){ // 默认强制转换为字符串
        $type   =   's';
    }
    if(strpos($name,'.')) { // 指定参数来源
        list($method,$name) =   explode('.',$name,2);
    }else{ // 默认为自动判断
        $method =   'param';
    }
    switch(strtolower($method)) {
        case 'get'     :   
        	$input =& $_GET;
        	break;
        case 'post'    :   
        	$input =& $_POST;
        	break;
        case 'put'     :   
        	if(is_null($_PUT)){
            	parse_str(file_get_contents('php://input'), $_PUT);
        	}
        	$input 	=	$_PUT;        
        	break;
        case 'param'   :
            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input  =  $_POST;
                    break;
                case 'PUT':
                	if(is_null($_PUT)){
                    	parse_str(file_get_contents('php://input'), $_PUT);
                	}
                	$input 	=	$_PUT;
                    break;
                default:
                    $input  =  $_GET;
            }
            break;
        case 'path'    :   
            $input  =   array();
            if(!empty($_SERVER['PATH_INFO'])){
                $depr   =   C('URL_PATHINFO_DEPR');
                $input  =   explode($depr,trim($_SERVER['PATH_INFO'],$depr));            
            }
            break;
        case 'request' :   
        	$input =& $_REQUEST;   
        	break;
        case 'session' :   
        	$input =& $_SESSION;   
        	break;
        case 'cookie'  :   
        	$input =& $_COOKIE;    
        	break;
        case 'server'  :   
        	$input =& $_SERVER;    
        	break;
        case 'globals' :   
        	$input =& $GLOBALS;    
        	break;
        case 'data'    :   
        	$input =& $datas;      
        	break;
        default:
            return null;
    }
    if(''==$name) { // 获取全部变量
        $data       =   $input;
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            if(is_string($filters)){
                $filters    =   explode(',',$filters);
            }
            foreach($filters as $filter){
                $data   =   array_map_recursive($filter,$data); // 参数过滤
            }
        }
    }elseif(isset($input[$name])) { // 取值操作
        $data       =   $input[$name];
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            if(is_string($filters)){
                if(0 === strpos($filters,'/') && 1 !== preg_match($filters,(string)$data)){
                    // 支持正则验证
                    return   isset($default) ? $default : null;
                }else{
                    $filters    =   explode(',',$filters);                    
                }
            }elseif(is_int($filters)){
                $filters    =   array($filters);
            }
            
            if(is_array($filters)){
                foreach($filters as $filter){
                    if(function_exists($filter)) {
                        $data   =   is_array($data) ? array_map_recursive($filter,$data) : $filter($data); // 参数过滤
                    }else{
                        $data   =   filter_var($data,is_int($filter) ? $filter : filter_id($filter));
                        if(false === $data) {
                            return   isset($default) ? $default : null;
                        }
                    }
                }
            }
        }
        if(!empty($type)){
        	switch(strtolower($type)){
        		case 'a':	// 数组
        			$data 	=	(array)$data;
        			break;
        		case 'd':	// 数字
        			$data 	=	(int)$data;
        			break;
        		case 'f':	// 浮点
        			$data 	=	(float)$data;
        			break;
        		case 'b':	// 布尔
        			$data 	=	(boolean)$data;
        			break;
                case 's':   // 字符串
                default:
                    $data   =   (string)$data;
        	}
        }
    }else{ // 变量默认值
        $data       =    isset($default)?$default:null;
    }
    is_array($data) && array_walk_recursive($data,'think_filter');
    return $data;
}

function array_map_recursive($filter, $data) {
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val)
         ? array_map_recursive($filter, $val)
         : call_user_func($filter, $val);
    }
    return $result;
 }

/**
 * 设置和获取统计数据
 * 使用方法:
 * <code>
 * N('db',1); // 记录数据库操作次数
 * N('read',1); // 记录读取次数
 * echo N('db'); // 获取当前页面数据库的所有操作次数
 * echo N('read'); // 获取当前页面读取次数
 * </code>
 * @param string $key 标识位置
 * @param integer $step 步进值
 * @param boolean $save 是否保存结果
 * @return mixed
 */
function N($key, $step=0,$save=false) {
    static $_num    = array();
    if (!isset($_num[$key])) {
        $_num[$key] = (false !== $save)? S('N_'.$key) :  0;
    }
    if (empty($step)){
        return $_num[$key];
    }else{
        $_num[$key] = $_num[$key] + (int)$step;
    }
    if(false !== $save){ // 保存结果
        S('N_'.$key,$_num[$key],$save);
    }
    return null;
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type=0) {
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename) {
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

/**
 * 导入所需的类库 同java的Import 本函数有缓存功能
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @return boolean
 */
function import($class, $baseUrl = '', $ext=EXT) {
    static $_file = array();
    $class = str_replace(array('.', '#'), array('/', '.'), $class);
    if (isset($_file[$class . $baseUrl]))
        return true;
    else
        $_file[$class . $baseUrl] = true;
    $class_strut     = explode('/', $class);
    if (empty($baseUrl)) {
        if ('@' == $class_strut[0] || MODULE_NAME == $class_strut[0]) {
            //加载当前模块的类库
            $baseUrl = MODULE_PATH;
            $class   = substr_replace($class, '', 0, strlen($class_strut[0]) + 1);
        }elseif ('Common' == $class_strut[0]) {
            //加载公共模块的类库
            $baseUrl = COMMON_PATH;
            $class   = substr($class, 7);
        }elseif (in_array($class_strut[0],array('Think','Org','Behavior','Com','Vendor')) || is_dir(LIB_PATH.$class_strut[0])) {
            // 系统类库包和第三方类库包
            $baseUrl = LIB_PATH;
        }else { // 加载其他模块的类库
            $baseUrl = APP_PATH;
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl    .= '/';
    $classfile       = $baseUrl . $class . $ext;
    if (!class_exists(basename($class),false)) {
        // 如果类不存在 则导入类库文件
        return require_cache($classfile);
    }
    return null;
}

/**
 * 基于命名空间方式导入函数库
 * load('@.Util.Array')
 * @param string $name 函数库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @return void
 */
function load($name, $baseUrl='', $ext='.php') {
    $name = str_replace(array('.', '#'), array('/', '.'), $name);
    if (empty($baseUrl)) {
        if (0 === strpos($name, '@/')) {//加载当前模块函数库
            $baseUrl    =   MODULE_PATH.'Common/';
            $name       =   substr($name, 2);
        } else { //加载其他模块函数库
            $array      =   explode('/', $name);
            $baseUrl    =   APP_PATH . array_shift($array).'/Common/';
            $name       =   implode('/',$array);
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl       .= '/';
    require_cache($baseUrl . $name . $ext);
}

/**
 * 快速导入第三方框架类库 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
 * @param string $class 类库
 * @param string $baseUrl 基础目录
 * @param string $ext 类库后缀
 * @return boolean
 */
function vendor($class, $baseUrl = '', $ext='.php') {
    if (empty($baseUrl))
        $baseUrl = VENDOR_PATH;
    return import($class, $baseUrl, $ext);
}

/**
 * 实例化模型类 格式 [资源://][模块/]模型
 * @param string $name 资源地址
 * @param string $layer 模型层名称
 * @return Think\Model
 */
function D($name='',$layer='') {
    if(empty($name)) return new Think\Model;
    static $_model  =   array();
    $layer          =   $layer? : C('DEFAULT_M_LAYER');
    if(isset($_model[$name.$layer]))
        return $_model[$name.$layer];
    $class          =   parse_res_name($name,$layer);
    if(class_exists($class)) {
        $model      =   new $class(basename($name));
    }elseif(false === strpos($name,'/')){
        // 自动加载公共模块下面的模型
        if(!C('APP_USE_NAMESPACE')){
            import('Common/'.$layer.'/'.$class);
        }else{
            $class      =   '\\Common\\'.$layer.'\\'.$name.$layer;
        }
        $model      =   class_exists($class)? new $class($name) : new Think\Model($name);
    }else {
        Think\Log::record('D方法实例化没找到模型类'.$class,Think\Log::NOTICE);
        $model      =   new Think\Model(basename($name));
    }
    $_model[$name.$layer]  =  $model;
    return $model;
}

/**
 * 实例化一个没有模型文件的Model
 * @param string $name Model名称 支持指定基础模型 例如 MongoModel:User
 * @param string $tablePrefix 表前缀
 * @param mixed $connection 数据库连接信息
 * @return Think\Model
 */
function M($name='', $tablePrefix='',$connection='') {
    static $_model  = array();
    if(strpos($name,':')) {
        list($class,$name)    =  explode(':',$name);
    }else{
        $class      =   'Think\\Model';
    }
    $guid           =   (is_array($connection)?implode('',$connection):$connection).$tablePrefix . $name . '_' . $class;
    if (!isset($_model[$guid]))
        $_model[$guid] = new $class($name,$tablePrefix,$connection);
    return $_model[$guid];
}

/**
 * 解析资源地址并导入类库文件
 * 例如 module/controller addon://module/behavior
 * @param string $name 资源地址 格式：[扩展://][模块/]资源名
 * @param string $layer 分层名称
 * @param integer $level 控制器层次
 * @return string
 */
function parse_res_name($name,$layer,$level=1){
    if(strpos($name,'://')) {// 指定扩展资源
        list($extend,$name)  =   explode('://',$name);
    }else{
        $extend  =   '';
    }
    if(strpos($name,'/') && substr_count($name, '/')>=$level){ // 指定模块
        list($module,$name) =  explode('/',$name,2);
    }else{
        $module =   defined('MODULE_NAME') ? MODULE_NAME : '' ;
    }
    $array  =   explode('/',$name);
    if(!C('APP_USE_NAMESPACE')){
        $class  =   parse_name($name, 1);
        import($module.'/'.$layer.'/'.$class.$layer);
    }else{
        $class  =   $module.'\\'.$layer;
        foreach($array as $name){
            $class  .=   '\\'.parse_name($name, 1);
        }
        // 导入资源类库
        if($extend){ // 扩展资源
            $class      =   $extend.'\\'.$class;
        }
    }
    return $class.$layer;
}

/**
 * 用于实例化访问控制器
 * @param string $name 控制器名
 * @param string $path 控制器命名空间（路径）
 * @return Think\Controller|false
 */
function controller($name,$path=''){
    $layer  =   C('DEFAULT_C_LAYER');
    if(!C('APP_USE_NAMESPACE')){
        $class  =   parse_name($name, 1).$layer;
        import(MODULE_NAME.'/'.$layer.'/'.$class);
    }else{
        $class  =   ( $path ? basename(ADDON_PATH).'\\'.$path : MODULE_NAME ).'\\'.$layer;
        $array  =   explode('/',$name);
        foreach($array as $name){
            $class  .=   '\\'.parse_name($name, 1);
        }
        $class .=   $layer;
    }
    if(class_exists($class)) {
        return new $class();
    }else {
        return false;
    }
}

/**
 * 实例化多层控制器 格式：[资源://][模块/]控制器
 * @param string $name 资源地址
 * @param string $layer 控制层名称
 * @param integer $level 控制器层次
 * @return Think\Controller|false
 */
function A($name,$layer='',$level=0) {
    static $_action = array();
    $layer  =   $layer? : C('DEFAULT_C_LAYER');
    $level  =   $level? : ($layer == C('DEFAULT_C_LAYER')?C('CONTROLLER_LEVEL'):1);
    if(isset($_action[$name.$layer]))
        return $_action[$name.$layer];
    
    $class  =   parse_res_name($name,$layer,$level);
    if(class_exists($class)) {
        $action             =   new $class();
        $_action[$name.$layer]     =   $action;
        return $action;
    }else {
        return false;
    }
}


/**
 * 远程调用控制器的操作方法 URL 参数格式 [资源://][模块/]控制器/操作
 * @param string $url 调用地址
 * @param string|array $vars 调用参数 支持字符串和数组
 * @param string $layer 要调用的控制层名称
 * @return mixed
 */
function R($url,$vars=array(),$layer='') {
    $info   =   pathinfo($url);
    $action =   $info['basename'];
    $module =   $info['dirname'];
    $class  =   A($module,$layer);
    if($class){
        if(is_string($vars)) {
            parse_str($vars,$vars);
        }
        return call_user_func_array(array(&$class,$action.C('ACTION_SUFFIX')),$vars);
    }else{
        return false;
    }
}

/**
 * 处理标签扩展
 * @param string $tag 标签名称
 * @param mixed $params 传入参数
 * @return void
 */
function tag($tag, &$params=NULL) {
    \Think\Hook::listen($tag,$params);
}

/**
 * 执行某个行为
 * @param string $name 行为名称
 * @param string $tag 标签名称（行为类无需传入） 
 * @param Mixed $params 传入的参数
 * @return void
 */
function B($name, $tag='',&$params=NULL) {
    if(''==$tag){
        $name   .=  'Behavior';
    }
    return \Think\Hook::exec($name,$tag,$params);
}

/**
 * 去除代码中的空白和注释
 * @param string $content 代码内容
 * @return string
 */
function strip_whitespace($content) {
    $stripStr   = '';
    //分析php源码
    $tokens     = token_get_all($content);
    $last_space = false;
    for ($i = 0, $j = count($tokens); $i < $j; $i++) {
        if (is_string($tokens[$i])) {
            $last_space = false;
            $stripStr  .= $tokens[$i];
        } else {
            switch ($tokens[$i][0]) {
                //过滤各种PHP注释
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                //过滤空格
                case T_WHITESPACE:
                    if (!$last_space) {
                        $stripStr  .= ' ';
                        $last_space = true;
                    }
                    break;
                case T_START_HEREDOC:
                    $stripStr .= "<<<THINK\n";
                    break;
                case T_END_HEREDOC:
                    $stripStr .= "THINK;\n";
                    for($k = $i+1; $k < $j; $k++) {
                        if(is_string($tokens[$k]) && $tokens[$k] == ';') {
                            $i = $k;
                            break;
                        } else if($tokens[$k][0] == T_CLOSE_TAG) {
                            break;
                        }
                    }
                    break;
                default:
                    $last_space = false;
                    $stripStr  .= $tokens[$i][1];
            }
        }
    }
    return $stripStr;
}

/**
 * 自定义异常处理
 * @param string $msg 异常消息
 * @param string $type 异常类型 默认为Think\Exception
 * @param integer $code 异常代码 默认为0
 * @return void
 */
function throw_exception($msg, $type='Think\\Exception', $code=0) {
    Think\Log::record('建议使用E方法替代throw_exception',Think\Log::NOTICE);
    if (class_exists($type, false))
        throw new $type($msg, $code);
    else
        Think\Think::halt($msg);        // 异常类型不存在则输出错误信息字串
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

/**
 * 设置当前页面的布局
 * @param string|false $layout 布局名称 为false的时候表示关闭布局
 * @return void
 */
function layout($layout) {
    if(false !== $layout) {
        // 开启布局
        C('LAYOUT_ON',true);
        if(is_string($layout)) { // 设置新的布局模板
            C('LAYOUT_NAME',$layout);
        }
    }else{// 临时关闭布局
        C('LAYOUT_ON',false);
    }
}

/**
 * URL组装 支持不同URL模式
 * @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string|boolean $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $domain 是否显示域名
 * @return string
 */
function U($url='',$vars='',$suffix=true,$domain=false) {
    // 解析URL
    $info   =  parse_url($url);
    $url    =  !empty($info['path'])?$info['path']:ACTION_NAME;
    if(isset($info['fragment'])) { // 解析锚点
        $anchor =   $info['fragment'];
        if(false !== strpos($anchor,'?')) { // 解析参数
            list($anchor,$info['query']) = explode('?',$anchor,2);
        }        
        if(false !== strpos($anchor,'@')) { // 解析域名
            list($anchor,$host)    =   explode('@',$anchor, 2);
        }
    }elseif(false !== strpos($url,'@')) { // 解析域名
        list($url,$host)    =   explode('@',$info['path'], 2);
    }
    // 解析子域名
    if(isset($host)) {
        $domain = $host.(strpos($host,'.')?'':strstr($_SERVER['HTTP_HOST'],'.'));
    }elseif($domain===true){
        $domain = $_SERVER['HTTP_HOST'];
        if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 开启子域名部署
            $domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
            // '子域名'=>array('模块[/控制器]');
            foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                $rule   =   is_array($rule)?$rule[0]:$rule;
                if(false === strpos($key,'*') && 0=== strpos($url,$rule)) {
                    $domain = $key.strstr($domain,'.'); // 生成对应子域名
                    $url    =  substr_replace($url,'',0,strlen($rule));
                    break;
                }
            }
        }
    }

    // 解析参数
    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }
    
    // URL组装
    $depr       =   C('URL_PATHINFO_DEPR');
    $urlCase    =   C('URL_CASE_INSENSITIVE');
    if($url) {
        if(0=== strpos($url,'/')) {// 定义路由
            $route      =   true;
            $url        =   substr($url,1);
            if('/' != $depr) {
                $url    =   str_replace('/',$depr,$url);
            }
        }else{
            if('/' != $depr) { // 安全替换
                $url    =   str_replace('/',$depr,$url);
            }
            // 解析模块、控制器和操作
            $url        =   trim($url,$depr);
            $path       =   explode($depr,$url);
            $var        =   array();
            $varModule      =   C('VAR_MODULE');
            $varController  =   C('VAR_CONTROLLER');
            $varAction      =   C('VAR_ACTION');
            $var[$varAction]       =   !empty($path)?array_pop($path):ACTION_NAME;
            $var[$varController]   =   !empty($path)?array_pop($path):CONTROLLER_NAME;
            if($maps = C('URL_ACTION_MAP')) {
                if(isset($maps[strtolower($var[$varController])])) {
                    $maps    =   $maps[strtolower($var[$varController])];
                    if($action = array_search(strtolower($var[$varAction]),$maps)){
                        $var[$varAction] = $action;
                    }
                }
            }
            if($maps = C('URL_CONTROLLER_MAP')) {
                if($controller = array_search(strtolower($var[$varController]),$maps)){
                    $var[$varController] = $controller;
                }
            }
            if($urlCase) {
                $var[$varController]   =   parse_name($var[$varController]);
            }
            $module =   '';
            
            if(!empty($path)) {
                $var[$varModule]    =   implode($depr,$path);
            }else{
                if(C('MULTI_MODULE')) {
                    if(MODULE_NAME != C('DEFAULT_MODULE') || !C('MODULE_ALLOW_LIST')){
                        $var[$varModule]=   MODULE_NAME;
                    }
                }
            }
            if($maps = C('URL_MODULE_MAP')) {
                if($_module = array_search(strtolower($var[$varModule]),$maps)){
                    $var[$varModule] = $_module;
                }
            }
            if(isset($var[$varModule])){
                $module =   $var[$varModule];
                unset($var[$varModule]);
            }
            
        }
    }

    if(C('URL_MODEL') == 0) { // 普通模式URL转换
        $url        =   __APP__.'?'.C('VAR_MODULE')."={$module}&".http_build_query(array_reverse($var));
        if($urlCase){
            $url    =   strtolower($url);
        }        
        if(!empty($vars)) {
            $vars   =   http_build_query($vars);
            $url   .=   '&'.$vars;
        }
    }else{ // PATHINFO模式或者兼容URL模式
        if(isset($route)) {
            $url    =   __APP__.'/'.rtrim($url,$depr);
        }else{
            $module =   (defined('BIND_MODULE') && BIND_MODULE==$module )? '' : $module;
            $url    =   __APP__.'/'.($module?$module.MODULE_PATHINFO_DEPR:'').implode($depr,array_reverse($var));
        }
        if($urlCase){
            $url    =   strtolower($url);
        }
        if(!empty($vars)) { // 添加参数
            foreach ($vars as $var => $val){
                if('' !== trim($val))   $url .= $depr . $var . $depr . urlencode($val);
            }                
        }
        if($suffix) {
            $suffix   =  $suffix===true?C('URL_HTML_SUFFIX'):$suffix;
            if($pos = strpos($suffix, '|')){
                $suffix = substr($suffix, 0, $pos);
            }
            if($suffix && '/' != substr($url,-1)){
                $url  .=  '.'.ltrim($suffix,'.');
            }
        }
    }
    if(isset($anchor)){
        $url  .= '#'.$anchor;
    }
    if($domain) {
        $url   =  (is_ssl()?'https://':'http://').$domain.$url;
    }
    return $url;
}

/**
 * 渲染输出Widget
 * @param string $name Widget名称
 * @param array $data 传入的参数
 * @return void
 */
function W($name, $data=array()) {
    return R($name,$data,'Widget');
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
    //多行URL地址支持
    $url        = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function S($name,$value='',$options=null) {
    static $cache   =   '';
    if(is_array($options)){
        // 缓存操作的同时初始化
        $type       =   isset($options['type'])?$options['type']:'';
        $cache      =   Think\Cache::getInstance($type,$options);
    }elseif(is_array($name)) { // 缓存初始化
        $type       =   isset($name['type'])?$name['type']:'';
        $cache      =   Think\Cache::getInstance($type,$name);
        return $cache;
    }elseif(empty($cache)) { // 自动初始化
        $cache      =   Think\Cache::getInstance();
    }
    if(''=== $value){ // 获取缓存
        return $cache->get($name);
    }elseif(is_null($value)) { // 删除缓存
        return $cache->rm($name);
    }else { // 缓存数据
        if(is_array($options)) {
            $expire     =   isset($options['expire'])?$options['expire']:NULL;
        }else{
            $expire     =   is_numeric($options)?$options:NULL;
        }
        return $cache->set($name, $value, $expire);
    }
}

/**
 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 * @param string $name 缓存名称
 * @param mixed $value 缓存值
 * @param string $path 缓存路径
 * @return mixed
 */
function F($name, $value='', $path=DATA_PATH) {
    static $_cache  =   array();
    $filename       =   $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            if(false !== strpos($name,'*')){
                return false; // TODO 
            }else{
                unset($_cache[$name]);
                return Think\Storage::unlink($filename,'F');
            }
        } else {
            Think\Storage::put($filename,serialize($value),'F');
            // 缓存数据
            $_cache[$name]  =   $value;
            return null;
        }
    }
    // 获取缓存数据
    if (isset($_cache[$name]))
        return $_cache[$name];
    if (Think\Storage::has($filename,'F')){
        $value      =   unserialize(Think\Storage::read($filename,'F'));
        $_cache[$name]  =   $value;
    } else {
        $value          =   false;
    }
    return $value;
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * XML编码
 * @param mixed $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string $attr 根节点属性
 * @param string $id   数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 * @return string
 */
function xml_encode($data, $root='think', $item='item', $attr='', $id='id', $encoding='utf-8') {
    if(is_array($attr)){
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr   = trim($attr);
    $attr   = empty($attr) ? '' : " {$attr}";
    $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml   .= "<{$root}{$attr}>";
    $xml   .= data_to_xml($data, $item, $id);
    $xml   .= "</{$root}>";
    return $xml;
}

/**
 * 数据XML编码
 * @param mixed  $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id   数字索引key转换为的属性名
 * @return string
 */
function data_to_xml($data, $item='item', $id='id') {
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if(is_numeric($key)){
            $id && $attr = " {$id}=\"{$key}\"";
            $key  = $item;
        }
        $xml    .=  "<{$key}{$attr}>";
        $xml    .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
        $xml    .=  "</{$key}>";
    }
    return $xml;
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 */
function session($name='',$value='') {
    $prefix   =  C('SESSION_PREFIX');
    if(is_array($name)) { // session初始化 在session_start 之前调用
        if(isset($name['prefix'])) C('SESSION_PREFIX',$name['prefix']);
        if(C('VAR_SESSION_ID') && isset($_REQUEST[C('VAR_SESSION_ID')])){
            session_id($_REQUEST[C('VAR_SESSION_ID')]);
        }elseif(isset($name['id'])) {
            session_id($name['id']);
        }
        if('common' != APP_MODE){ // 其它模式可能不支持
            ini_set('session.auto_start', 0);
        }
        if(isset($name['name']))            session_name($name['name']);
        if(isset($name['path']))            session_save_path($name['path']);
        if(isset($name['domain']))          ini_set('session.cookie_domain', $name['domain']);
        if(isset($name['expire']))          {
            ini_set('session.gc_maxlifetime',   $name['expire']);
            ini_set('session.cookie_lifetime',  $name['expire']);
        }
        if(isset($name['use_trans_sid']))   ini_set('session.use_trans_sid', $name['use_trans_sid']?1:0);
        if(isset($name['use_cookies']))     ini_set('session.use_cookies', $name['use_cookies']?1:0);
        if(isset($name['cache_limiter']))   session_cache_limiter($name['cache_limiter']);
        if(isset($name['cache_expire']))    session_cache_expire($name['cache_expire']);
        if(isset($name['type']))            C('SESSION_TYPE',$name['type']);
        if(C('SESSION_TYPE')) { // 读取session驱动
            $type   =   C('SESSION_TYPE');
            $class  =   strpos($type,'\\')? $type : 'Think\\Session\\Driver\\'. ucwords(strtolower($type));
            $hander =   new $class();
            session_set_save_handler(
                array(&$hander,"open"), 
                array(&$hander,"close"), 
                array(&$hander,"read"), 
                array(&$hander,"write"), 
                array(&$hander,"destroy"), 
                array(&$hander,"gc")); 
        }
        // 启动session
        if(C('SESSION_AUTO_START'))  session_start();
    }elseif('' === $value){ 
        if(''===$name){
            // 获取全部的session
            return $prefix ? $_SESSION[$prefix] : $_SESSION;
        }elseif(0===strpos($name,'[')) { // session 操作
            if('[pause]'==$name){ // 暂停session
                session_write_close();
            }elseif('[start]'==$name){ // 启动session
                session_start();
            }elseif('[destroy]'==$name){ // 销毁session
                $_SESSION =  array();
                session_unset();
                session_destroy();
            }elseif('[regenerate]'==$name){ // 重新生成id
                session_regenerate_id();
            }
        }elseif(0===strpos($name,'?')){ // 检查session
            $name   =  substr($name,1);
            if(strpos($name,'.')){ // 支持数组
                list($name1,$name2) =   explode('.',$name);
                return $prefix?isset($_SESSION[$prefix][$name1][$name2]):isset($_SESSION[$name1][$name2]);
            }else{
                return $prefix?isset($_SESSION[$prefix][$name]):isset($_SESSION[$name]);
            }
        }elseif(is_null($name)){ // 清空session
            if($prefix) {
                unset($_SESSION[$prefix]);
            }else{
                $_SESSION = array();
            }
        }elseif($prefix){ // 获取session
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$prefix][$name1][$name2])?$_SESSION[$prefix][$name1][$name2]:null;  
            }else{
                return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;                
            }            
        }else{
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null;  
            }else{
                return isset($_SESSION[$name])?$_SESSION[$name]:null;
            }            
        }
    }elseif(is_null($value)){ // 删除session
        if(strpos($name,'.')){
            list($name1,$name2) =   explode('.',$name);
            if($prefix){
                unset($_SESSION[$prefix][$name1][$name2]);
            }else{
                unset($_SESSION[$name1][$name2]);
            }
        }else{
            if($prefix){
                unset($_SESSION[$prefix][$name]);
            }else{
                unset($_SESSION[$name]);
            }
        }
    }else{ // 设置session
		if(strpos($name,'.')){
			list($name1,$name2) =   explode('.',$name);
			if($prefix){
				$_SESSION[$prefix][$name1][$name2]   =  $value;
			}else{
				$_SESSION[$name1][$name2]  =  $value;
			}
		}else{
			if($prefix){
				$_SESSION[$prefix][$name]   =  $value;
			}else{
				$_SESSION[$name]  =  $value;
			}
		}
    }
    return null;
}

/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $option cookie参数
 * @return mixed
 */
function cookie($name='', $value='', $option=null) {
    // 默认设置
    $config = array(
        'prefix'    =>  C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire'    =>  C('COOKIE_EXPIRE'), // cookie 保存时间
        'path'      =>  C('COOKIE_PATH'), // cookie 保存路径
        'domain'    =>  C('COOKIE_DOMAIN'), // cookie 有效域名
        'secure'    =>  C('COOKIE_SECURE'), //  cookie 启用安全传输
        'httponly'  =>  C('COOKIE_HTTPONLY'), // httponly设置
    );
    // 参数设置(会覆盖黙认设置)
    if (!is_null($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config     = array_merge($config, array_change_key_case($option));
    }
    if(!empty($config['httponly'])){
        ini_set("session.cookie_httponly", 1);
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return null;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return null;
    }elseif('' === $name){
        // 获取全部的cookie
        return $_COOKIE;
    }
    $name = $config['prefix'] . str_replace('.', '_', $name);
    if ('' === $value) {
        if(isset($_COOKIE[$name])){
            $value =    $_COOKIE[$name];
            if(0===strpos($value,'think:')){
                $value  =   substr($value,6);
                return array_map('urldecode',json_decode(MAGIC_QUOTES_GPC?stripslashes($value):$value,true));
            }else{
                return $value;
            }
        }else{
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            if(is_array($value)){
                $value  = 'think:'.json_encode(array_map('urlencode',$value));
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
            $_COOKIE[$name] = $value;
        }
    }
    return null;
}

/**
 * 加载动态扩展文件
 * @var string $path 文件路径
 * @return void
 */
function load_ext_file($path) {
    // 加载自定义外部文件
    if($files = C('LOAD_EXT_FILE')) {
        $files      =  explode(',',$files);
        foreach ($files as $file){
            $file   = $path.'Common/'.$file.'.php';
            if(is_file($file)) include $file;
        }
    }
    // 加载自定义的动态配置文件
    if($configs = C('LOAD_EXT_CONFIG')) {
        if(is_string($configs)) $configs =  explode(',',$configs);
        foreach ($configs as $key=>$config){
            $file   = is_file($config)? $config : $path.'Conf/'.$config.CONF_EXT;
            if(is_file($file)) {
                is_numeric($key)?C(load_config($file)):C($key,load_config($file));
            }
        }
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code) {
    static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:'.$code.' '.$_status[$code]);
    }
}

function think_filter(&$value){
	// TODO 其他安全过滤

	// 过滤查询特殊字符
    if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i',$value)){
        $value .= ' ';
    }
}

// 不区分大小写的in_array实现
function in_array_case($value,$array){
    return in_array(strtolower($value),array_map('strtolower',$array));
}
require 'utils.php';
require 'home.php';
require 'checkcheck.php';




