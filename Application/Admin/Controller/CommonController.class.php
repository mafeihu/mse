<?php

namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller {
	
	function _initialize() {

		if (method_exists ( $this, '_first' ))
			$this->_first ();
		if (method_exists ( $this, '_second' ))
			$this->_second ();
		
	}
// 	function _empty(){
// 		 header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
//         $this->display("Public:404");
// 	}
}