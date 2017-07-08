<?php

namespace App\Controller;

class AnchorController extends CommonController {
    //主播收益
    public function anchor_earnings(){
        $user = checklogin();
        success($user['get_money']);
    }
    //主播提现
    public function anchor_withdrawal(){
        $user = checklogin();
        $yp = I('yp');
        $money = I("money");
        if(empty($money) || empty($yp) || empty($money)){
            error('参数错误');
        }
        $data = array();
        $data['user_id'] = $user['user_id'];
        $data['yp'] = $yp;
        $data['money'] = $money;
        $data['status'] = 1;
        $data['intime'] = time();
        $data['uptime'] = time();
        //获取主播的银票数量
        $user_info = M('user')->where(['user_id'=>$user['user_id']])->find();
        //判断银票数量是否小于账户余额\
        if(($user_info['get_money']-$yp)<=0){
            error("账户余额不足！！");
        }
        //进行提现操作
        $account_balance = $user_info['get_money']-$yp;
        $userupdate = M('user')->where(['user_id'=>$user['user_id']])->save(['get_money'=>$account_balance]);
        if($userupdate){
            $result = M('withdraw')->add($data);
        }
        if($result){
            success('申请成功');
        }else{
            error('参数错误');
        }
    }
    // 提现记录
    public function get_withdrawal(){
        $user = checklogin();
        $user_id = I('user_id');
        $result = M('withdraw')->where(['user_id'=>$user_id])->order('uptime')->field('yp,money,status,uptime')->select();

        foreach ($result as  $key => $val){
            $result[$key]['uptime'] =  date ('Y-m-d H:i:s',$val['uptime']);
        }
        if($result){
            success($result);
        }else{
            success([]);
        }
    }

    //主播贡献榜
    public function anchor_contribute(){
        $user = checklogin();
        $arr = $this->gxb($user['user_id']);
        if($arr){
            $data = array(
                'total_jewel' => M('give_gift')->where(['user_id2'=>$user['user_id']])->sum('jewel'),
                'data'  =>  $arr,
            );
            success($data);
        }else{
            success([]);
        }
    }
    //根据主播ID查询贡献榜信息
    private function gxb($id){
        $data = M('give_gift')->where(['user_id2'=>$id])->select();
        //找出二维数组中，某个值相等的一维数组，将之放在一块
        $ar=array();
        foreach($data as $v){
            $ar[$v['user_id']][]=$v;
        }
        $price = 0;$id = 0;
        foreach ($ar as $v1){
            foreach ($v1 as $v2){
                $price += $v2['jewel'];
                $id = $v2['user_id'];
            }
            $arr['jewel'] = $price;$arr['user_id'] = $id;$bb[] = $arr;
            $price = 0;$arr = [];
        }
        foreach ($bb as $k=>$value){
            $info = M('user')->field('username, img, personalized_signature, sex')->where(['user_id'=>$value['user_id'], 'is_del'=>1])->find();
            $bb[$k]['username'] = $info['username'];
            $bb[$k]['personalized_signature'] = $info['personalized_signature'];
            $bb[$k]['sex'] = $info['sex'];
            $bb[$k]['img'] = C('IMG_PREFIX') . $info['img'];
        }
        //倒序排序
        $arr = multi_array_sort($bb,'jewel', SORT_DESC);
        return $arr;
    }

    //往期回放 or 主播的直播列表
    public function c(){
        $user = checklogin();
        $count = M('live')->where(['user_id'=>$user['user_id']])->count();
        if($count > 0){
            $data = M('live')->where(['user_id'=>$user['user_id']])->select();
            success(['count'=>$count, 'data'=>$data]);
        }else{
            success([]);
        }
    }

    //主播往期直播信息详情
    public function live_particulars(){
        checklogin();
        $user_id = I('user_id');
        empty($user_id) ? error('参数不能为空') : $user_id = $user_id;
        $info = M('user')->field('username, sex, img, personalized_signature')->where(['user_id'=>$user_id, 'type'=>2, 'is_del'=>1])->find();
        if($info){
            $info['img'] = C('IMG_PREFIX') . $info['img'];
            $info['gxbBean'] = $this->gxb($user_id);
            $data['data'] = $info;
            $data['live_num'] = M('live')->where(['user_id'=>$user_id])->count();
            $data['anchor_fans_num'] = M('follow')->where(['user_id2'=>$user_id])->count();
            $data['anchor_attention_num'] = M('follow')->where(['user_id'=>$user_id])->count();
            success($data);
        }else{
            success([]);
        }
    }

