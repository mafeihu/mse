<?php

namespace App\Controller;

use Think\Controller;

class CommonController extends Controller {
	function curl_get($url){
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
}
