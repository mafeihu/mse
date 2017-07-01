<?php
namespace Admin\Controller;
use Org\Util\Date;

use Psr\Log\Test\DummyTest;

use Think\Controller;
use Think\Auth;
class CliController extends Controller{
	/**
	 * @没三分钟,判断按home键退出的直播,更改为下线状态
	 */
	public function check_live(){
        for ($i=1;$i<50;$i++){
            set_time_limit(0);
            $live = M('Live')->where(['live_status' => '1','live_time' =>['neq','']])->page($i)->limit(50)->select();
            if (empty($live)) break;
            foreach ($live as $k=>$v){
                if (time() - $v['live_time'] > 3 * 60) {
                    M('Live')->where(['live_id'=>$v['live_id']])->save(['live_status'=>2,'end_time'=>time(),'uptime'=>time()]);
                    //保存视频
                    import('Vendor.Qiniu.Pili');
                    $system = M('System')->where(['id' => 1])->find();
                    $ak = $system['ak'];
                    $sk = $system['sk'];
                    $hubName = "vxiu1";
                    $mac = new \Qiniu\Pili\Mac($ak, $sk);
                    $client = new \Qiniu\Pili\Client($mac);
                    $hub = $client->hub($hubName);
                    //获取stream
                    $streamKey = $live['stream_key'];
                    $stream = $hub->stream($streamKey);
                    //保存直播数据
                    $fname = $stream->save(0, 0);
                    if ($fname['fname']) {
                        $data = [
                            'live_id' => $live['live_id'],
                            'user_id' => $live['user_id'],
                            'play_img' => $live['play_img'],
                            'title' => $live['title'],
                            'url' => 'http://oc3pwoyhb.bkt.clouddn.com/' . $fname['fname'],
                            'intime' => time(),
                            'room_id' => $live['room_id'],
                            'lebel' => $live['lebel'],
                            'date'=>date('Y-m-d',time()),
                            'sheng'=>$live['sheng'],
                            'shi'=>$live['shi']
                        ];
                        M('Live_store')->add($data);
                    }
                }
            }
        }
	}

    /**
     *@列出七牛正在直播的流，不在里面则改变直播状态。
     */
    public function check_online(){
        import('Vendor.Qiniu.Pili');
        $system = M('System')->where(['id' => 1])->find();
        $ak = $system['ak'];
        $sk = $system['sk'];
        $hubName = "vxiu1";
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        $resp = $hub->listLiveStreams("php-sdk-test", 100000, "");
        $resp = $resp[keys];
        for ($i = 0; $i < 50; $i++) {
            $live = M('Live')->where(['live_status' => '1'])->page($i)->limit(50)->select();
            if (empty($live)) break;
            foreach ($live as $k => $v) {
                if(!in_array($v['stream_key'],$resp)){
                    M('Live')->where(['live_id'=>$v['live_id']])->save(['live_status'=>2,'end_time'=>time(),'uptime'=>time()]);
                    //保存视频
                    import('Vendor.Qiniu.Pili');
                    $system = M('System')->where(['id' => 1])->find();
                    $ak = $system['ak'];
                    $sk = $system['sk'];
                    $hubName = "vxiu1";
                    $mac = new \Qiniu\Pili\Mac($ak, $sk);
                    $client = new \Qiniu\Pili\Client($mac);
                    $hub = $client->hub($hubName);
                    //获取stream
                    $streamKey = $live['stream_key'];
                    $stream = $hub->stream($streamKey);
                    //保存直播数据
                    $fname = $stream->save(0, 0);
                    if ($fname['fname']) {
                        $data = [
                            'live_id' => $live['live_id'],
                            'user_id' => $live['user_id'],
                            'play_img' => $live['play_img'],
                            'title' => $live['title'],
                            'url' => 'http://oc3pwoyhb.bkt.clouddn.com/' . $fname['fname'],
                            'intime' => time(),
                            'room_id' => $live['room_id'],
                            'lebel' => $live['lebel'],
                            'date'=>date('Y-m-d',time()),
                            'sheng'=>$live['sheng'],
                            'shi'=>$live['shi']
                        ];
                        M('Live_store')->add($data);
                    }
                }
            }
            set_time_limit(0);
        }
    }
    /**
     *@每过一分钟一个僵尸粉,如果直播间人数超过10人,则不加僵尸粉
     */
    public function set_fans(){
        for ($i = 0; $i < 50; $i++) {
            $live = M('Live')->where(['live_status' =>'1'])->page($i)->limit(50)->select();
            if (empty($live)) break;
            foreach($live as $k=>$v){
                $count = M('Live_number')->where(['live_id'=>$v['live_id']])->count();
                if($count<11){
                    $live_number = M('Live_number')->where(['live_id'=>$v['live_id']])->select();
                    if ($live_number){
                        $user_ids = array_map(function($v){ return $v['user_id2'];},$live_number);  //观众id集合
                        $fans = M('User')->field('user_id')->where(['is_fans'=>2,'user_id'=>['not in',$user_ids]])->select();
                        if ($fans){
                            $fans_ids = array_map(function($v){ return $v['user_id'];},$fans);  //僵尸粉id集合
                            $rand = array_rand($fans_ids,1);
                            M('Live_number')->add(['live_id'=>$v['live_id'],'user_id'=>$v['user_id'],'user_id2'=>$fans_ids[$rand],'intime'=>time()]);
                            M('Live')->where(['live_id'=>$v['live_id']])->setInc('nums');
                            M('Live')->where(['live_id'=>$v['live_id']])->setInc('watch_nums');
                        }
                    }
                }
            }

        }
    }


    /**
     * @每天半夜11:30备份数据库
     */
    public function backup_database(){
        $database=C('DB_NAME');//数据库名
        $name = "back_".date('Y-m-d',time());
        $options=array(
            'hostname' => C('DB_HOST'),//ip地址
            'charset'  => C('DB_CHARSET'),//编码
            'filename' => $name.'.sql',//文件名
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















	}