    //主播列表
    public function anchor_list(){
        $data = M('user')->where(['type'=>2, 'is_del'=>1])->select();
        if($data){
            foreach ($data as &$v){
                $v['img'] = C('IMG_PREFIX') . $v['img'];
            }
            success($data);
        }else{
            success([]);
        }
    }
    //演练出题
    public function exercises_problem(){
        $user = checklogin();
        $live_time = I('live_time');
        $title = I('title');
        $test_img = $_FILES['test_img'];
        $topic_name = I('topic_name');
        $data =array();
        if(empty($live_time) || empty($title) || empty($test_img) || empty($user_id) || empty($topic_name)){
            error('参数有误');
        }
        //上传图片
        $config = array(
            'maxSize' => 3145728 * 3,
            'rootPath' => './Public/Uploads/',//保存根路径
            'savePath' =>  'tupian/',
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub' => true,
            'subName' => array('date', 'Ym/d'),
        );
        $Upload = new \Think\Upload($config);// 实例化上传类
        $info = $Upload->upload($test_img);
        if ($info) {
            $img_path = trim($config['rootPath'], '.') . $file['savepath'] . $file['savename'];
        } else {
            exit($Upload->getError());
        }
        $data = array();
        $data['live_time'] = $img_path;
        $data['title'] = $title;
        $data['test_img'] = $test_img;
        $data['user_id'] = $user['user_id'];
        $data['intime'] = time();
        $test_id = M('live_test')->add($data);
        //出题相关信息添加完成
        if($test_id){
            $topic_array = explode(';',$topic_name);
            $topic_data = array();
            $topic_data['test_id'] = $test_id;
            $topic_data['intime'] = time();
           foreach ($topic_array as $val){
               $topic_data['topic_name'] = $val;
               M('live_topic')->add($topic_data);
           }
           success('添加成功');
        }
    }
    //课后演练(主播端)
    public function anchor_test(){
        $user = checklogin();
        $page = I('page');$pagesize = I('pagesize');
        $page = empty($page) ? 0 : $page;
        $pagesize = empty($pagesize) ? 10 : $pagesize;
        $page = $page * $pagesize;
        $data = M('live_test')->where(['user_id'=>$user['user_id']])->limit($page, $pagesize)->select();
        if($data){
            foreach ($data as &$v){
                $v['test_img'] = C('IMG_PREFIX') . $v['test_img'];
                $v['is_grade'] = 0;
                $bb = M('live_topic_grade')->where(['test_id'=>$v['test_id']])->find();
                if($bb){
                    $v['is_grade'] = 1;
                    $v['grade_time'] = $bb['grade_time'];
                }
                $v['dtrName'] = '';
                $v['topicBean'] = M('live_topic')->where(['test_id'=>$v['test_id']])->select();
                if($v['topicBean']){
                    foreach ($v['topicBean'] as &$item) {
                        $item['topicAnswerBean'] = M('live_answer')->where(['topic_id'=>$item['topic_id']])->find();
                        $v['dtrName'] = M('user')->where(['user_id'=>$item['topicAnswerBean']['user_id']])->getField('username');
                    }
                }else{
                    $v['topicBean'] = [];
                }

            }
            success($data);
        }else{
            success([]);
        }
    }
    //评分
    public function comment_grade(){
        $user = checklogin();
        $user_id = I('user_id');$test_id = I('test_id');$grade = I('grade');$comment = I('comment');
        if(empty($user_id) || empty($test_id) || empty($grade) || empty($comment)) error('参数不能为空');
        $data = array(
            'user_id'   =>  $user_id,
            'test_id'   =>  $test_id,
            'grade'     =>  $grade,
            'comment'   =>  $comment,
            'user_id2'  =>  $user['user_id'],
            'grade_time'=>  time(),
            'intime'    =>  time(),
        );
        if(M('live_topic_grade')->add($data)){
            success('评分成功');
        }else{
            error('评分失败');
        }
    }


}