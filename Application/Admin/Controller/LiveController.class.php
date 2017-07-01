<?php
namespace  Admin\Controller;
/**
 * 用户
 * @author 
 *
 */
use Think\Db;
use Think\Image\Driver\Imagick;

class LiveController extends CommonController {
    function _initialize() {
        $nums = ['5','10','15','20','25','30','50','100'];
        $this->assign('nums',$nums);
    }
	/**
     * @直播列表
     */
	public function index(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        if (!empty($_GET['username'])){
            $data['b.username|b.ID'] = ['like','%'.$_GET['username'].'%'];
            $this->assign('username',$_GET['username']);
        }
        if (!empty($_GET['live_status'])){
            $data['a.live_status'] = ['eq',$_GET['live_status']];
            $this->assign('live_status',$_GET['live_status']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $data['a.intime'] = ['gt',$start];
            $this->assign('start',$_GET['start']);
        }elseif(empty($_GET['start']) && !empty($_GET['end'])){
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['a.intime'] = ['lt',$end];
            $this->assign('end',$_GET['end']);
        }elseif(!empty($_GET['start']) && !empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['a.intime'] = ['between',[$start,$end]];
            $this->assign('start',$_GET['start']);  $this->assign('end',$_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Live')->alias('a')->join('__USER__ b on a.user_id=b.user_id')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Live')
            ->alias('a')
            ->field('a.*,b.username,b.img,b.sex,b.phone,b.ID')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->limit($p->firstRow.','.$p->listRows)
            ->where($data)
            ->order('a.live_status asc,a.intime desc')->select();
        foreach ($list as $k=>$v){
            $gift_count = M('Give_gift')->where(['live_id'=>$v['live_id']])->sum('jewel');
            $gift_count ? $list[$k]['gift_count'] = $gift_count : $list[$k]['gift_count'] = '0';
        }
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '直播列表' );
	    $this->display();
    }

    /**
     *@强制下线
     */
    public function offline(){
        $id = I('id');
        $live = M('Live')->find($id);
        //强制下线
        import('Vendor.Qiniu.Pili');
        $system = M('System')->where(['id' => 1])->find();
        $ak = $system['ak'];
        $sk = $system['sk'];
        $hubName = $system['hubname'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        //获取stream
        $streamKey = $live['stream_key'];
        $stream = $hub->stream($streamKey);
        $result = $stream->disable();
        $rs = M('Live')->where(['live_id'=>['in',$id]])->save(['live_status'=>2,'end_time'=>time(),'is_offline'=>2]);
        echo $rs ? 1 : 2;
    }


    /******************************************录播列表********************************************************/

    /**
     * @录播列表
     */
    public function recorded(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        if (!empty($_GET['username'])){
            $data['b.username|b.ID'] = ['like','%'.$_GET['username'].'%'];
            $this->assign('username',$_GET['username']);
        }
        if (!empty($_GET['is_tuijian_video'])){
            $data['a.is_tuijian_video'] = $_GET['is_tuijian_video'];
            $this->assign('is_tuijian_video',$_GET['is_tuijian_video']);
        }
        if (!empty($_GET['is_tuijian_info'])){
            $data['a.is_tuijian'] = $_GET['is_tuijian_info'];
            $this->assign('is_tuijian_info',$_GET['is_tuijian_info']);
        }
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Live_store')->alias('a')->join('__USER__ b on a.user_id=b.user_id')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Live_store')
            ->alias('a')
            ->field('a.*,b.username,b.img,b.sex,b.phone,b.ID')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->limit($p->firstRow.','.$p->listRows)
            ->where($data)
            ->order('a.intime desc')->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '录播列表' );
        $this->display();
    }
    public function play(){
        $id = I('id');
        $live_store = M('Live_store')->find($id);
        $this->assign('l',$live_store);
        $this->display();
    }
    /**
     * @彻底删除
     */
    public function del(){
        $id = I('ids');
        $rs = M('Live_store')->where(['live_store_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }
    /**
     * @推荐到视频列表
     */
    public function tuijian_video(){
        $id = I('id');
        $live_store_id = I('live_store_id');
        $rs = M('Live_store')->where(['live_store_id'=>$live_store_id])->save(['is_tuijian_video'=>$id,'uptime'=>time()]);
        echo $rs ? 1 : 2;
    }
    /**
     * @推荐到资讯列表
     */
    public function tuijian_info(){
        $id = I('id');
        $live_store_id = I('live_store_id');
        $rs = M('Live_store')->where(['live_store_id'=>$live_store_id])->save(['is_tuijian'=>$id,'uptime'=>time()]);
        echo $rs ? 1 : 2;
    }

    /******************************************视频列表********************************************************/

     /**
      * @视频列表
      */
     public function video(){
         if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
         unset($_POST['__hash__']);
         $data = [];
         if (!empty($_GET['username'])){
             $data['username|title|lebel'] = ['like','%'.$_GET['username'].'%'];
             $this->assign('username',$_GET['username']);
         }
         if (!empty($_GET['is_tuijian_info'])){
             $data['is_tuijian'] = $_GET['is_tuijian_info'];
             $this->assign('is_tuijian_info',$_GET['is_tuijian_info']);
         }
         if (!empty($_GET['start']) && empty($_GET['end'])){
             $start = strtotime($_GET['start']);
             $data['intime'] = ['gt',$start];
             $this->assign('start',$_GET['start']);
         }elseif(empty($_GET['start']) && !empty($_GET['end'])){
             $end = strtotime($_GET['end'])+(24*60*60-1);
             $data['intime'] = ['lt',$end];
             $this->assign('end',$_GET['end']);
         }elseif(!empty($_GET['start']) && !empty($_GET['end'])){
             $start = strtotime($_GET['start']);
             $end = strtotime($_GET['end'])+(24*60*60-1);
             $data['intime'] = ['between',[$start,$end]];
             $this->assign('start',$_GET['start']);  $this->assign('end',$_GET['end']);
         }
         //每页显示几条
         if (isset($_GET['nums'])){
             $nus  = intval($_GET['nums']);
         }else {
             $nus  = 10;
         }
         $this->assign("nus",$nus);
         $count = M('Video')->where($data)->count();//一共有多少条记录
         $p = getpage($count,$nus);
         $list =  M('Video')
             ->where($data)
             ->order('intime desc')->select();
         $this->assign('list',$list);
         $this->assign("show",$p->show());
         $this->assign ( 'pagetitle', '视频列表' );
         $this->display();
     }

    /**
     * @添加、修改映射
     */
    public function toadd_video(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);

        //省
        $sheng = M('Areas')->where("level=1")->select();
        $this->assign('sheng',$sheng);
        //标签
        $lebel = M('Lebel')->where(['type'=>1])->select();
        $this->assign('lebel',$lebel);
        $id = I('id');
        if ($id){
            $info = M('Video')->find($id);
            $info['intime'] = date('Y-m-d H:i:s',$info['intime']);
            $fid = M('Areas')->where(array('name' => $info['sheng'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['fid'] = $fid;
                $data['level'] = 2;
                $info['shi2'] = M('Areas')->where($data)->select();  //市
            } else {
                $info['shi2'] = null;
            }
            $info['city_id'] = M('Areas')->where(array('name' => $info['shi'], 'level' => 2))->getField('id');
            $this->assign('u',$info);
            $sta = '编辑';
        }else{
            $sta = '添加';
        }
        $this->assign ( 'pagetitle', $sta );
        $this->display();
    }

    /**
     * @修改
     */
    public function doadd_video(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $logo = I('logo');
        $url = I('video');
        $riqi = I('riqi');
        if ($riqi){
            $time = strtotime($riqi);
        }else{
            $time = time();
        }
        $date = date('Y-m-d',$time);
        $data = [
            'play_img'=>$logo,
            'username'=>I('username'),
            'title'=>I('title'),
            'url'=>$url,
            'lebel'=>I('lebels'),
//            'sheng'=>M('Areas')->where(array('id'=>I('sheng')))->getField('name'),
//            'shi'=>M('Areas')->where(array('id'=>I('shi')))->getField('name'),
            'sheng'=>I('sheng'),
            'shi'=>I('shi'),
            'intime'=>$time,
            'date'=>$date,
        ];
        if ($id){
            $video = M('Video')->find($id);
            $data['uptime'] = time();
            if (M('Video')->where(['video_id'=>$id])->save($data)){
                if ($video['url']!=$url){
                    unlink($video['url']);
                }
                $this->success('成功!',U('video'));
            }else{
                $this->error('失败!',U('video'));
            }
        }else{
            M('Video')->add($data) ? $this->success('成功!',U('video')) : $this->error('失败!',U('video'));
        }
    }

    /**
     * @删除
     */
    public function del_video(){
        $id = I('ids');
        $rs = M('Video')->where(['video_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }



    public function play_video(){
        $id = I('id');
        $video = M('Video')->find($id);
        $this->assign('l',$video);
        $this->display();
    }




    /**
     * @推荐到资讯列表
     */
    public function video_tuijian_info(){
        $id = I('id');
        $video_id = I('video_id');
        $rs = M('Video')->where(['video_id'=>$video_id])->save(['is_tuijian'=>$id,'uptime'=>time()]);
        echo $rs ? 1 : 2;
    }
}