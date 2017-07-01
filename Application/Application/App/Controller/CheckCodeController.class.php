<?php

namespace App\Controller;

class CheckCodeController extends CommonController {
	function getCode() {
		getCode();
	}
	function checkCode(){
		$Verify = new \Think\Verify ();
		$r = $Verify->check("4415");
		dump($r);
	}
}