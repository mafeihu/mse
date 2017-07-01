<?php

namespace App\Controller;
use Qiniu\Pili\Mac;
use Think\Controller;
use JPush\Client as JPush;
use Think\Upload;
class IndexController extends CommonController {
    /**
     * @根据环信用户名，返回用户信息
     */
    public function get_user_info(){
        $hx_username = I('hx_username');
        if (empty($hx_username)) {
            error("不存在");
        } else {
            $user = M('User')->field('user_id,username,personalized_signature,img,sex,hx_username,ID')->where(['hx_username' => $hx_username, 'is_del'=>1])->find();
            if($user){
                $user['img'] = C('IMG_PREFIX') . $user['img'];
                success($user);
            }else{
                success([]);
            }
        }
    }

    /*
     * @直播间签到
     */
    public function live_sign(){
        $user = checklogin();
        $live_id = I('live_id');
        empty($live_id) ? error('参数不能为空') : $live_id = $live_id;
        $info = M('user_sign')->where(['user_id'=>$user['user_id'], 'live_id'=>$live_id])->find();
        if($info){
            success('已签到');
        } else {
            if( M('user_sign')->add(['user_id'=>$user['user_id'], 'live_id'=>$live_id, 'create_time'=>time()]) ){
                success('签到成功');
            }else{
                error('签到失败');
            }
        }
    }

    /*
     * @直播间提问
     */
    public function live_quiz(){
        checklogin();
        $live_id = I('live_id');
        $page = I('page');$pageSize = I('pageSize');
        $page = empty($page) ? 0 : $page;
        $pageSize = empty($pageSize) ? 10 : $pageSize;
        $page = $page * $pageSize;
        empty($live_id) ? error('参数不能为空') : $live_id = $live_id;
        $data = M('live_quiz')->where(['live_id'=>$live_id])->limit($page, $pageSize)->select();
        if($data){
            foreach ($data as $k=>$v){
                $info = M('user')->field('username, img, personalized_signature, sex')->where(['user_id'=>$v['user_id2'], 'is_del'=>1])->find();
                $data[$k]['username'] = $info['username'];
                $data[$k]['personalized_signature'] = $info['personalized_signature'];
                $data[$k]['sex'] = $info['sex'];
                $data[$k]['img'] = C('IMG_PREFIX') . $info['img'];
            }
            success($data);
        }else{
            success([]);
        }
    }

