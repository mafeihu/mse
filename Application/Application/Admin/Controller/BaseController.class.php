<?php
namespace  Admin\Controller;
/**
 * 友情链接
 * @author 
 *
 */
use Think\Db;

class BaseController extends CommonController {
    function _initialize() {
        $nums = ['5','10','15','20','25','30','50','100'];
        $this->assign('nums',$nums);
    }
	// 主页面
	// 获得数据
	function index() {
		$Base = D ( "Base" );
		$data = $Base->order ( "paixu" )->select ();
		$this->assign ( "base", $data );
		$this->assign ( 'pagetitle', '站点设置' );
		$this->display ();
	}
	
	// 跳转到修改页面
	function toupdate() {
		$id = I ( "id", 0 );
		$B = D ( "System" );
		$base = $B->find ();	
		$this->assign ( "base", $base );
		$this->assign ( 'pagetitle', '站点设置-修改' );
		$this->display ();
	}
	
	// 执行修改
	function doupdate() {
		//dump($_POST);exit;
		$B = D ( 'System' );
		$B->create ();
		$B->updatetime = getCurrentTime ();
		$r = $B->save ();
		if ($r) {
			$this->success ( '修改成功！',U('toupdate')  );
		} else {
			$this->error ( "修改失败:" . $B->getDbError () );
		}
	}
	//banner
	function banner(){
		$count = M('Banner')->count();//一共有多少条记录
		$p = getpage($count,10);
		$ban = M('Banner')->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('ban',$ban);
		$this->assign("show",$p->show());
		$this->assign ( 'pagetitle', 'Banner' );
	    $this->display();
	}
	//添加
	function toadd(){
	   $this->assign ( 'pagetitle', 'banner添加' );
	   $this->display('add'); 
	}
	//添加实现
	function doadd(){
	    $data['b_img']    = I('logo');
        $data['b_city']   = I('diqu');
        $data['b_content']=  $_POST['content'];
	    $data['b_intime'] = time();
	    if (M('Banner')->add($data)){
	       $this->success('添加成功',U('banner'));
	    }else {
	       $this->error('添加失败',U('banner'));
	    }
	}
	/**
	 * @删除（单个）
	 */
	public function del(){
	    if (isset($_GET['b_id'])){
			if (M('Banner')->delete(intval($_GET['b_id']))){
				$this->success('删除成功！',U('banner'));
			}else {
				$this->success('删除失败！',U('banner'));
			}
			
		 }
	}
	/**
	 * @删除(多个)
	 */
    public function delete(){
		if (!empty($_POST['chois'])){
			$id = implode(',', $_POST['chois']);
			if (M('Banner')->delete($id)){
				$this->success('删除成功！',U('banner'));
			}else {
				$this->success('删除失败！',U('banner'));
			}
		}
	 } 
	/**
	 * @修改
	 */
    public function upbanner(){
        $city = M('Unify')->where(['type'=>2])->select();
        $this->assign('city',$city);
		if (!empty($_GET['b_id'])){
		   $hots = M('Banner')->find(intval($_GET['b_id']));
		   $this->assign('hots',$hots);
		   $this->assign ( 'pagetitle', '修改' );
		   $this->display('upbanner');
		}
	 }
	/**
	 * @实现修改
	 */
    public function doupbanner(){
		header("Content-Type:text/html; charset=utf-8");
		if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
	    unset($_POST['__hash__']);
	    if(IS_POST){
	    	$data['b_id']        =  intval($_POST['id']);
            $data['b_city']      = I('diqu');
	    	$data['b_img']       =  $_POST['logo'];
            $data['b_content']   =  $_POST['content'];
	        if (M('Banner')->save($data)){
	    		$this->success('修改成功！',U('banner'));
	    	}else {
	    		$this->error('修改失败！',U('banner'));
	    	}
	    }
	 } 
	/**
	 * @数据库备份
	 */
	 public function backup(){
	     $this->assign ( 'pagetitle', '数据库备份' );
		 $this->display();
	 }
     function backup_do(){
		$database=C('DB_NAME');//数据库名
		$options=array(
				'hostname' => C('DB_HOST'),//ip地址
				'charset'  => C('DB_CHARSET'),//编码
				'filename' => $_POST['name'].'.sql',//文件名
				'username' => C('DB_USER'),
				'password' => C('DB_PWD')       //密码
		);
		mysql_connect($options['hostname'],$options['username'],$options['password'])or die("不能连接数据库!");
		mysql_select_db($database) or die("数据库名称错误!");
		mysql_query("SET NAMES '{$options['charset']}'");

		$tables = list_tables($database);
		$filename = sprintf($options['filename'],$database);
		$fp = fopen('./sql/'.$filename, 'w');
		foreach ($tables as $table) {
			dump_table($table, $fp);
		}
		fclose($fp);
		$file_name=$options['filename'];
		Header("Content-type:application/octet-stream");
		Header("Content-Disposition:attachment;filename=".$file_name);
		readfile($file_name);
		exit;
	}
	/**
     * @礼物列表
     */
	public function gift_list(){
	    $data = [];
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Gift')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Gift')->limit($p->firstRow.','.$p->listRows)->where($data)->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '礼物列表' );
	    $this->display();
    }
    /**
     * @添加(修改映射)
     */
    public function toadd_gift(){
        $id = I('id');
        if (empty($id)){
            $this->assign ( 'pagetitle', '添加' );
        }else{
            $p = M('Gift')->find($id);
            $this->assign('p',$p);
            $this->assign ( 'pagetitle', '编辑' );
        }
        $this->display();
    }
    /**
     * @添加(修改)
     */
    public function doadd_gift(){
        $id = I('id');
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [
            'name'=>I('name'),
            'img'=>I('logo'),
            'price'=>I('price'),
            'experience'=>I('experience'),
            'is_running'=>I('is_running'),
            'is_special'=>I('is_special')
        ];
        if (empty($id)){
            $data['intime'] = time();
            M('Gift')->add($data) ? $this->success('添加成功!',U('gift_list')) : $this->error('添加失败!',U('gift_list')) ;
        }else{
            $data['uptime'] = time();
            M('Gift')->where(['gift_id'=>$id])->save($data) ? $this->success('编辑成功!',U('gift_list')) : $this->error('编辑失败!',U('gift_list')) ;
        }
    }
    /**
     * @删除
     */
    public function del_gift(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('ids');
        $rs = M('Gift')->where(['gift_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }


    /**
     * @资料管理(星座、地区、学历、职业、兴趣、性格、年龄)
     */
    public function unify(){
        $type = ['1'=>'星座','2'=>'地区','3'=>'学历','4'=>'职业','5'=>'兴趣','6'=>'性格','7'=>'年龄'];
        $this->assign('type',$type);

        $data = [];
        if (!empty($_GET['type'])){
            $data['type'] = $_GET['type'];
            $this->assign('lei',$_GET['type']);
        }
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Unify')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Unify')->limit($p->firstRow.','.$p->listRows)->where($data)->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '资料管理' );
        $this->display();
    }

    /**
     * @添加(修改)映射
     */
    public function toadd_unify(){
        $type = ['1'=>'星座','2'=>'地区','3'=>'学历','4'=>'职业','5'=>'兴趣','6'=>'性格','7'=>'年龄'];
        $this->assign('type',$type);

        $id = I('id');
        if (empty($id)){
            $this->assign ( 'pagetitle', '添加' );
        }else{
            $u = M('Unify')->find($id);
            $this->assign('u',$u);
            $this->assign ( 'pagetitle', '编辑' );
        }
        $this->display();
    }
    /**
     * @添加(修改)
     */
    public function doadd_unify(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $data = [
            'name'=>I('name'),
            'img'=>I('logo'),
            'type'=>I('leixing'),
        ];
        if (empty($id)){
            $data['intime'] = time();
            M('Unify')->add($data) ? $this->success('添加成功!',U('unify')) : $this->error('添加失败',U('unify'));
        }else{
            $data['uptime'] = time();
            $un = M('Unify')->find($id);
            if (M('Unify')->where(['unify_id'=>$id])->save($data)){
                if ($un['img']!=I('logo')){
                    unlink($un['img']);
                }
                $this->success('编辑成功!',U('unify'));
            }else{
                $this->error('编辑失败',U('unify'));
            }
        }
    }
    /**
     * @删除
     */
    public function del_unify(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('ids');
        $rs = M('Unify')->where(['unify_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }

    /**
     * @敏感词
     */
    public function sensitive_word(){
        $this->assign ( 'pagetitle', '敏感词' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,sensitive_word')->where(['id'=>1])->find();
            $this->assign('s',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['sensitive_word'=>I('sensitive_word'),'uptime'=>time()]) ? $this->success('成功!',U('sensitive_word')) : $this->error('失败!',U('sensitive_word')) ;
        }
    }
    /**
     * @关于我们
     */
    public function about_us(){
        $this->assign ( 'pagetitle', '关于我们' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,about_us')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['about_us'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('about_us')) : $this->error('失败!',U('about_us')) ;
        }
    }
    /**
     * @用户协议
     */
    public function xieyi(){
        $this->assign ( 'pagetitle', '用户协议' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,xieyi')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['xieyi'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('xieyi')) : $this->error('失败!',U('xieyi')) ;
        }
    }
    /**
     * @举报类型
     */
    public function report_type(){
        $data = [];
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Report_type')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Report_type')->limit($p->firstRow.','.$p->listRows)->where($data)->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '举报类型' );
        $this->display();
    }

    /**
     * @标签
     */
    public function lebel(){
        $data = [];
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Lebel')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Lebel')->limit($p->firstRow.','.$p->listRows)->where($data)->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '标签列表' );
        $this->display();
    }

    /**
     * @添加(修改)映射
     */
    public function toadd_lebel(){
        $id = I('id');
        if (empty($id)){
            $this->assign ( 'pagetitle', '添加' );
        }else{
            $u = M('Lebel')->find($id);
            $this->assign('u',$u);
            $this->assign ( 'pagetitle', '编辑' );
        }
        $this->display();
    }
    /**
     * @添加(修改)
     */
    public function doadd_lebel(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $data = [
            'name'=>I('name'),
            'type'=>I('type'),
            'img'=>I('logo')
        ];
        if (empty($id)){
            $data['intime'] = time();
            M('Lebel')->add($data) ? $this->success('添加成功!',U('lebel')) : $this->error('添加失败',U('lebel'));
        }else{
            $data['uptime'] = time();
            if (M('Lebel')->where(['lebel_id'=>$id])->save($data)){
                $this->success('编辑成功!',U('lebel'));
            }else{
                $this->error('编辑失败',U('lebel'));
            }
        }
    }
    /**
     * @删除
     */
    public function del_lebel(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('ids');
        $rs = M('Lebel')->where(['lebel_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }

    /*********************************价格列表********************************************/
    public function price_list(){
        $data = [];
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Price')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Price')->limit($p->firstRow.','.$p->listRows)->where($data)->select();
        $this->assign('list',$list);
        $this->assign ( 'pagetitle', '价格列表' );
        $this->assign("show",$p->show());
        $this->display();
    }
    /**
     * @添加(修改)映射
     */
    public function toadd_price(){
        $id = I('id');
        if (empty($id)){
            $this->assign ( 'pagetitle', '添加' );
        }else{
            $u = M('Price')->find($id);
            $this->assign('u',$u);
            $this->assign ( 'pagetitle', '编辑' );
        }
        $this->display();
    }
    /**
     * @添加(修改)
     */
    public function doadd_price(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $data = [
            'price'=>I('price'),
            'meters'=>I('meters')
        ];
        if (empty($id)){
            $data['intime'] = time();
            M('Price')->add($data) ? $this->success('添加成功!',U('price_list')) : $this->error('添加失败',U('price_list'));
        }else{
            $data['uptime'] = time();
            if (M('Price')->where(['price_id'=>$id])->save($data)){
                $this->success('编辑成功!',U('price_list'));
            }else{
                $this->error('编辑失败',U('price_list'));
            }
        }
    }
    /**
     * @删除
     */
    public function del_price(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('ids');
        $rs = M('Price')->where(['price_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }

    /**
     * @系统消息
     */
    public function message(){
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $data = ['is_del'=>1];
        $this->assign("nus",$nus);
        $count = M('Notice')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Notice')->limit($p->firstRow.','.$p->listRows)->where($data)->order('ctime desc')->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '系统消息' );
        $this->display();
    }
    /**
     * @添加(修改)映射
     */
    public function toadd_message(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        if (empty($id)){
            $this->assign ( 'pagetitle', '添加' );
        }else{
            $u = M('Notice')->find($id);
            $this->assign('u',$u);
            $this->assign ( 'pagetitle', '编辑' );
        }
        $this->display();
    }
    /**
     * @添加(修改)
     */
    public function doadd_message(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $data = [
            'content'=>I('content'),
            'state'=>I('state'),
            'ctime'=>time()
        ];
        if (empty($id)){
            M('Notice')->add($data) ? $this->success('添加成功!',U('message')) : $this->error('添加失败',U('message'));
        }else{
            if (M('Notice')->where(['id'=>$id])->save($data)){
                $this->success('编辑成功!',U('message'));
            }else{
                $this->error('编辑失败',U('message'));
            }
        }
    }
    /**
     * @发送
     */
    public function send(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $no = M('Notice')->find();
        $data = [
            'is_del'=>1
        ];
        $user = M('User')->where($data)->select();
        if (M('Notice')->where(['id'=>$id])->save(['status'=>2,'stime'=>time()])){
            foreach ($user as $k=>$v){
                M('Message')->add(['type'=>1,'user_id2'=>$v['user_id'],'content'=>$no['content'],'intime'=>time(),'date'=>date('Y-m-d',time())]);

                //极光推送
//                push5($v['user_id'],$no['content'],$v['alias'],1);

                set_time_limit(0);
            }
            $this->success('发送成功!',U('message'));
        }else{
            $this->error('发送失败',U('message'));
        }
    }
    /**
     * @删除
     */
    public function del_message(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('ids');
        $rs = M('Notice')->where(['id'=>['in',$id]])->save(['is_del'=>2]);
        echo $rs ? 1 : 2;
    }

    /**
     * @公开课
     */
    public function open_course(){
        $this->assign ( 'pagetitle', '公开课' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,open_course')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['open_course'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('open_course')) : $this->error('失败!',U('open_course')) ;
        }
    }

    /**
     * @内训视频
     */
    public function inne_video(){
        $this->assign ( 'pagetitle', '内训视频' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,inne_video')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['inne_video'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('inne_video')) : $this->error('失败!',U('inne_video')) ;
        }
    }

    /**
     * @内训课
     */
    public function inne_course(){
        $this->assign ( 'pagetitle', '内训课' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,inne_course')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['inne_course'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('inne_course')) : $this->error('失败!',U('inne_course')) ;
        }
    }

    /**
     * @应用技术
     */
    public function applications(){
        $this->assign ( 'pagetitle', '应用技术' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,applications')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['applications'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('applications')) : $this->error('失败!',U('applications')) ;
        }
    }

    /**
     * @学员分享
     */
    public function student_share(){
        $this->assign ( 'pagetitle', '学员分享' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,student_share')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['student_share'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('student_share')) : $this->error('失败!',U('student_share')) ;
        }
    }

    /**
     * @钻石兑换人民币比例
     */
    public function diamond_exchange(){
        $this->assign ( 'pagetitle', '钻石兑换人民币比例' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,diamond_exchange')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['diamond_exchange'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('diamond_exchange')) : $this->error('失败!',U('diamond_exchange')) ;
        }
    }

    /**
     * @银票兑换人民币比例
     */
    public function bank_notes_exchange(){
        $this->assign ( 'pagetitle', '银票兑换人民币比例' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,bank_notes_exchange')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['bank_notes_exchange'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('bank_notes_exchange')) : $this->error('失败!',U('bank_notes_exchange')) ;
        }
    }

    /**
     * @打赏－主播收益比
     */
    public function give_anchor_exchange(){
        $this->assign ( 'pagetitle', '打赏－主播收益比' );
        $id = I('id');
        if (empty($id)){
            $s = M('System')->field('id,give_anchor_exchange')->where(['id'=>1])->find();
            $this->assign('a',$s);
            $this->display();
        }else{
            M('System')->where(['id'=>$id])->save(['give_anchor_exchange'=>I('content'),'uptime'=>time()]) ? $this->success('成功!',U('give_anchor_exchange')) : $this->error('失败!',U('give_anchor_exchange')) ;
        }
    }

}