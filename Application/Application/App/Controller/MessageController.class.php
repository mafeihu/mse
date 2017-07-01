<?php
namespace App\Controller;
use Behavior\CheckLangBehavior;

use Home\Controller\IndexController;
use Psr\Log\Test\DummyTest;

use Org\Util\Date;
use Think\Upload;
use Think\Controller;
class MessageController extends CommonController {
    /**
     * @点击消息
     */
    public function index(){
        $user = checklogin();
        $system = M('Message')->where(['type'=>1,'state'=>1,'user_id2'=>$user['user_id']])->select();
        $system ? $result['system'] = "2" : $result['system'] ="1";
        $comment = M('Message')->where(['type'=>2,'state'=>1,'user_id2'=>$user['user_id']])->select();
        $comment ? $result['comment'] = "2" : $result['comment'] ="1";
        $zan = M('Message')->where(['type'=>3,'state'=>1,'user_id2'=>$user['user_id']])->select();
        $zan ? $result['zan'] = "2" : $result['zan'] ="1";
        success($result);
    }


    /**
     * @消息列表
     * @type  1:系统消息   2:评论消息  3:点赞消息
     */
    public function message_list(){
        $user = checklogin();
        $type = I('type');
        empty($type) ? error('参数错误!') : true;
        switch ($type){
            case 1:
                $list = M('Message')->where(['type'=>$type,'user_id2'=>$user['user_id']])->order('intime desc')->select();
                if ($list){
                    foreach ($list as $k=>$v){
                        if ($v['state']==1){
                            M('Message')->where(['message_id'=>$v['message_id']])->save(['state'=>2,'uptime'=>time()]);
                        }
                        $list[$k]['intime'] = get_times($v['intime']);
                    }
                }else{$list=[];}
                break;
            case 2:
                $list = M('Message')->where(['type'=>$type,'user_id2'=>$user['user_id']])->order('intime desc')->select();
                if ($list){
                    foreach ($list as $k=>$v){
                        if ($v['state']==1){
                            M('Message')->where(['message_id'=>$v['message_id']])->save(['state'=>2,'uptime'=>time()]);
                        }
                        $list[$k]['intime'] = get_times($v['intime']);
                        $list[$k]['img'] = C('IMG_PREFIX').M('User')->where(['user_id'=>$v['user_id']])->getField('img');
                        $username = M('User')->where(['user_id'=>$v['user_id']])->getField('username');
                        $username ? $list[$k]['username'] = $username : $list[$k]['username'] = "";
                        $list[$k]['info_img'] = C('IMG_PREFIX').M('Information')->where(['information_id'=>$v['information_id']])->getField('img');
                    }
                }else{$list=[];}
                break;
            case 3:
                $list = M('Message')->where(['type'=>$type,'user_id2'=>$user['user_id']])->order('intime desc')->select();
                if ($list){
                    foreach ($list as $k=>$v){
                        if ($v['state']==1){
                            M('Message')->where(['message_id'=>$v['message_id']])->save(['state'=>2,'uptime'=>time()]);
                        }
                        $list[$k]['intime'] = get_times($v['intime']);
                        $list[$k]['img'] = C('IMG_PREFIX').M('User')->where(['user_id'=>$v['user_id']])->getField('img');
                        $username = M('User')->where(['user_id'=>$v['user_id']])->getField('username');
                        $username ? $list[$k]['username'] = $username : $list[$k]['username'] = "";
                    }
                }else{$list=[];}
                break;
        }
        $list = str_replace(null, "", $list);
        success($list);
    }




    /**
     * @删除消息
     */
    public function del_message(){
        $user = checklogin();
        $message_id = I('message_id');
        empty($message_id) ? error('参数错误!') : true;
        if (M('Message')->where(['message_id'=>$message_id])->delete()){
            success('成功!');
        }else{
            error('失败!');
        }
    }


    /**
     * @内训课
     */
    public function inne_course(){
        $info = M('System')->field('id,inne_course')->where(['id' => 1])->find();
        $this->assign('inne_course', htmlspecialchars_decode($info['inne_course']));
        $this->display();
    }

    /**
     * @公开课
     */
    public function open_course(){
        $info = M('System')->field('id,open_course')->where(['id' => 1])->find();
        $this->assign('open_course', htmlspecialchars_decode($info['open_course']));
        $this->display();
    }

    /**
     * @内训视频
     */
    public function inne_video(){
        $info = M('System')->field('id,inne_video')->where(['id' => 1])->find();
        $this->assign('inne_video', htmlspecialchars_decode($info['inne_video']));
        $this->display();
    }

    /**
     * @应用技术
     */
    public function applications(){
        $info = M('System')->field('id,applications')->where(['id' => 1])->find();
        $this->assign('applications', htmlspecialchars_decode($info['applications']));
        $this->display();
    }

    /**
     * @学员分享
     */
    public function student_share(){
        $info = M('System')->field('id,student_share')->where(['id' => 1])->find();
        $this->assign('student_share', htmlspecialchars_decode($info['student_share']));
        $this->display();
    }

    /**
     * @内训师列表
     */
    public function teacher_list(){
        $data = M('inner_teacher')->where(['is_del'=>1])->select();
        if($data){
            foreach ($data as &$v){
                $v['inner_img'] = C('IMG_PREFIX') . $v['inner_img'];
                $v['abstract'] = htmlspecialchars_decode($v['abstract']);//转义
            }
            success($data);
        }else{
            success([]);
        }
    }

    /**
     * @内训师信息
     */
    public function teacher_info(){
        $inner_teacher_id = I('inner_teacher_id');
        empty($inner_teacher_id) ? error('参数不能为空') : $inner_teacher_id = $inner_teacher_id;
        $info = M('inner_teacher')->where(['is_del'=>1, 'inner_teacher_id'=>$inner_teacher_id])->find();
        if($info){
            $info['inner_img'] = C('IMG_PREFIX') . $info['inner_img'];
            $info['abstract'] = htmlspecialchars_decode($info['abstract']);//转义
            success($info);
        }else{
            success([]);
        }
    }
}