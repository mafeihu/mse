<?php
namespace  Admin\Controller;
/**
 * (说明)
 * @abstract
 * @access public
 */
class MenusController extends CommonController{
		
	/**
	 * (功能描述)
	 *
	 * @param    (类型)     (参数名)    (描述)
	 */
	function index(){
		$keyword = empty($_POST['keyword'])?"":trim($_POST['keyword']);
		$ftype   = $_POST['ftype'];
		$condition = 'level = 2';
		if ($keyword != null and $keyword != "") {
			$condition = $ftype." like '%".$keyword."%'";
		}
		$num=I("num",10);
		import('ORG.Util.Page');
		$model = D('Menus');
		$count = $model->where($condition)->count();
		$page = new \Think\Page($count,$num);
		$page->setConfig('header','条记录');
		$show = $page->show();
		$list = $model->where($condition)->order('px asc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('num',$num);
		$this->assign('show',$show);
		$this->assign('pagetitle','菜单管理');
		$this->display();		
	}

	function index2(){
		$id = I("id");
		$this->assign('pid',$id);
		$condition = 'pid = '.$id;
		$num=empty($_GET['num'])?10:$_GET['num'];
		import('ORG.Util.Page');
		$model = D('Menus');
		$count = $model->where($condition)->count();
		$page = new \Think\Page($count,$num);
		$page->setConfig('header','条记录');
		$show = $page->show();
		$list = $model->where($condition)->order('px desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('num',$num);
		$this->assign('show',$show);
		$this->assign('pagetitle','二级菜单管理');
		$this->display();
	}
	
    /**
     * (功能描述)
     *
     * @param    (类型)     (参数名)    (描述)
     */
    function add(){
		$this->display();
    }
    /**
     * (功能描述)
     *
     * @param    (类型)     (参数名)    (描述)
     */
    function add2(){
    	$this->assign('pid',I("pid"));
		$this->display();
    }

	/**
	 * (功能描述)
	 *
	 * @param    (类型)     (参数名)    (描述)
	 */
	function insert(){
		$model = D('Menus');
		$title = I('title');
		$status = I('status');
		$data = array(
					'title'=>$title,
					'status'=>$status,
					'pid'=>0,
					'level'=>2,
				    'px'=>I('px'),
				);
		if ($model->add($data)) {
			$this->success("添加成功",U("index"));
		}else{
			$this->error("添加失败".$model->getDbError());
		}
	}
	/**
	 * (功能描述)
	 *
	 * @param    (类型)     (参数名)    (描述)
	 */
	function insert2(){
		$model = D('Menus');
		$title = I('title');
		$status = I('status');
		$data = array(
					'title'=>$title,
					'status'=>$status,
					'pid'=>I('pid'),
					'url'=>I('url'),
				    'px'=>I('px'),
					'level'=>3
				);
		if ($model->add($data)) {
			$this->success("添加成功",U("index"));
		}else{
			$this->error("添加失败".$model->getDbError());
		}
	}
	
	function edit(){
		$id = I('id');
		$model = D('Menus');
		$vo = $model->getById($id);
		$this->assign('vo',$vo);
		$this->display();
	}
	function edit2(){
		$id = I('id');
		$model = D('Menus');
		$vo = $model->getById($id);
		$this->assign('vo',$vo);
		$this->display();
	}
	
	/*function update(){
		
		$model = D('Menus');
		$title = I('title');
		$status = I('status');
		$id = I('id');
		$px=I('px');
		$res = $model->execute("update think_menus set status = ".$status.", title = '".$title."' where id = ".$id);
		if ($res) {
			$this->success("修改成功！",__URL__.'/index',2);
		}else{
			$this->error("修改失败");
		}
	}*/
	function update(){
	
		$model = D('Menus');
		$title = I('title');
		$status = I('status');
		$id = I('id');
		$data = $model->getById($id);
		$data['title']=$title;
		$data['status']=$status;
		$data['px']=I('px');
		$data['updatetime']=getCurrentTime();
		$res = $model->save($data);
		if ($res) {
			$this->success("修改成功！",U("index"));
		}else{
			$this->error("修改失败");
		}
	}
	function update2(){
		
		$model = D('Menus');
		$id = I('id');
		$data["px"]=I('px');
		$data["title"]=I('title');
		$data["status"]=I('status');
		$data["url"]=I('url');
		//$res = $model->execute("update think_menus set status = ".$status.",px='".$px."' title = '".$title."',url= '".$url."' where id = ".$id);
		$res = $model->where(array("id"=>I('id')))->save($data);
		if ($res) {
			$this->success("修改成功！",U("index"));
		}else{
			$this->error("修改失败");
		}
	}

	/**
	 * (功能描述)
	 *
	 * @param    (类型)     (参数名)    (描述)
	 */
	function delete(){
		$did=$_POST['chois'];
		if(!empty($did) && is_array($did)){
			$model = D('Menus');
			$id=implode(',',$did);
			
			if(false!==$model->where('id in('.$id.')')->delete()){
				//将二级菜单删除掉
				$model->where('pid in('.$id.')')->delete();
				$this->success('删除成功',U("index"));
			}else{
				$this->error('删除用户失败：'.$model->getDbError());
			}
		}else{
			$this->error('请选择要删除的数据');
		}
	}




}
?>