    /**
     * @开启直播
     */
    public function start_live(){
        checklogin();
        $user_id = I('uid');
        $teach_id = I('teach_id');$title = I('title'); $log = I('log');  $lag = I('lag');
        if (empty($log) || empty($lag)){
            true;
        }else{
            $gwd = $lag.','.$log;
            $baidu_apikey = M('System')->getFieldById(1,'baidu_apikey');
            $file_contents = file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.$baidu_apikey.'&location='.$gwd.'&output=json');
            $rs = json_decode($file_contents,true);
            $sheng = $rs['result']['addressComponent']['province'];
            $shi = $rs['result']['addressComponent']['city'];
        }
        (empty($title) || empty($teach_id)) ? error('参数错误!') : true;
        $config = [
            'maxSize' => 30 * 3145728,
            'rootPath' => './Public/upload/',
            'savePath' => 'playimg/',
            'saveName' => ['uniqid', ''],
            'exts' => ['png', 'jpg', 'jpeg', 'git', 'gif'],
            'autoSub' => true,
            'subName' => '',
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info) {
            foreach ($info as $file) {
                $img = '/Public/upload/playimg/' . $file["savename"];
            }
        } else {
            error($uploader->getError());
        }
        $user = M('User')->where(['user_id' =>$user_id])->find();
        if($user['type'] != 2){
            error('你不是导师');
        }
        $user['username'] ? $name = $user['username'] : $name = "直播间" . rand(100, 999);
        $options = [
            'name' => $name,
            'description' => $name,
            'maxusers' => 3000,
            'owner' => $user['hx_username']
        ];
        $create = createChatRoom($options);
        $create['error'] ? error('创建聊天室失败!') : true;
        $play_address = push_address(); //七牛
        $time = time();
        $data = [
            'user_id'=>$user_id,
            'play_img'=>$img,
            'title'=>$title,
            'teach_id'=>$teach_id,
            'push_flow_address'=>$play_address['url'],
            'play_address'=>$play_address['url2'],
            'play_address_m3u8'=>$play_address['m3u8'],
            'play_address_flv'=>$play_address['flv'],
            'play_address_rtmp'=>$play_address['rtmp'],
            'start_time'=>$time,
            'stream_key'=>$play_address['streamKey'],
            'live_status'=>1,
            'live_time'=>0,
            'room_id'=>$create['data']['id'],
            'sheng'=>$sheng,
            'shi'=>$shi,
            'intime'=>$time
        ];
        if ($live_id=M('Live')->add($data)) {
            $url = C('IMG_PREFIX')."/App/Index/share_live/live_id/" . base64_encode($live_id);
            $result = [
                'nums' => '0',
                'push_flow_address' => $play_address['url'],
                'play_address'=>$play_address['url2'],
                'room_id' => $create['data']['id'],
                'ID' => $user['id'],
                'money' => $user['get_money'],
                'start_time' =>$time,
                'url' => $url,
                'live_id'=>$live_id
            ];
            success($result);
        } else {
            error('开启失败!');
        }
    }

    /**
     * @获取直播流
     */
    public function get_zhinum(){
        $result = push_address();
        $result = explode('/vxiu1/',$result['url']);
        $zhibo_num = array();
        $zhibo_num['rtmp'] = $result[0]."/vxiu1/";
        $zhibo_num['php_sdk_test'] = $result[1];
        success($zhibo_num);
    }

    /**
     * @获取直播信息
     */
    public function liveInfo(){
        $live_id = I("live_id");
        $re = M('Live')->alias('a')
          ->field("a.live_id,a.title,a.play_img,a.play_address_m3u8,a.play_address_flv,a.play_address_rtmp,a.room_id,a.nums,a.watch_nums,b.zan,a.user_id,b.username,b.img")
          ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
           ->where(["a.live_id"=>$live_id])
          ->order('a.live_id desc')->limit(1)->find();
           $is_follow = M('Follow')->where(['user_id' => $re['user_id'], 'user_id2' => $re['user_id']])->find();
           if($is_follow){
               $is_follow  =1;
           }else{
               $is_follow = 0;
           }
           $re['is_follow'] = $is_follow;
            success($re);
//        foreach ($re as $v){
//            $v['play_img'] = C('IMG_PREFIX') . $v['play_img'];
//            $v['img'] = C('IMG_PREFIX') . $v['img'];
//        }
//        $a = M('yk')->count();
//        if($re['watch_nums'] > C('NUM')){
//            $re['watch_nums'] = C('NUM') + ($a - 8645);
//        }else{
//            $re['watch_nums'] = $re['watch_nums']+C('NUM');
//        }
//        if($re['nums'] > C('NUM')){
//            $re['nums'] = C('NUM') + ($a - 8645);
//        }else{
//            $re['nums'] = $re['nums']+C('NUM');
//        }

    }
    /**
     * @分享直播界面
     */
    public function share_live(){
        $live_id = base64_decode(I('live_id'));
        $live = M('Live')
            ->alias('a')
            ->field('a.live_id,a.play_img,a.title,a.play_address_m3u8,b.img,b.username,b.ID')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.live_id'=>$live_id])
            ->find();
        $this->assign('live',$live);
        $this->display();
    }

    /**
     * @直播列表
     */
    public function live_list(){
        checklogin();
        $user_id = I('uid');
        $page = I('page'); $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        $list = M('Live')
            ->alias('a')
            ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.hx_password,b.province,b.city,b.area,b.zan,b.money,b.get_money,b.url')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.live_status'=>1])
            ->order('a.intime desc')
            ->page($page,$pageSize)
            ->select();
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $list[$k]['url'] = C('IMG_PREFIX')."/App/Index/share_live/live_id/" . base64_encode($v['live_id']);
                $is_follow = M('Follow')->where(['user_id'=>$user_id,'user_id2'=>$v['user_id']])->find();
                $is_follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";
                $list[$k]['start_time'] = date('Y年m月d日 H:i',$v['start_time']);
            }
        }else{$list=[];}
        success($list);
    }

    /**
     * @进入直播间
     */
    public function into_live(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        M('Shield')->where(['user_id'=>M('Live')->getFieldByLive_id($live_id,'user_id'),'user_id2'=>$user_id])->find() ? error('被拉黑,无法进入!') : true;
        //进入直播间,把进入的其他正在直播的记录删除
        $live = M('Live')
            ->alias('a')
            ->field('a.live_id,b.user_id2')
            ->join('right join __LIVE_NUMBER__ b on a.live_id=b.live_id')
            ->where(['a.live_status'=>1])
            ->select();
        if ($live){
            $ids = array_map(function($v){
                return $v['user_id2'];
            },$live);   //返回直播间观看的用户ID，为数组
            if (in_array($user_id,$ids)){
                $live_number = M('Live_number')->where(['live_id'=>$live_id, 'user_id2'=>$user_id])->find();//如果有其他的直播正在观看
                //将其他正在观看的直播记录改变状态
                if($live_number){M('Live_number')->delete($live_number['live_number_id']);}
            }
        }
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        if (M('Live_number')->add(['live_id'=>$live_id,'user_id'=>$user_id2,'user_id2'=>$user_id,'intime'=>time()])){
            M('Live')->comment('观看总人数加1')->where(['live_id'=>$live_id])->setInc('nums');
            M('Live')->comment('观看人数加1')->where(['live_id'=>$live_id])->setInc('watch_nums');
            $is_follow = M('Follow')->where(['user_id' => $user_id, 'user_id2' => $user_id2])->find();
            $is_follow ? $is_follow = "2" : $is_follow = "1";
            $lignt_up = M('Live_light_up')->where(['live_id'=>$live_id,'user_id'=>$user_id,'user_id2'=>$user_id2])->find();
            $lignt_up ? $is_lignt_up = "2" : $is_lignt_up = "1";
            $url = C('IMG_PREFIX')."/App/Index/share_live/live_id/" . base64_encode($live_id);
            $result = ['is_follow'=>$is_follow,'is_lignt_up'=>$is_lignt_up,'url'=>$url];
            success($result);
        }else{
            error('失败!');
        }
    }
    /**
     * @获取主播信息
     */
    public function get_live_info(){
        checklogin();
        $uid = I('uid');
        $user_id = I('user_id');
        empty($user_id) ? error('参数错误!') : true;
        $info = M('User')->where(['user_id'=>$user_id])->find();
        $info['img'] = C('IMG_PREFIX').$info['img'];
        $info['follow_count'] = M('Follow')->comment('关注数')->where(['user_id' =>$user_id])->count();
        $info['fans_count']  = M('Follow')->comment('粉丝数')->where(['user_id2' =>$user_id])->count();
        $is_follow = M('Follow')->where(['user_id'=>$uid,'user_id2'=>$user_id])->find();
        $is_follow ? $info['is_follow'] = "2" : $info['is_follow'] = "1";
        success($info);
    }
    /**
     * @直播间用户列表
     */
    public function show_viewer(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $list = M('Live_number')
            ->alias('a')
            ->field('a.*,b.img,b.username,b.ID,b.hx_username,b.hx_password')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->where(['a.live_id'=>$live_id])
            ->order('a.intime desc')
            ->select();
        if ($list){
            foreach ($list as $k=>$v) {
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $list[$k]['follow_count'] = M('Follow')->comment('关注数')->where(['user_id' =>$v['user_id2']])->count();
                $list[$k]['fans_count']  = M('Follow')->comment('粉丝数')->where(['user_id2' =>$v['user_id2']])->count();
                $is_follow = M('Follow')->where(['user_id'=>$user_id,'user_id2'=>$v['user_id2']])->find();
                $is_follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";
                //查询是否被禁言
                $is_banned = M('Banned')->where(['live_id'=>$live_id,'user_id2'=>$v['user_id2']])->find();
                $is_banned ? $list[$k]['is_banned'] = "2" : $list[$k]['is_banned'] = "1";
                //查询是否是管理
                $is_management = M('Live_management')->where(['user_id'=>$v['user_id'],'user_id'=>$v['user_id2']])->find();
                $is_management ? $list[$k]['is_management'] = "2" : $list[$k]['is_management'] = "1";
            }
        }else{$list=[];}
        success($list);
    }

    /**
     * @禁言(取消禁言)
     */
    public function banned(){
        checklogin();
        $user_id2 = I('user_id'); $type = I('type'); $live_id = I('live_id');
        (empty($user_id2) || empty($type) || empty($live_id)) ? error('参数错误!') : true;
        $uid = M('Live')->getFieldByLive_id($live_id,'user_id');   //主播id
        $ba = M('Banned')->where(['live_id'=>$live_id,'user_id'=>$uid,'user_id2'=>$user_id2])->find();
        switch ($type){
            case 1:
                if ($ba){
                    error('已被禁言!');
                }else{
                    if (M('Banned')->add(['live_id'=>$live_id,'user_id'=>$uid,'user_id2'=>$user_id2,'intime'=>time()])){
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }
                break;
            case 2:
                if ($ba){
                    if (M('Banned')->where(['banned_id'=>$ba['banned_id']])->delete()){
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }else{
                    error('还未被禁言!');
                }
        }
    }

    /**
     * @判断是否被禁言
     */
    public function get_banned(){
        checklogin();
        $user_id = I('user_id');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        if (M('Banned')->where(['user_id'=>$user_id2,'user_id2'=>$user_id])->find()){
            $result = "已禁言";
        }else{
            $result = "未禁言";
        }
        success($result);
    }

    /**
     * @设为管理(取消管理)
     * @type  1:设为管理   2:取消管理
     */
    public function live_manag(){
        checklogin();
        $user_id = I('uid'); $user_id2 = I('user_id'); $type = I('type');
        (empty($user_id2) || empty($type)) ? error('参数错误!') : true;
        ($type==1 || $type==2) ? true : error('传值错误!');
        $man = M('Live_management')->where(['user_id'=>$user_id,'user_id2'=>$user_id2])->find();
        switch ($type){
            case 1:
                if ($man){
                    error('已经设为管理!');
                }else{
                    if (M('Live_management')->add(['user_id'=>$user_id,'user_id2'=>$user_id2,'intime'=>time()])){
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }
                break;
            case 2:
                if (!$man){
                    error('还未设为管理!');
                }else{
                    if (M('Live_management')->where(['live_management_id'=>$man['live_management_id']])->delete()){
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }
                break;
        }
    }

    /**
     * @判断是否是管理
     */
    public function is_mangement(){
        checklogin();
        $user_id = I('user_id');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        if (M('Live_management')->where(['user_id'=>$user_id2,'user_id2'=>$user_id])->find()){
            $result = "已是管理";
        }else{
            $result = "不是管理";
        }
        success($result);
    }


    /**
     * @管理列表
     */
    public function management_list(){
        $user = checklogin();
        $live_id = I('live_id');
        $page = I('page');
        $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        $live = M('Live')->find($live_id);
        empty($live_id) ? error('参数错误!') : true;
        $list = M('Live_management')
            ->alias('a')
            ->field('b.user_id,b.username,b.img,b.ID,b.hx_username')
            ->join('__USER__ b on a.user_id2=b.user_id')
            ->where(['a.user_id'=>$live['user_id']])
            ->page($page,$pageSize)
            ->order('a.intime desc')
            ->select();
        if ($list){
            foreach ($list as $k=>$v) {
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                $follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";
            }
        }else{$list=[];}
        success($list);
    }

    /**
     * @退出直播间
     */
    public function out_live(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        if (M('Live')->where(['live_id' =>$live_id])->setDec('watch_nums')){
            $live_number_id = M('Live_number')->where(['live_id'=>$live_id,'user_id'=>$user_id2,'user_id2'=>$user_id])->getField('live_number_id');
            M('Live_number')->comment('删除记录')->where(['live_number_id'=>$live_number_id])->save(['status'=>1]);
            success('成功!');
        }else{
            error('失败!');
        }
    }

    /**
     * @点亮
     */
    public function lignt_up(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        $lignt_up = M('Live_light_up')->where(['live_id'=>$live_id,'user_id'=>$user_id,'user_id2'=>$user_id2])->find();
        if($lignt_up){
            error('已点亮!');
        }else{
            if (M('Live_light_up')->add(['live_id'=>$live_id,'user_id'=>$user_id,'user_id2'=>$user_id2,'intime'=>time()])){
                M('Live')->comment('点亮数加1')->where(['live_id'=>$live_id])->setInc('light_up_count');
                success('成功!');
            }else{
                error('失败!');
            }
        }
    }

    /**
     * @关注（取消关注）
     * @type  1:关注   2：取消关注
     */
    public function follow(){
        checklogin();
        $user_id = I('uid');
        $user_id2 = I('user_id2');
        $type = I('type');
        (empty($type) || empty($user_id2)) ? error('参数错误!') : true;
        ($type == 1 || $type == 2) ? true : error('传值错误!');
        if ($user_id == $user_id2) error("传值错误");
        $check = M('Follow')->where(['user_id' => $user_id, 'user_id2' => $user_id2])->find();
        if ($type == 1) {
            if ($check) error("已关注");
            if (M('Follow')->add(['user_id' => $user_id, 'user_id2' => $user_id2, 'intime' => time()])) {
                success('成功!');
            } else {
                error('失败!');
            }
        } else {
            if (!$check) error("未关注");
            if (M('Follow')->where(['user_id' => $user_id, 'user_id2' => $user_id2])->delete()) {
                success('成功!');
            } else {
                error('失败!');
            }
        }
    }

    /**
     * 结束直播(主播端)
     */
    public function end_live(){
        checklogin();
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        if (M('Live')->where(['live_id'=>$live_id])->save(['end_time'=>time(),'live_status'=>2])){
            $live = M('Live')->find($live_id);
            if (time()-$live['start_time'] > 120) {
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
                    $url = 'http://oc3pwoyhb.bkt.clouddn.com/' . $fname['fname'];
                    $url ? $url = $url : $url = '';
                    $data = [
                        'live_id' => $live_id,
                        'user_id' => $live['user_id'],
                        'play_img' => $live['play_img'],
                        'title' => $live['title'],
                        'url' => $url,
                        'intime' => time(),
                        'room_id' => $live['room_id'],
                        'date' => date('Y-m-d', time()),
                        'sheng' => $live['sheng'],
                        'shi' => $live['shi'],
                        'live_store_time'=>(time()-$live['start_time'])/60
                    ];
                    $live_store_id = M('Live_store')->add($data);
                }
            }
            $live_store_id ? $live_store_id = $live_store_id : $live_store_id = '';
            $result = [
                'watch_nums'=>$live['watch_nums'],
                'light_up_count'=>$lv=$live['light_up_count'],
                'url'=>$url,
                'live_store_id'=>$live_store_id
            ];
            success($result);
        }else{
            error('失败!');
        }
    }

    /**
     * @结束直播(观看端)
     */
    public function live_end(){
        $user = checklogin();
        $live_id = I('live_id');
        empty($live_id) ? error('参数错误!') : true;
        $live = M('Live')->alias('a')
            ->field('a.live_id,a.user_id,a.play_img,a.title,a.start_time,a.end_time,a.watch_nums,b.img,b.username,b.personalized_signature')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['live_id'=>$live_id])
            ->find();
        $live['play_img'] = C('IMG_PREFIX').$live['play_img'];
        $live['img'] = C('IMG_PREFIX').$live['img'];
        if (empty($live['end_time'])){
            $endtime = time();
        }else{
            $endtime = $live['end_time'];
        }
        $timediff = $endtime-$live['start_time'];
        //计算小时数
        $remain = $timediff%86400;
        $hours = intval($remain/3600);
        //计算分钟数
        $remain = $remain%3600;
        $mins = intval($remain/60);
        //计算秒数
        $secs = $remain%60;
        $live['time'] = $hours.":".$mins.":".$secs;
        $is_follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$live['user_id']])->find();
        $is_follow ? $live['is_follow'] = "2" : $live['is_follow'] = "1";
        success($live);
    }

    /**
     * @礼物列表
     */
    public function gift_list(){
        checklogin();
        $list = M('Gift')->order('intime desc')->select();
        if ($list){
            foreach($list as $k=>$v){
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
            }
        }else{$list=[];}
        success($list);
    }
    /**
     * @送礼
     */
    public function give_gift(){
        checklogin();
        $user_id = I('uid');
        $live_id = I('live_id');
        $gift_id = I('gift_id');
        (empty($live_id) || empty($gift_id)) ? error('参数错误!') : true;
        $gift = M('Gift')->where(['gift_id' => $gift_id])->find();
        //获取送礼人的余额
        $money = M('User')->getFieldByUser_id($user_id, 'money');
        $money - $gift['price'] < 0 ? error('余额不足!') : true;
        //余额减去要送礼的价格
        $user_money = $money-$gift['price'];
        //获取主播ID
        $user_id2 = M('Live')->getFieldByLive_id($live_id,'user_id');
        //获取主播的余额
        $get_money = M('User')->getFieldByUser_id($user_id2, 'get_money') + $gift['price'];
        //将送礼人和主播的余额更新
        $u = M('User')->where(['user_id' =>$user_id])->save(['money' => $user_money, 'uptime' => time()]);
        $u2 = M('User')->where(['user_id' => $user_id2])->save(['get_money' => $get_money, 'uptime' => time()]);
        //添加送礼的记录
        $give_gift =M('Give_gift')->add([
            'user_id' => $user_id,
            'live_id'=>$live_id,
            'user_id2' => $user_id2,
            'gift_id' => $gift_id,
            'intime' => time(),
            'date' => date('Y-m-d', time()),
            'jewel' => $gift['price'],
            'experience' => $gift['experience']
        ]);
        if ($u && $u2 && $give_gift) {
            success('成功!');
        } else {
            error('失败!');
        }
    }

    /**
     * @主播非正常退出,再次进入,判断
     */
    public function check_to_live(){
        $user = checklogin();
        $live = M('Live')->where(['user_id'=>$user['user_id'],'live_status'=>1])->find();
        if ($live){
            $result = ['state'=>1,'nums' => $live['nums'],'user_dis'=>$live['user_dis'],'push_flow_address' => $live['push_flow_address'],'play_address'=>$live['play_address'], 'room_id' => $live['room_id'], 'ID'=>$user['id'], 'money' => $user['money'], 'start_time' =>$live['start_time'], 'url' => $user['url'],'live_id'=>$live['live_id'],'time'=>date('Y.m.d',$live['start_time'])];
        }else{
            $result = ['state'=>2];
        }
        success($result);
    }
    /**
     * @如果不再继续直播,关闭直播,删掉流
     */
    public function shut_down_live(){
        $user = checklogin();
        $live = M('Live')->where(['user_id'=>$user['user_id'],'live_status'=>1])->find();
        if ($live){
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
            M('Live')->where(['live_id'=>$live['live_id']])->save(['end_time'=>time(),'live_status'=>2]);
        }
        success('成功!');
    }


    /**
     * @弹幕扣钱
     */
    public function screen_price(){
        $user = checklogin();
        $live_id = I('live_id');  empty($live_id) ? error('参数错误!') : true;
        $price = M('System')->where(['id'=>1])->getField('screen_price');
        $user['money']-$price<0 ? error('余额不足!') : true;
        $user_money = $user['money']-$price;
        $live_user_id = M('Live')->where(['live_id'=>$live_id])->getField('user_id');
        $u_money = M('User')->where(['user_id'=>$live_user_id])->getField('get_money')+$price;
        if (M('User')->where(['user_id'=>$user['user_id']])->save(['money'=>$user_money,'uptime'=>time()]) && M('User')->where(['user_id'=>$live_user_id])->save(['get_money'=>$u_money,'uptime'=>time()])){
            M('Screen')->add(['live_id'=>$live_id,'user_id'=>$live_user_id,'user_id2'=>$user['user_id'],'intime'=>time()]);
            success('成功!');
        }else{
            error('失败!');
        }
    }

    /**
     * @按home键
     * $type   1:退出   2:进入
     */
    public function home_live(){
        checklogin();
        $user_id = I('uid');
        $type = I('type');
        empty($type) ? error('参数错误!') : true;   ($type==1 || $type==2) ? true : error('传值错误!');
        $live = M('Live')->where(['user_id'=>$user_id,'live_status'=>1])->find();
        switch ($type){
            case 1:
                if ($live){
                    M('Live')->where(['live_id'=>$live['live_id']])->save(['live_time'=>time(),'uptime'=>time()]);
                }
                success('成功!');
                break;
            case 2:
                if ($live){
                    if (!empty($live['live_time'])){
                        if (time() - $live['live_time'] > 3 * 60) {
                            M('Live')->where(['live_id'=>$live['live_id']])->save(['live_status'=>2,'end_time'=>time(),'uptime'=>time()]);
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
                            error("直播结束");
                        }else{
                            M('Live')->where(['live_id'=>$live['live_id']])->save(['live_status'=>1,'live_time'=>0,'uptime'=>time()]);
                            $result = ['nums' => $live['watch_nums'], 'push_flow_address' => $live['push_flow_address'], 'room_id' => $live['room_id'], 'ID' => M('User')->getFieldByUser_id($user_id,'ID'), 'money' => M('User')->getFieldByUser_id($user_id,'get_money'), 'start_time' =>$live['start_time'], 'url' => M('User')->getFieldByUser_id($user_id,'url')];
                        }
                    }else{
                        M('Live')->where(['live_id'=>$live['live_id']])->save(['live_status'=>1,'live_time'=>0,'uptime'=>time()]);
                        $result = ['nums' => $live['watch_nums'], 'push_flow_address' => $live['push_flow_address'], 'room_id' => $live['room_id'], 'ID' => M('User')->getFieldByUser_id($user_id,'ID'), 'money' => M('User')->getFieldByUser_id($user_id,'get_money'), 'start_time' =>$live['start_time'], 'url' => M('User')->getFieldByUser_id($user_id,'url')];
                    }
                    success($result);
                }else{
                    error('没有直播!');
                }
                break;
        }
    }
    /**
     *判断主播是否下线
     */
    public function check_anchor_state(){
            checklogin();
            $anchor_id = I('user_id');
            $live = M('Live')->where(['user_id'=>$anchor_id,'live_status'=>1])->find();
            if(!$live){
                error("主播已下线");
            }else{
                success("ok");
            }
    }

    /**
     * @拉黑
     * @type  1:拉黑   2:取消拉黑
     */
    public function shield(){
        checklogin();
        $user_id = I('uid');
        $user_id2 = I('user_id'); $live_id = I('live_id');  $type = I('type');
        $uid = M('Live')->getFieldByLive_id($live_id,'user_id');
        (empty($user_id2) || empty($live_id) || empty($type)) ? error('参数错误!') : true;
        $shield = M('Shield')->where(['user_id'=>$uid,'user_id2'=>$user_id2])->find();
        switch ($type){
            case 1:
                if ($shield){
                    error('已拉黑!');
                }else{
                    if (M('Shield')->add(['live_id'=>$live_id,'user_id'=>$uid,'user_id2'=>$user_id2,'intime'=>time()])){
                        //环信拉黑
                        $hx_username = M('User')->getFieldByUser_id($user_id,'hx_username');
                        $hx_username2 = M('User')->getFieldByUser_id($user_id2,'hx_username');
                        addUserForBlacklist($hx_username,$hx_username2);

                        //把拉黑的从直播间踢出去
                        M('Live_number')->where(['live_id'=>$live_id,'user_id'=>$uid,'user_id2'=>$user_id2])->delete();

                        success($user_id2);
                    }else{
                        error('失败!');
                    }
                }
                break;
            case 2:
                if ($shield){
                    if (M('Shield')->where(['shield_id'=>$shield['shield_id']])->delete()){
                        $hx_username = M('User')->getFieldByUser_id($user_id,'hx_username');
                        $hx_username2 = M('User')->getFieldByUser_id($user_id2,'hx_username');
                        deleteUserFromBlacklist($hx_username,$hx_username2);  //环信移除黑名单
                        success('成功!');
                    }else{
                        error('失败!');
                    }
                }else{
                    error('未拉黑!');
                }
                break;
        }

    }

    /**
     * @当前登录用户的余额
     */
    public function get_money(){
        checklogin();
        $user_id = I('uid');
        $money = M('User')->getFieldByUser_id($user_id,'money');
        success($money);
    }


    /**
     * @每分钟更新一下,
     */
    public function is_receive(){
        $live_id = I('live_id');
        $user_id = I('user_id');
        (empty($live_id) || empty($user_id)) ? error('参数错误!') : true;
        $re = M('Receive')->where(['live_id'=>$live_id,'user_id'=>$user_id])->find();
        if ($re){
            M('Receive')->where(['receive_id'=>$re['receive_id']])->save(['intime'=>time()]);
        }else{
            M('Receive')->add(['live_id'=>$live_id,'user_id'=>$user_id,'intime'=>time()]);
        }
        success('成功!');
    }

    /**
     * @复制个人简介  (返回个人简介)
     */
    public function get_user_dis(){
        checklogin();
        $user_id = I('uid');
        $dis = M('User')->getFieldByUser_id($user_id,'personalized_signature');
        $dis ? $result = $dis : $result = "";
        success($result);
    }

    /**
     * @充值价格列表
     */
    public function price_list(){
        checklogin();
        $list = M('Price')->select();
        if (!$list){$list=[];}
        success($list);
    }

    /**
     * @判断直播间中用户是否被拉黑、禁言、是否是管理
     */
    public function check_user(){
        $user = checklogin();
        $live_id = I('live_id');   $user_id = I('user_id');
        empty($live_id) ? error('参数错误!') : true;
        $live = M('Live')->find($live_id);
        $shield = M('Shield')->where(['user_id'=>$live['user_id'],'user_id2'=>$user_id])->find();    //是否被拉黑
        $shield ? $is_shield = "2" : $is_shield = "1";
        $banned = M('Banned')->where(['live_id'=>$live_id,'user_id2'=>$user_id])->find();      //是否被禁言
        $banned ? $is_banned = "2" : $is_banned = "1";
        $management = M('Live_management')->where(['user_id'=>$live['user_id'],'user_id2'=>$user_id])->find();  //是否是管理
        $management ? $is_management = "2" : $is_management = "1";
        $result = ['is_shield'=>$is_shield,'is_banned'=>$is_banned,'is_management'=>$is_management];
        success($result);
    }

    /***************************视频***************************/

    /**
     * @预告列表
     */
    public function prevue(){
        $user = checklogin();
        $pre = M('Prevue')
            ->alias('a')
            ->field('a.*,b.img as user_img,b.username,b.personalized_signature')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->order('a.intime desc')
            ->limit(5)
            ->select();
        if ($pre){
            foreach ($pre as $k=>$v){
                $pre[$k]['img'] = C('IMG_PREFIX').$v['img'];
                $pre[$k]['user_img'] = C('IMG_PREFIX').$v['user_img'];
                $is_sign_up = M('Prevue_sign_up')->where(['user_id'=>$user['user_id'],'prevue_id'=>$v['prevue_id']])->find();
                $is_sign_up ? $pre[$k]['is_sign_up'] = "2" : $pre[$k]['is_sign_up'] = "1";
            }
        }else{$pre=[];}
        success($pre);
    }

    /**
     * @回放视频分享地址
     */
    public function video_live_share(){
        $live_store_id = base64_decode(I('live_store_id'));
        $live = M('Live_store')
            ->alias('a')
            ->field('a.live_store_id,a.user_id,a.play_img,a.title,a.url,b.img,b.username,b.personalized_signature,b.ID')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['a.live_store_id'=>$live_store_id])
            ->find();
        $this->assign('live',$live);
        $this->display();
    }

    /**
     * @平台视频分享地址
     */
    public function video_share(){
        $video_id = base64_decode(I('video_id'));
        $live = M('Video')->find($video_id);
        $this->assign('live',$live);
        $this->display();
    }

    /**
     * @视频列表()
     * @视频列表和录播推荐
     */
    public function store_list(){
        $user = checklogin();
        $page = I('page');
        $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        $video = M('Video')->select();
        if ($video){
            foreach ($video as $k=>$v){
                $video[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                $video[$k]['url'] = C('IMG_PREFIX').$v['url'];
                $video[$k]['is_type'] = "1";
                $video[$k]['share_url'] = C('IMG_PREFIX')."/App/Index/video_share/video_id/" . base64_encode($v['video_id']);
            }
        }else{$video=[];}
        $live_store = M('Live_store')
            ->alias('a')
            ->field('a.*,b.nums,b.watch_nums,b.light_up_count,c.username')
            ->join('__LIVE__ b on a.live_id=b.live_id')
            ->join('__USER__ c on a.user_id=c.user_id')
            ->where(['a.is_tuijian_video'=>2])
            ->order('a.intime desc')
            ->select();
        if ($live_store){
            foreach ($live_store as $k=>$v){
                $live_store[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                $live_store[$k]['is_type'] = "2";
                $live_store[$k]['share_url'] = C('IMG_PREFIX')."/App/Index/video_live_share/live_store_id/" . base64_encode($v['live_store_id']);
            }
        }else{$live_store=[];}
        $list = array_merge($video,$live_store);
        foreach ($list as $key => $row ){
            $num1[$key] = $row ['intime'];
        }
        array_multisort($num1, SORT_DESC, $list);
        $list = array_slice($list,($page-1)*$pageSize,$pageSize);
        if($page==1){
            //推荐列表(插入10个)
            $foll = M('Follow')->where(['user_id'=>$user['user_id']])->select();
            if ($foll){
                $ids = array_map(function($v){ return $v['user_id2'];},$foll);
                array_push($ids, $user['user_id']);
                $dat['user_id'] = ['not in',$ids];
                $dat['is_fans'] = 1;
                $dat['is_del'] = 1;
                $tuijian = M('User')
                    ->field('user_id,phone,personalized_signature,img,sex,username,ID,hx_username,province,city,zan,money,url')
                    ->where($dat)
                    ->order('get_money desc')
                    ->limit(10)
                    ->select();
                if ($tuijian){
                    foreach ($tuijian as $k=>$v){
                        $tuijian[$k]['img'] = C('IMG_PREFIX').$v['img'];
                    }
                    $tj = ['tuijian'=>['is_type'=>"4",'tuijian'=>$tuijian]];
                    array_splice($list,4,0,$tj);    //把推荐列表插入到数组第四个位置,如果前面没有,自动推前
                }
                //else{$tuijian=[];}
            }else{
                $dat['is_fans'] = 1;
                $dat['is_del'] = 1;
                $tuijian = M('User')
                    ->field('user_id,phone,personalized_signature,img,sex,username,ID,hx_username,province,city,zan,money,url')
                    ->where($dat)
                    ->order('get_money desc')
                    ->limit(10)
                    ->select();
                if ($tuijian){
                    foreach ($tuijian as $k=>$v){
                        $tuijian[$k]['img'] = C('IMG_PREFIX').$v['img'];
                    }
                    $tj = ['tuijian'=>['is_type'=>"4",'tuijian'=>$tuijian]];
                    array_splice($list,4,0,$tj);    //把推荐列表插入到数组第四个位置,如果前面没有,自动推前
                }
            }
        }
        success($list);
    }

    /**
     * @点击视频播放+1
     * @state   1:平台视频   2:录播视频
     */
    public function play_store(){
        $user = checklogin();
        $live_store_id = I('live_store_id');  $state = I('state');
        (empty($live_store_id) || empty($state)) ? error('参数错误!') : true;
        switch ($state){
            case 1:
                if (M('Video')->where(['video_id'=>$live_store_id])->setInc('play_number')){
                    success('成功!');
                }else{
                    error('失败!');
                }
                break;
            case 2:
                if (M('Live_store')->where(['live_store_id'=>$live_store_id])->setInc('play_number')){
                    success('成功!');
                }else{
                    error('失败!');
                }
                break;
        }
    }

    /**
     * @判断视频是否被收藏
     * @type  1:平台视频  2:录制视频
     */
    public function video_is_collection(){
        $user = checklogin();
        $video_id = I('vi_id');   $type = I('type');
        (empty($video_id) || empty($type)) ? error('参数错误!') : true;
        $collection = M('Collection')->where(['user_id'=>$user['user_id'],'type'=>2,'about_id'=>$video_id,'is_type'=>$type])->find();
        $collection ? $is_collection = "2" : $is_collection = "1";
        success($is_collection);
    }

    /******************************关注列表*******************************************/
    /**
     * @判断是否有关注
     * @如果有关注的,判断是否有正在直播的或者有发布的视频
     */
    public function is_follow(){
        checklogin();
        $user_id = I('uid');
        $follow = M('Follow')->where(['user_id'=>$user_id])->select();

        $list1 = M('Follow')
            ->alias('a')
            ->field('a.follow_id,b.live_id,b.user_id,b.play_img,b.title,b.user_dis,b.lebel,b.content,b.push_flow_address,b.play_address,b.start_time,b.stream_key,b.live_status,b.room_id,b.nums,b.watch_nums,b.light_up_count,b.sheng,b.shi,b.intime,c.username,c.personalized_signature,c.img,c.hx_username,c.ID')
            ->join('__LIVE__ b on a.user_id2=b.user_id')
            ->join('__USER__ c on b.user_id=c.user_id')
            ->where(['a.user_id'=>$user_id,'b.live_status'=>1])
            ->order('b.intime desc')
            ->select();
        $list2 = M('Follow')
            ->alias('a')
            ->field('a.follow_id,b.live_store_id,b.user_id,b.play_img,b.title,b.url,b.play_number,b.room_id,b.lebel,b.intime,b.sheng,b.shi,c.username,c.personalized_signature,c.img,c.hx_username,c.ID')
            ->join('__LIVE_STORE__ b on a.user_id2=b.user_id')
            ->join('__USER__ c on b.user_id=c.user_id')
            ->where(['a.user_id'=>$user_id])
            ->select();

        //$store = M('Live_store')->where(['user_id'=>$user_id])->select();
        if ($follow || $list1 || $list2){
            $result = "2";
        }else{
            $result = "1";
        }
        success($result);
    }
    /**
     * @关注列表
     * @1:没有关注的   2:有关注并且关注的人有发布的视频
     */
    public function follow_list(){
        checklogin();
        $user_id = I('uid');
        $type = I('type');
        $page = I('page');
        $pageSize = I('pagesize');
        $page ? $page : $page = 1;  $pageSize ? $pageSize : $pageSize = 10;
        empty($type) ? error('参数错误!') : true;   ($type==1 || $type==2) ? true : error('传值错误!');
        switch ($type){
            case 1:
                $foll = M('Follow')->where(['user_id'=>$user_id])->select();
                if ($foll){
                    $ids = array_map(function($v){ return $v['user_id2'];},$foll);
                    array_push($ids, $user_id);
                    $date = [
                        'is_fans'=>1,
                        'is_del'=>1,
                        'user_id'=>['not in',$ids]
                    ];
                    $list = M('User')
                        ->field('user_id,phone,img,sex,username,personalized_signature,ID,hx_username,province,city,zan,money,url')
                        ->where($date)
                        ->order('get_money desc')
                        ->page($page,$pageSize)
                        ->select();
                    if ($list){
                        foreach ($list as $k=>$v){
                            $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        }
                    }else{$list=[];}
                }else{
                    $date = [
                        'is_fans'=>1,
                        'is_del'=>1,
                        'user_id'=>['neq',$user_id]
                    ];
                    $list = M('User')
                        ->field('user_id,phone,img,sex,username,personalized_signature,ID,hx_username,province,city,zan,money,url')
                        ->where($date)
                        ->order('get_money desc')
                        ->page($page,$pageSize)
                        ->select();
                    if ($list){
                        foreach ($list as $k=>$v){
                            $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        }
                    }else{$list=[];}
                }
                break;
            case 2:
                if ($page==1){
                    $foll = M('Follow')->where(['user_id'=>$user_id])->select();
                    if ($foll){
                        $ids = array_map(function($v){ return $v['user_id2'];},$foll);
                        array_push($ids, $user_id);
                        $date = [
                            'is_fans'=>1,
                            'is_del'=>1,
                            'user_id'=>['not in',$ids]
                        ];
                        $tuijian = M('User')
                            ->field('user_id,phone,img,sex,username,personalized_signature,ID,hx_username,province,city,zan,money,url')
                            ->where($date)
                            ->order('get_money desc')
                            ->limit(10)
                            ->select();
                        if ($tuijian){
                            foreach ($tuijian as $k=>$v){
                                $tuijian[$k]['img'] = C('IMG_PREFIX').$v['img'];
                            }
                        }else{$tuijian=[];}
                    }else{$tuijian=[];}
                }else{$tuijian=[];}
                $list['tuijian'] = $tuijian;
                $list1 = M('Follow')
                    ->alias('a')
                    ->field('a.follow_id,b.live_id,b.user_id,b.play_img,b.title,b.user_dis,b.lebel,b.content,b.push_flow_address,b.play_address,b.start_time,b.stream_key,b.live_status,b.room_id,b.nums,b.watch_nums,b.light_up_count,b.sheng,b.shi,b.intime,c.username,c.personalized_signature,c.img,c.hx_username,c.ID')
                    ->join('__LIVE__ b on a.user_id2=b.user_id')
                    ->join('__USER__ c on b.user_id=c.user_id')
                    ->where(['a.user_id'=>$user_id,'b.live_status'=>1])
                    ->page($page,$pageSize)
                    ->order('b.intime desc')
                    ->select();
                if ($list1){
                    foreach ($list1 as $k=>$v){
                        $list1[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                        $list1[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        $list1[$k]['intime'] = date('Y-m-d H:i',$v['intime']);
                        $list1[$k]['is_state'] = "1";
                    }
                }else{$list1=[];}
                $list2 = M('Follow')
                    ->alias('a')
                    ->field('a.follow_id,b.live_store_id,b.user_id,b.play_img,b.title,b.url,b.play_number,b.room_id,b.lebel,b.intime,b.sheng,b.shi,c.username,c.personalized_signature,c.img,c.hx_username,c.ID')
                    ->join('__LIVE_STORE__ b on a.user_id2=b.user_id')
                    ->join('__USER__ c on b.user_id=c.user_id')
                    ->where(['a.user_id'=>$user_id])
                    ->page($page,$pageSize)
                    ->select();
                if ($list2){
                    foreach ($list2 as $k=>$v){
                        $list2[$k]['play_img'] = C('IMG_PREFIX').$v['play_img'];
                        $list2[$k]['img'] = C('IMG_PREFIX').$v['img'];
                        $list2[$k]['intime'] = date('Y-m-d H:i',$v['intime']);
                        $list2[$k]['is_state'] = "2";
                    }
                }else{$list2=[];}
                $list_array = array_merge($list1,$list2);
                //$list['list'] = array_slice($list_array,($page-1)*$pageSize,$pageSize);
                $list['list'] = $list_array;
                break;
        }
        success($list);
    }

    /**
     * @标签列表
     */
    public function lebel_list(){
        $type = I('type');
        if (empty($type)) $type=1;
        $list = M('Lebel')->where(['type'=>$type])->select();
        if ($list){
            foreach ($list as $k=>$v){
                $v['img'] ? $list[$k]['img'] = C('IMG_PREFIX') . $v['img'] : $list[$k]['img'] = "/admin/images/nopic.gif" ;
            }
        }else{$list=[];}
        success($list);
    }
}
