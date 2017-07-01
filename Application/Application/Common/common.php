<?php
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

function curl_get($url)
{
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOP_TIMEOUT, "60");
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //运行curl，结果以jason形式返回
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
?>
