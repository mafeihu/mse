<?php 
namespace App\Controller;
/**
 * 用于省市区三级联运action
 * @author yyb
 *
 */
class AddressController extends CommonController{
	
	/**
	 * 通过省id获取市列表,并返回ajax数据
	 */
	function getCityByProvince(){
		$C = D("City");
		$where["pid"]=I("pid",0);
		$where["status"]=1;
		$city = $C->where($where)->select();
		if($city){
			$this->ajaxReturn(array("sta"=>1,"data"=>$city));
		}else{
			$this->ajaxReturn(array("sta"=>2,"msg"=>"城市列表获取失败,请稍候重试"));
		}
	}
	/**
	 * 通过省id获取市列表,并返回ajax数据
	 */
	function getAreaByCity(){
		$C = D("Area");
		$where["pid"]=I("pid",0);
		$where["status"]=1;
		$area = $C->where($where)->select();
		if($area){
			$this->ajaxReturn(array("sta"=>1,"data"=>$area));
		}else{
			$this->ajaxReturn(array("sta"=>2,"msg"=>"区县列表获取失败,请稍候重试"));
		}
	}
}
	