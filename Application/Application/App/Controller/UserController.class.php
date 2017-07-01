<?php

namespace App\Controller;

class UserController extends CommonController {
    //个人信息
    public function user_info(){
        $user = checklogin();
        $user['img'] = C('IMG_PREFIX') . $user['img'];
        success($user);
    }
    //修改个人信息
    public function edit_user(){
        $user = checklogin();
        $personalized_signature = I('personalized_signature');
        $img = I('img');$username = I('username');$sex = I('sex');
        if(empty($personalized_signature) || empty($img) || empty($username) || empty($sex)) error('参数错误');
        $data = array(
            'personalized_signature'    =>  $personalized_signature,
            'username'    =>  $username,
            'img'    =>  $img,
            'sex'    =>  $sex,
            'uptime' =>  time(),
        );
        if(M('user')->where(['user_id'=>$user['user_id']])->save($data)){
            success('修改成功');
        }else{
            error('修改失败');
        }

    }

    //我关注的人
    public function user_attention(){
        $user = checklogin();
        $page = I('page');$pagesize = I('pagesize');
        $page = empty($page) ? 0 : $page;
        $pagesize = empty($pagesize) ? 10 : $pagesize;
        $page = $page * $pagesize;
        $data = M('follow')->where(['user_id'=>$user['user_id']])->limit($page, $pagesize)->select();
        if($data){
            foreach ($data as $k=>$v){
                $info = M('user')->field('username, img, personalized_signature, sex')->where(['user_id'=>$v['user_id'], 'is_del'=>1])->find();
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

    //我的粉丝
    public function user_fans(){
        $user = checklogin();
        $page = I('page');$pagesize = I('pagesize');
        $page = empty($page) ? 0 : $page;
        $pagesize = empty($pagesize) ? 10 : $pagesize;
        $page = $page * $pagesize;
        $data = M('follow')->where(['user_id2'=>$user['user_id']])->limit($page, $pagesize)->select();
        if($data){
            foreach ($data as $k=>$v){
                $info = M('user')->field('username, img, personalized_signature, sex')->where(['user_id'=>$v['user_id'], 'is_del'=>1])->find();
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

    //我的提问
    public function user_quiz(){
        $user = checklogin();
        $page = I('page');$pagesize = I('pagesize');
        $page = empty($page) ? 0 : $page;
        $pagesize = empty($pagesize) ? 10 : $pagesize;
        $page = $page * $pagesize;
        $data = M('live_quiz')->where(['user_id'=>$user['user_id']])->limit($page, $pagesize)->select();
        if($data){
            foreach ($data as $k=>&$v){
                $data[$k]['quizUserBean'] = M('user')->field('user_id,username, img, personalized_signature, sex')->where(['user_id'=>$v['user_id2'], 'is_del'=>1])->find();
                $data[$k]['quizReplyBean'] = M('live_quiz_reply')->where(['live_quiz_id'=>$v['live_quiz_id']])->select();
                $data[$k]['quizUserBean']['img'] = C('IMG_PREFIX') . $data[$k]['quizUserBean']['img'];
                foreach ($v['quizReplyBean'] as &$v1){
                    $v1['replyUserBean'] = M('user')->field('user_id,username, img, personalized_signature, sex')->where(['user_id'=>$v1['user_id'], 'is_del'=>1])->find();
                    $v1['replyUserBean']['img'] = C('IMG_PREFIX') . $v1['replyUserBean']['img'];
                }
            }
            success($data);
        }else{
            success([]);
        }
    }

    //问题详情
    public function quiz_particulars(){
        checklogin();
        $live_quiz_id = I('live_quiz_id');
        empty($live_quiz_id) ? error('参数不能为空') : $live_quiz_id = $live_quiz_id;
        $info = M('live_quiz')->find($live_quiz_id);
        if($info){
            $info['liveBean'] = M('live')->where(['live_id'=>$info['live_id']])->find();
            $info['liveBean']['play_img'] = C('IMG_PREFIX') . $info['liveBean']['play_img'];
            $info['userBean'] = M('user')->field('img, user_id, username, personalized_signature')->where(['user_id2'=>$info['user_id'], 'is_del'=>1])->find();
            $info['userBean']['img'] = C('IMG_PREFIX') . $info['userBean']['img'];
            success($info);
        }else{
            success([]);
        }
    }

    //观看记录
    public function user_watch_record(){
        $user = checklogin();
        $page = I('page');$pagesize = I('pagesize');
        $page = empty($page) ? 0 : $page;
        $pagesize = empty($pagesize) ? 10 : $pagesize;
        $page = $page * $pagesize;
        $data = M('live_number')->where(['user_id2'=>$user['user_id']])->limit($page, $pagesize)->select();
        if($data){
            foreach ($data as $k=>$v){
                $username = M('user')->where(['user_id'=>$v['user_id2'], 'is_del'=>1])->getField('username');
                $data[$k]['username'] = $username;
                $info = M('live')->field('title')->where(['live_id'=>$v['live_id']])->find();
                $data[$k]['title'] = $info['title'];
            }
            success($data);
        }else{
            success([]);
        }
    }

    //我的账户
    public function user_account(){
        $user = checklogin();
        success($user['money']);
    }

    //试卷列表（课后演练）
    public function test_list(){
        $user = checklogin();
        $page = I('page');$pagesize = I('pagesize');
        $page = empty($page) ? 0 : $page;
        $pagesize = empty($pagesize) ? 10 : $pagesize;
        $page = $page * $pagesize;
        $data = M('live_test')->limit($page, $pagesize)->select();
        if($data){
            foreach ($data as &$v){
                $v['test_img'] = C('IMG_PREFIX') . $v['test_img'];
                $v['status'] = 0;       //status（答题状态）  0-开始答题, 1-等待评分, 2-已完成;
                $v['pyBean'] = M('live_topic_grade')->where(['user_id'=>$user['user_id'], 'test_id'=>$v['test_id']])->find();
                if($v['pyBean']){
                    if(empty($v['pyBean']['grade'])){
                        $v['status'] = 1;
                    }else{
                        $v['status'] = 2;
                    }
                }
            }
            success($data);
        }else{
            success([]);
        }
    }

    //根据试卷ID查询题目
    public function get_test_topic(){
        checklogin();
        $test_id = I('test_id');
        empty($test_id) ? error('参数不能为空') : $test_id = $test_id;
        $data = M('live_topic')->where(['test_id'=>$test_id])->select();
        if($data){
            success($data);
        }else{
            success([]);
        }
    }

    //答题
    public function add_answer(){
        checklogin();
        $test_id = I('test_id');$json = I('json');
        if(empty($json) || empty($test_id)) error('参数不能为空');
        $arr = json_decode($json, true);
        $count = M('live_topic')->where(['test_id'=>$test_id])->count();
        foreach ($arr as $v){
            $aa[] = M('live_answer')->add($v);
        }
        if(count($aa) != $count){
            error('题目未答完');
        }else{
            success('答题成功');
        }
    }

    //查看评分
    public function select_grade(){
        $user = checklogin();
        $test_id = I('test_id');
        empty($test_id) ? error('参数不能为空') : $test_id = $test_id;
        $info = M('live_topic_grade')->where(['test_id'=>$test_id, 'user_id'=>$user['user_id']])->find();
        if($info){
            success($info);
        }else{
            success([]);
        }
    }

    //查看演练详情
    public function select_manoeuvre(){
        $user = checklogin();
        $test_id = I('test_id');
        empty($test_id) ? error('参数不能为空') : $test_id = $test_id;
        $data = M('live_topic')->where(['test_id'=>$test_id])->select();
        if($data){
            foreach ($data as &$v){
                $info = M('live_answer')->where(['topic_id'=>$v['topic_id'], 'user_id'=>$user['user_id']])->find();
                $v['answerBean'] = $info;
            }
            success($data);
        }else{
            success([]);
        }
    }

    //绑定支付宝
    public function user_alipay(){
        $user = checklogin();
        $relname = I('relname');$phone = I('phone');$alipay = I('alipay');$yzm = I('yzm');
        if(empty($relname) || empty($phone) || empty($alipay) || empty($yzm)) error('参数不能为空');
        if(!preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}|^17[0-9]\d{8}$#', $phone)) error('手机格式不对');
        $info = M('mobile_sms')->where(['mobile'=>$phone])->order('intime desc')->find();
        $time = M('System')->getFieldById(1,'code_volidity');
        if($info){
            if($info['code'] != $yzm){
                error('验证码错误');
            }
            if((time() - $info['intime']) > ($time*60)){
                error('验证码已失效');
            }
            $data = array(
                'relname'   =>  $relname,
                'phone'     =>  $phone,
                'user_id'   =>  $user['user_id'],
                'alipay'    =>  $alipay,
                'intime'    =>  time(),
            );
            if(M('alipay')->add($data)){
                success('绑定成功');
            }else{
                error('绑定失败');
            }
        }else{
            error('手机号码错误！');
        }
    }
}