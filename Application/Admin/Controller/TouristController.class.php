<?php

namespace Admin\Controller;


class TouristController extends CommonController {
	
	function login(){
		
		if ($_SESSION["user"]["id"]) {
			$this->redirect("Index/index");
		}
        $this->assign('title',M('System')->getFieldById(1,'title'));
		$this->display();
	}
	function checkLogin(){
		$code = I("checkcode");
		if(!checkCode($code)){
			$this->error("验证码错误",U('Tourist/login'));
		}
		$username = I("username");
		$password = I("password");
		$where["username"]=$username;
		$where["password"]=md5($password);
		$U = D("Member");
		$user = $U->where($where)->find();
		if(!$user){
			$this->error("用户名密码不正确",U('Tourist/login'));
		}
		if($user["status"]!="admin"&&$user["status"]!=1){
			$this->error("用户名被禁用",U('Tourist/login'));
		}
		session("user",$user);
		
		if(C("quanxian")&&$user["username"]!="admin"){
			session("roleid",$user["roleid"]);
		}else{
			session("roleid","admin");
		}
		$this->success("登录成功",U('Index/index'));
	}
	public function logout() {
		session ( "user", null );
		$this->success ( '注销成功！' ,U("Tourist/login"));
	}
	
}