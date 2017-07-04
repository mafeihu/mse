<?php
namespace Admin\Controller;

/**
 * 用户
 * @author
 *
 */
use Think\Db;

class UserController extends CommonController
{
    function _initialize()
    {
        $nums = ['5', '10', '15', '20', '25', '30', '50', '100'];
        $this->assign('nums', $nums);
    }

    /**
     * @获取市,区
     */
    public function get_area()
    {
        $value = I('value');
        $type = I('type');
        if (isset($value)) {
            if ($type == 1) {
                $data['level'] = 2;
                $data['pid'] = array('eq', $value);
                $type_list = "<option value=''>请选择（市）</option>";
                $shi = M('Areas')->where($data)->select();
            } else {
                $data['level'] = 3;
                $data['pid'] = array('eq', $value);
                $type_list = "<option value=''>请选择（区/县）</option>";
                $shi = M('Areas')->where($data)->select();
            }
            foreach ($shi as $k => $v) {
                $type_list .= "<option value=" . $shi[$k]['id'] . ">" . $shi[$k]['name'] . "</option>";
            }
            echo $type_list;
        }
    }

    /**
     * @添加（修改）验证手机号
     */
    public function yzmobile()
    {
        $id = I('id');
        $mobile = I('mobile');
        if ($id == '') {
            $me = M('User')->where(array('phone' => $mobile))->find();
            echo $me ? 1 : 2;
        } else {
            $mobile_ok = M('User')->where(array('user_id' => $id))->getField('phone');
            if ($mobile != $mobile_ok) {
                $me = M('User')->where(array('phone' => $mobile))->find();
                echo $me ? 1 : 2;
            }
        }
    }

    // 主页面
    public function index()
    {
        if (!M()->autoCheckToken($_POST)) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        $data = [
            'is_del' => 1,
            'is_fans' => 1  //是否僵尸粉
        ];
        if (!empty($_GET['username'])) {
            $data['phone|username|employee_id'] = ['like', '%' . $_GET['username'] . '%'];
            $this->assign('username', $_GET['username']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])) {
            $start = strtotime($_GET['start']);
            $data['intime'] = ['gt', $start];
            $this->assign('start', $_GET['start']);
        } elseif (empty($_GET['start']) && !empty($_GET['end'])) {
            $end = strtotime($_GET['end']) + (24 * 60 * 60 - 1);
            $data['intime'] = ['lt', $end];
            $this->assign('end', $_GET['end']);
        } elseif (!empty($_GET['start']) && !empty($_GET['end'])) {
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end']) + (24 * 60 * 60 - 1);
            $data['intime'] = ['between', [$start, $end]];
            $this->assign('start', $_GET['start']);
            $this->assign('end', $_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])) {
            $nus = intval($_GET['nums']);
        } else {
            $nus = 10;
        }
        $data['type'] = "2";
        //判断普通会员还是直播会员
        if($_GET['ids'] == "459"){
            $data['type'] = "2";
        }else{
            $data['type'] = "1";
        }
        $this->assign("nus", $nus);
        $count = M('User')->where($data)->count();//一共有多少条记录
        $p = getpage($count, $nus);
        $list = M('User')->limit($p->firstRow . ',' . $p->listRows)->where($data)->order('intime desc')->select();
        $this->assign('list', $list);
        $this->assign("show", $p->show());
        if($data['type'] == "2"){
          $this->assign('pagetitle', '直播会员列表');
        }else{
          $this->assign('pagetitle', '普通会员列表');
        }
        $this->display();
    }
    /**
    *@获取直播流
    **/
    public function zhibo(){
      $user_id = I('id');
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
        $result = explode('/vxiu1/',$play_address['url']);
    $data = [
        'play_rtmp' =>  $result[0]."/vxiu1/",
        'php_sdk'=> $result[1],
        'user_id'=>$user_id,
        'play_img'=>empty($img) ? '/Public/upload/playimg/593e0637665f4.jpeg' : $img,
        'title'=>empty($title) ? '哈哈':$title,
        'teach_id'=>empty($teach_id) ? 1 : $teach_id,
        'push_flow_address'=>$play_address['url'],
        'play_address'=>$play_address['url2'],
        'play_address_m3u8'=>$play_address['m3u8'],
        'play_address_flv'=>$play_address['flv'],
        'play_address_rtmp'=>$play_address['rtmp'],
        'start_time'=>time(),
        'stream_key'=>$play_address['streamKey'],
        'live_status'=>1,
        'live_time'=>0,
        'room_id'=>$create['data']['id'],
        'sheng'=>'',
        'shi'=>'',
        'intime'=>time()
    ];
       $live_id = M('live')->add($data);
//        if($live_id){
//           $data =  M('Live')->where(['live_id'=>$live_id])->find();
//            $url = C('IMG_PREFIX')."/App/Index/share_live/live_id/" . base64_encode($live_id);
//            M('live')->where(['live_id'=>$live_id])->save(['url'=>$url]);
//            $this->redirect("User/details",array('id' => $user_id,));
//        }
        if($live_id){
            $user_live = M('Live')->where(['user_id'=>$user_id])->order('live_id desc')->limit(1)->find();
            $this->ajaxreturn(array("php_sdk"=>$user_live['php_sdk'],"play_rtmp"=>$user_live['play_rtmp']));
        }else{
            $this->ajaxreturn('获取失败');
        }

    }

    /**
     * @添加、修改映射
     */
    public function toadd()
    {
        //省
        $sheng = M('Areas')->where("level=1")->select();
        $this->assign('sheng', $sheng);

        $id = I('id');
        if ($id) {
            $user = M('User')->find($id);
            $fid = M('Areas')->where(array('name' => $user['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['fid'] = $fid;
                $data['level'] = 2;
                $user['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $user['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['fid'] = $fid2;
                $date['level'] = 3;
                $user['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $user['qu'] = null;
            }
            $user['city_id'] = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            $user['area_id'] = M('Areas')->where(array('name' => $user['area'], 'level' => 3))->getField('id');
            $this->assign('u', $user);
            $sta = '编辑';
        } else {
            $sta = '添加';
        }
        $this->assign('pagetitle', $sta);
        $this->display();
    }

    /**
     * @修改
     */
    public function doadd()
    {
        if (!M()->autoCheckToken($_POST)) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $logo = I('logo');
        empty($logo) ? $img = "/mse/Public/admin/touxiang.png" : $img = $logo;
        $data = [
            'token' => uniqid(),
            'phone' => I('phone'),
            'employee_id' => I('employee_id'),
            'img' => $img,
            'sex' => I('sex'),
            'username' => I('username'),
            'personalized_signature' => I('personalized_signature'),
            'province' => M('Areas')->where(array('id' => I('sheng')))->getField('name'),
            'city' => M('Areas')->where(array('id' => I('shi')))->getField('name'),
            'area' => M('Areas')->where(array('id' => I('qu')))->getField('name'),
            'address' => I('address'),
        ];
        if ($id) {
            $data['uptime'] = time();
            M('User')->where(['user_id' => $id])->save($data) ? $this->success('成功!', U('index',array('ids'=>459))) : $this->error('失败!', U('index'));
        } else {
            $chars = "abcdefghijklmnopqrstuvwxyz123456789";
            mt_srand(10000000 * (double)microtime());
            for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < 12; $i++) {
                $str .= $chars[mt_rand(0, $lc)];
            }
            $hx_password = "123456";
            $date = [
                'ID' => get_number(),
                'employee_id' => get_number(),
                'alias' => $str,
                'hx_username' => $str,
                'hx_password' => $hx_password,
                'intime' => time(),
                'type' => 2
            ];
            $array = array_merge($data, $date);
            if ($ids = M('User')->add($array)) {
                huanxin_zhuce($str, $hx_password); //环信注册
                $us = M('User')->where(['user_id' => $ids])->find();
                $us['img'] = C('IMG_PREFIX') . $us['img'];
                $url = C('IMG_PREFIX') . "/index.php?m=Home&c=Public&a=index" . "&uid=" . base64_encode($id);
                M('User')->where(['user_id' => $ids])->save(['url' => $url, 'uptime' => time()]);
                $this->success('成功!', U('index',array('ids'=>459)));
            } else {
                $this->error('失败!', U('index'));
            }
        }
    }

    /**
     * @详情
     */
    public function details()
    {
        if (!M()->autoCheckToken($_POST)) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $user = M('User')->where(['user_id' => $id])->find();
        $user['xiaofei'] = M('Give_gift')->where(['user_id' => $id])->sum('jewel');
        $user['withdraw_count'] = M('Withdraw')->where(['user_id' => $id])->sum('money');
        $this->assign('view', $user);
        $state = I('state');
        if (!$state) {
            $state = '1';
        }
        switch ($state) {
            case 1:
                //充值记录
                $count = M('Recharge_record')->where(['user_id' => $id])->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Recharge_record')->where(['user_id' => $id])->limit($p->firstRow . ',' . $p->listRows)->order('intime desc')->select();
                $this->assign("show", $p->show());
                $this->assign('re', $list);
                break;
            case 2:
                //兑换记录
                $count = M('Convert')->where(['user_id' => $id])->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Convert')->where(['user_id' => $id])->limit($p->firstRow . ',' . $p->listRows)->order('intime desc')->select();
                $this->assign("show", $p->show());
                $this->assign('con', $list);
                break;
            case 3:
                //提现记录
                $count = M('Withdraw')->where(['user_id' => $id])->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Withdraw')->where(['user_id' => $id])->limit($p->firstRow . ',' . $p->listRows)->order('intime desc')->select();
                $this->assign("show", $p->show());
                $this->assign('w', $list);
                break;
            case 4:
                //收益记录
                $count = M('Give_gift')
                    ->alias('a')
                    ->join('__LIVE__ b on a.live_id=b.live_id')
                    ->join('__USER__ c on a.user_id=c.user_id')
                    ->join('__GIFT__ d on a.gift_id=d.gift_id')
                    ->where(['a.user_id2' => $id])
                    ->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Give_gift')
                    ->alias('a')
                    ->field('a.*,b.title,c.username,d.name')
                    ->join('__LIVE__ b on a.live_id=b.live_id')
                    ->join('__USER__ c on a.user_id=c.user_id')
                    ->join('__GIFT__ d on a.gift_id=d.gift_id')
                    ->where(['a.user_id2' => $id])
                    ->limit($p->firstRow . ',' . $p->listRows)
                    ->order('a.intime desc')
                    ->select();
                $this->assign("show", $p->show());
                $this->assign('g', $list);
                break;
            case 5:
                //消费记录
                $count = M('Give_gift')
                    ->alias('a')
                    ->join('__LIVE__ b on a.live_id=b.live_id')
                    ->join('__USER__ c on a.user_id=c.user_id')
                    ->join('__GIFT__ d on a.gift_id=d.gift_id')
                    ->where(['a.user_id' => $id])
                    ->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Give_gift')
                    ->alias('a')
                    ->field('a.*,b.title,c.username,d.name')
                    ->join('__LIVE__ b on a.live_id=b.live_id')
                    ->join('__USER__ c on a.user_id2=c.user_id')
                    ->join('__GIFT__ d on a.gift_id=d.gift_id')
                    ->where(['a.user_id' => $id])->limit($p->firstRow . ',' . $p->listRows)
                    ->order('a.intime desc')
                    ->select();
                $this->assign("show", $p->show());
                $this->assign('give', $list);
                break;
            case 6:
                //关注列表
                $count = M('Follow')
                    ->alias('a')
                    ->join('__USER__ b on a.user_id2=b.user_id')
                    ->where(['a.user_id' => $id])
                    ->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Follow')
                    ->alias('a')
                    ->field('a.*, b.username, b.ID, b.employee_id')
                    ->join('__USER__ b on a.user_id2=b.user_id')
                    ->where(['a.user_id' => $id])->limit($p->firstRow . ',' . $p->listRows)
                    ->order('a.intime desc')
                    ->select();
                $this->assign("show", $p->show());
                $this->assign('f', $list);
                break;
            case 7:
                //粉丝列表
                $count = M('Follow')
                    ->alias('a')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where(['a.user_id2' => $id])
                    ->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Follow')
                    ->alias('a')
                    ->field('a.*, b.username, b.ID, b.employee_id')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where(['a.user_id2' => $id])->limit($p->firstRow . ',' . $p->listRows)
                    ->order('a.intime desc')
                    ->select();
                $this->assign("show", $p->show());
                $this->assign('fo', $list);
                break;
            case 8:
                //直播列表
                $count = M('Live')
                    ->alias('a')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where(['a.user_id' => $id])
                    ->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Live')
                    ->alias('a')
                    ->field('a.*, b.username, b.img, b.sex, b.phone, b.ID, b.employee_id')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->limit($p->firstRow . ',' . $p->listRows)
                    ->where(['a.user_id' => $id])
                    ->order('a.live_status asc,a.intime desc')
                    ->select();
                foreach ($list as $k => $v) {
                    $gift_count = M('Give_gift')->where(['live_id' => $v['live_id']])->sum('jewel');
                    $gift_count ? $list[$k]['gift_count'] = $gift_count : $list[$k]['gift_count'] = '0';
                }
                $this->assign('live', $list);
                $this->assign("show", $p->show());
                break;
            case 9:
                //录播列表
                $count = M('Live_store')
                    ->alias('a')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where(['a.user_id' => $id])
                    ->count();//一共有多少条记录
                $p = getpage($count, '10');
                $list = M('Live_store')
                    ->alias('a')
                    ->field('a.*, b.username, b.img, b.sex, b.phone, b.ID, b.employee_id')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->limit($p->firstRow . ',' . $p->listRows)
                    ->where(['a.user_id' => $id])
                    ->order('a.intime desc')->select();
                $this->assign('live_store', $list);
                $this->assign("show", $p->show());
                break;
        }
        $this->assign('state', $state);
        $this->assign('pagetitle', '详情');
        $this->display();
    }

    public function play()
    {
        $id = I('id');
        $live_store = M('Live_store')->find($id);
        $this->assign('l', $live_store);
        $this->display();
    }

    /**
     * @删除
     */
    public function del()
    {
        $id = I('ids');
        $rs = M('User')->where(['user_id' => ['in', $id]])->save(['is_del' => 2, 'del_time' => time()]);
        echo $rs ? 1 : 2;
    }


    /**
     * @已删除用户列表
     */
    public function del_user()
    {
        if (!M()->autoCheckToken($_POST)) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        $data = [
            'is_del' => 2
        ];
        if (!empty($_GET['username'])) {
            $data['phone|username|ID'] = ['like', '%' . $_GET['username'] . '%'];
            $this->assign('username', $_GET['username']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])) {
            $start = strtotime($_GET['start']);
            $data['intime'] = ['gt', $start];
            $this->assign('start', $_GET['start']);
        } elseif (empty($_GET['start']) && !empty($_GET['end'])) {
            $end = strtotime($_GET['end']) + (24 * 60 * 60 - 1);
            $data['intime'] = ['lt', $end];
            $this->assign('end', $_GET['end']);
        } elseif (!empty($_GET['start']) && !empty($_GET['end'])) {
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end']) + (24 * 60 * 60 - 1);
            $data['intime'] = ['between', [$start, $end]];
            $this->assign('start', $_GET['start']);
            $this->assign('end', $_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])) {
            $nus = intval($_GET['nums']);
        } else {
            $nus = 10;
        }
        $this->assign("nus", $nus);
        $count = M('User')->where($data)->count();//一共有多少条记录
        $p = getpage($count, $nus);
        $list = M('User')->limit($p->firstRow . ',' . $p->listRows)->where($data)->select();
        $this->assign('list', $list);
        $this->assign("show", $p->show());
        $this->assign('pagetitle', '已删除用户列表');


        $this->display();
    }

    /**
     * @恢复
     */
    public function restore()
    {
        $id = I('ids');
        $rs = M('User')->where(['user_id' => ['in', $id]])->save(['is_del' => 1, 'uptime' => time()]);
        echo $rs ? 1 : 2;
    }

    /**
     * @彻底删除
     */
    public function del_true()
    {
//        $id = I('ids');
//        $rs = M('User')->where(['user_id'=>['in',$id]])->delete();
//        echo $rs ? 1 : 2;
        $id = I('ids');
        $rs = M('User')->where(['user_id' => ['in', $id]])->save(['is_del' => 3, 'del_time' => time()]);
        echo $rs ? 1 : 2;
    }


    /**
     * @僵尸粉列表
     */
    public function fans()
    {
        if (!M()->autoCheckToken($_POST)) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        $data = [
            'is_fans' => 2
        ];
        if (!empty($_GET['username'])) {
            $data['phone|username|ID'] = ['like', '%' . $_GET['username'] . '%'];
            $this->assign('username', $_GET['username']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])) {
            $start = strtotime($_GET['start']);
            $data['intime'] = ['gt', $start];
            $this->assign('start', $_GET['start']);
        } elseif (empty($_GET['start']) && !empty($_GET['end'])) {
            $end = strtotime($_GET['end']) + (24 * 60 * 60 - 1);
            $data['intime'] = ['lt', $end];
            $this->assign('end', $_GET['end']);
        } elseif (!empty($_GET['start']) && !empty($_GET['end'])) {
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end']) + (24 * 60 * 60 - 1);
            $data['intime'] = ['between', [$start, $end]];
            $this->assign('start', $_GET['start']);
            $this->assign('end', $_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])) {
            $nus = intval($_GET['nums']);
        } else {
            $nus = 10;
        }
        $this->assign("nus", $nus);
        $count = M('User')->where($data)->count();//一共有多少条记录
        $p = getpage($count, $nus);
        $list = M('User')->limit($p->firstRow . ',' . $p->listRows)->where($data)->select();
        $this->assign('list', $list);
        $this->assign("show", $p->show());
        $this->assign('pagetitle', '僵尸粉列表');


        $this->display();
    }

    /**
     * @添加、修改映射
     */
    public function toadd_fans()
    {
        //省
        $sheng = M('Areas')->where("level=1")->select();
        $this->assign('sheng', $sheng);

        $id = I('id');
        if ($id) {
            $user = M('User')->find($id);
            $fid = M('Areas')->where(array('name' => $user['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['fid'] = $fid;
                $data['level'] = 2;
                $user['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $user['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['fid'] = $fid2;
                $date['level'] = 3;
                $user['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $user['qu'] = null;
            }
            $user['city_id'] = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            $user['area_id'] = M('Areas')->where(array('name' => $user['area'], 'level' => 3))->getField('id');
            $this->assign('u', $user);
            $sta = '编辑';
        } else {
            $sta = '添加';
        }
        $this->assign('pagetitle', $sta);
        $this->display();
    }

    /**
     * @修改
     */
    public function doadd_fans()
    {
        if (!M()->autoCheckToken($_POST)) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $logo = I('logo');
        empty($logo) ? $img = "/Public/admin/touxiang.png" : $img = $logo;
        $data = [
            'token' => uniqid(),
            'phone' => I('phone'),
            'img' => $img,
            'sex' => I('sex'),
            'username' => I('username'),
            'personalized_signature' => I('personalized_signature'),
            'province' => M('Areas')->where(array('id' => I('sheng')))->getField('name'),
            'city' => M('Areas')->where(array('id' => I('shi')))->getField('name'),
            'area' => M('Areas')->where(array('id' => I('qu')))->getField('name'),
            'address' => I('address'),
            'is_fans' => 2,
            'type' =>2,
        ];
        if ($id) {
            $data['uptime'] = time();
            M('User')->where(['user_id' => $id])->save($data) ? $this->success('成功!', U('fans')) : $this->error('失败!', U('fans'));
        } else {
            var_dump('nihao');exit;
            $chars = "abcdefghijklmnopqrstuvwxyz123456789";
            mt_srand(10000000 * (double)microtime());
            for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < 12; $i++) {
                $str .= $chars[mt_rand(0, $lc)];
            }
            $hx_password = "123456";
            $date = [
                'ID' => get_number(),
                'alias' => $str,
                'hx_username' => $str,
                'hx_password' => $hx_password,
                'intime' => time()
            ];
            $array = array_merge($data, $date);
            M('User')->add($array) ? $this->success('成功!', U('fans')) : $this->error('失败!', U('fans'));
        }
    }


    /*
     * @内训师
     */
    public function teacher_list()
    {
        if (!M()->autoCheckToken($_POST)) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        $data = [
            'is_del' => 1
        ];
        if (!empty($_GET['inner_name'])) {
            $data['inner_name'] = ['like', '%' . $_GET['inner_name'] . '%'];
            $this->assign('inner_name', $_GET['inner_name']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])) {
            $start = strtotime($_GET['start']);
            $data['intime'] = ['gt', $start];
            $this->assign('start', $_GET['start']);
        } elseif (empty($_GET['start']) && !empty($_GET['end'])) {
            $end = strtotime($_GET['end']) + (24 * 60 * 60 - 1);
            $data['intime'] = ['lt', $end];
            $this->assign('end', $_GET['end']);
        } elseif (!empty($_GET['start']) && !empty($_GET['end'])) {
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end']) + (24 * 60 * 60 - 1);
            $data['intime'] = ['between', [$start, $end]];
            $this->assign('start', $_GET['start']);
            $this->assign('end', $_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])) {
            $nus = intval($_GET['nums']);
        } else {
            $nus = 10;
        }
        $this->assign("nus", $nus);
        $count = M('inner_teacher')->where($data)->count();//一共有多少条记录
        $p = getpage($count, $nus);
        $list = M('inner_teacher')->limit($p->firstRow . ',' . $p->listRows)->where($data)->order('intime asc')->select();
        $this->assign('list', $list);
        $this->assign("show", $p->show());
        $this->assign('pagetitle', '内训师列表');

        $this->display();
    }

    /*
     * @添加、修改
     */
    public function techer_add()
    {
        //省
        $sheng = M('Areas')->where("level=1")->select();
        $this->assign('sheng', $sheng);

        $id = I('inner_teacher_id');
        if ($id) {
            $user = M('inner_teacher')->find($id);
            $fid = M('Areas')->where(array('name' => $user['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['fid'] = $fid;
                $data['level'] = 2;
                $user['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $user['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['fid'] = $fid2;
                $date['level'] = 3;
                $user['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $user['qu'] = null;
            }
            $user['city_id'] = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            $user['area_id'] = M('Areas')->where(array('name' => $user['area'], 'level' => 3))->getField('id');
            $this->assign('u', $user);
            $sta = '编辑';
        } else {
            $sta = '添加';
        }
        $this->assign('pagetitle', $sta);
        $this->display();
    }

    /*
     * @添加、修改逻辑处理
     */
    public function techer_doadd()
    {
        if (!M()->autoCheckToken($_POST)) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('inner_teacher_id');
        $logo = I('logo');
        empty($logo) ? $img = "/Public/admin/touxiang.png" : $img = $logo;
        $data = [
            'inner_img' => $img,
            'sex' => I('sex'),
            'inner_name' => I('inner_name'),
            'abstract' => I('abstract'),
            'province' => M('Areas')->where(array('id' => I('sheng')))->getField('name'),
            'city' => M('Areas')->where(array('id' => I('shi')))->getField('name'),
            'area' => M('Areas')->where(array('id' => I('qu')))->getField('name'),
            'address' => I('address'),
        ];
        if ($id) {
            $data['uptime'] = time();
            M('inner_teacher')->where(['inner_teacher_id' => $id])->save($data) ? $this->success('成功!', U('teacher_list'), 1) : $this->error('失败!', U('teacher_list'));
            die;
        } else {
            $data['intime'] = time();
            if ($ids = M('inner_teacher')->add($data)) {
                $this->success('成功!', U('teacher_list'), 1);
            } else {
                $this->error('失败!', U('teacher_list'));
            }
            die;
        }
    }

    /**
     * @删除
     */
    public function techer_del()
    {
        $id = I('ids');
        $rs = M('inner_teacher')->where(['inner_teacher_id' => ['in', $id]])->save(['is_del' => 2]);
        echo $rs ? 1 : 2;
    }
}

