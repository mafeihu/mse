<?php
namespace App\Controller;
use Admin\Controller\PublicController;

use Admin\Controller\CommonController;

use Org\Util\Date;
use Think\Upload;
use Think\Controller;
class LoginController extends CommonController {

    public function _initialize(){
        $this->system = M('system')->where(['id'=>1])->find();
    }
  /**
	 *获取openid
	 */
	public function getOpenId()
	{
		$code = cookie('url');
		$code = urldecode($code);
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->system['appid'] . "&secret=" . $this->system['appsecret'] . "&code=" . I("code") . "&grant_type=authorization_code";
		$result = curl_get($url);
		$arr = json_decode($result, true);
		$url1 = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $this->system['appid'] . "&grant_type=refresh_token&refresh_token=" . $arr['refresh_token'];
		$arr1 = curl_get($url1);
		$arr1 = json_decode($arr1, true);
		$url2 = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $arr1['access_token'] . "&openid=" . $this->system['appid'] . "&lang=zh_CN";
		$arr2 = curl_get($url2);
		$openid = $arr['openid'];
		if (!empty($openid)) {
			$check = M('User')->field('user_id,token,openid,hx_username,hx_password')->where(['openid' => $openid])->find();
			if (!$check) {
				$parse = explode('?', $code); //截取参数
				$count = count($parse);
				$parse = $parse[$count - 1];
				parse_str($parse, $e); //参数转化为数组
				$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
				mt_srand(10000000 * (double)microtime());
				for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < 12; $i++) {
					$str .= $chars[mt_rand(0, $lc)];
				}
				for ($i = 0, $str1 = '', $lc = strlen($chars) - 1; $i < 13; $i++) {
					$str1 .= $chars[mt_rand(0, $lc)];
				}
				$hx_password = '123456';
				$result =  huanxin_zhuce($str, $hx_password);
				if($result){
					$data['hx_password'] = $hx_password;
					$data['hx_username'] = $str;
					$data['alias'] = $str;
				}
				$data['score'] = 500;
				$arr2 = json_decode($arr2, true);
				$data['openid'] = $openid;
				$data['token'] = uniqid();
				$data['intime'] = date("Y-m-d H:i:s", time());
				$data['username'] = $arr2['nickname'];
				$data['img'] = $arr2['headimgurl'];
				$data['sex'] = $arr2['sex'];
				$data['province'] = $arr2['province'];
				$data['city'] = $arr2['city'];
				$result = M('User')->add($data);

		/*		$url = "http://91dreambar.com/web/#/?uid=" . $result;
				$middle = "./Uploads/qrcode/" . md5($url) . '_middle.png';
				qrcode($url, $middle, 4, 5);
				$share_qrcode = "/Uploads/qrcode/" . md5($url) . '_middle.png';
				M('User')->where(['user_id' => $result])->save(['share_qrcode' => $share_qrcode]);*/
/*				if (!empty($e['uid'])) {
					$today = strtotime(date("Y-m-d", time()));
					$count = M('Share')->where(['mid' => $e['uid'], 'intime' => ['gt', $today]])->count();
					if ($count < 6) {
						$share_arr['mid'] = $e['uid'];
						$share_arr['share_id'] = $result;
						$share_arr['intime'] = date("Y-m-d H:i:s");
						$share_arr['score'] = 200;
						$res = M('Share')->add($share_arr);
						if ($res) {
							M('Member')->where(['member_id' => $e['uid']])->setInc("score", 200);
						}
					}
				}
*/
				    $check = M('User')->where(['user_id' => $result])->find();
					$check = json_encode(array('uid' => $check['user_id'], 'token' => $check['token'], 'openid' => $check['openid'],
						'phone' => $check['phone'], 'hx_username' => $check['hx_username'], 'hx_password' => $check['hx_password']));
				} else {
					$arr2 = json_decode($arr2, true);
					$data['img'] = $arr2['headimgurl'];
					$data['sex'] = $arr2['sex'];
					$data['username'] = $arr2['nickname'];
					$data['province'] = $arr2['province'];
					$data['city'] = $arr2['city'];
					M('User')->where(['user_id' => $check['user_id']])->save($data);
					$check = json_encode(array('uid' => $check['user_id'], 'token' => $check['token'], 'openid' => $check['openid'],
						'phone' => $check['phone'], 'hx_username' => $check['hx_username'], 'hx_password' => $check['hx_password']));
				}
				cookie('user', $check);
			}
			if (!empty($code)) {
				header('location:' . $code);
			}
	}
    /**\
     *判断用户是否认证
     */
    public function is_authentication(){
        $user_id = I("user_id");
        if(empty($user_id)){
            error("参数错误");
            return false;
        }
        $user = M('member_info')->where(['user_id'=>$user_id])->find();
        if($user){
            success("已经认证");
        }else{
            success("未认证");
        }
    }
    /**
     * 进行实名认证
     */
    public function real_authentication(){
        $user_id = I("user_id");
        $real_name = I('real_name');
        $card_id = I('card_id');
        $employee_id = I('employee_id');
        if(empty($user_id) || empty($real_name) || empty($card_id) || empty($employee_id) || empty($user_id)){
            error('参数有误');
        }
        $data['real_name'] = $real_name;
        $data['card_id'] = $card_id;
        $data['employee_id'] = $employee_id;
        $ren_result = M("member_info")->where($data)->find();
        if($ren_result){
            $renzheng = M("member_info")->where($data)->save(['user_id'=>$user_id]);
            success("认证成功");
            if($renzheng){
                success("数据更新成功");
            }else{
                error("数据更新失败");
            }
        }else{
            error('认证失败');
        }
    }
  /*
   * @发送短信
   * @type 1:注册  2:找回密码
   * Enter description here ...
   */
  public function sendSMS(){
		$mobile = I('mobile');
		if (empty($mobile) || !preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}|^17[0-9]\d{8}$#', $mobile)) {
			error("手机格式不正确");
		}else{
			$type = I("type");
			empty($type) ?error('参数错误!') :true;
		    $date = M("User")->where(array('phone'=>$mobile))->find();
            $time = M('System')->getFieldById(1,'code_volidity');
		    $mobile_code = random(6, 1);
		    $_SESSION['mobile_code'] = $mobile_code;
		    if ($type==1){
		    	if ($date){
		    	   error('已注册！');
		    	}else {
		    		$content = "您的验证码：".$mobile_code.",有效时间:".$time."分钟,请不要轻易把验证码泄露给其他人。【梅 塞 尔】";
		    	}
		    }elseif ($type==2){
		    	if ($date){
		    	    $content = "您修改密码的短信验证码为：".$mobile_code."，请妥善保存。【梅 塞 尔】";
		    	}else {
		    	   error('未注册！');
		    	}
		    }elseif($type==3){
				if($date){
					$content = "您登陆的短信验证码为：".$mobile_code."，请妥善保存。 【梅 塞 尔】";
				}else{
					error('未注册！');
				}
			}elseif($type==4){
				//if($date){
                    //success($mobile_code);
					$content = "您的短信验证码为：".$mobile_code."，有效时间:".$time."分钟,请妥善保存。【梅 塞 尔】";
				//}else{
					//error('未注册！');
				//}
			}
            $gateway =zhutong_sendSMS($content, $mobile);
            $arr = explode(',',$gateway);
            switch ($arr['0']){
                case 1:
                    M('Mobile_sms')->add(['mobile'=>$mobile,'code'=>$mobile_code,'state'=>1,'date'=>date('Y-m-d',time()),'intime'=>time()]);
                    success('发送成功!');
                    break;
                case 12:
                    error('提交号码错误!');
                    break;
                case 13:
                    error('短信内容为空!');
                    break;
                case 17:
                    error('一分钟内一个手机号只能发两次!');
                    break;
                case 19:
                    error('号码为黑号!');
                    break;
                case 26:
                    error('一小时内只能发五条!');
                    break;
                case 27:
                    error('一天一手机号只能发20条');
                    break;
                default:
                    error('发送失败!');
            }
		}
	}
    /**
     * @登录(注册)
     */
    public function message_login(){
        $phone = I('phone');  $yzm = I('yzm'); $log = I('log');  $lag = I('lag');
        (empty($phone) || !preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}|^17[0-9]\d{8}$#', $phone) ||empty($yzm)) ? error('参数错误!') : true;
        $code = M('Mobile_sms')->where(['phone'=>$phone,'state'=>1])->order('intime desc')->limit(1)->find();
        if ($code) {
            $time = M('System')->getFieldById(1,'code_volidity');
            if (time()-$code['intime']>($time*60)){
                error('验证码已失效!');
            }
            if ($code['code']==$yzm){
                $user = M('User')->where(array('phone'=>$phone))->find();
                if ($user){
                    //登陆
                    if($user['is_del']==2){
                        error('账号被限制,请联系平台!');
                    }else{
                        $user['img'] = C('IMG_PREFIX').$user['img'];
                        $gwd = $lag.','.$log;
                        $baidu_apikey = M('System')->getFieldById(1,'baidu_apikey');
                        $file_contents = file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.$baidu_apikey.'&location='.$gwd.'&output=json');
                        $rs = json_decode($file_contents,true);
                        M('Login_hostroy')->add(['user_id'=>$user['user_id'],'log'=>$log,'lag'=>$lag,'area'=>$rs['result']['addressComponent']['city'],'address'=>$rs['result']['formatted_address'],'intime'=>time(),'date'=>date('Y-m-d',time())]);
                        success($user);
                    }
                }else{
                    //注册
                    $chars = "abcdefghijklmnopqrstuvwxyz123456789";
                    mt_srand(10000000*(double)microtime());
                    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < 12; $i++){
                        $str .= $chars[mt_rand(0, $lc)];
                    }
                    $photo = "/Public/admin/touxiang.png";
                    $hx_password="123456";
                    $date = [
                        'token'=>uniqid(),
                        'phone'=>$phone,
                        'username'=>GetfourStr(6),
                        'img'=>$photo,
                        'ID'=>get_number(),
                        'intime'=>time(),
                        'alias'=>$str,
                        'hx_username'=>$str,
                        'hx_password'=>$hx_password,
                    ];
                    if ($id=M('User')->add($date)){
                        huanxin_zhuce($str,$hx_password); //环信注册
                        $us = M('User')->where(['user_id'=>$id])->find();
                        $us['img'] = C('IMG_PREFIX').$us['img'];
                        success($us);
                    }else {
                        error('失败!');
                    }
                }
            }
        }else{
            error('验证码不一致!');
        }
    }

	/**
	 * @第三方登陆（微信，qq）
	 * @state 1:微信  2：qq    3:微博
     * @type  1:第一步,登录(注册),  2:完善性别和地区
	 */
	public function login(){
        $state = I('state'); //$type = I('type');
        $openid = I('openid');  $log = I('log');  $lag = I('lag');
        (empty($state) || empty($openid)) ? error('参数错误!') : true;
        ($state==1 || $state==2 || $state==3) ? true : error('传值错误');
        switch ($state){
            case 1:$data['openid'] = $openid;break;
            case 2:$data['qq_openid'] = $openid;break;
            case 3:$data['weibo'] = $openid;break;
        }
        $user = M('User')->where($data)->find();
        if ($user){
            if ($user['is_del']==2){
                error('账号被限制,请联系平台!');
            }else{
                $user['img'] = C('IMG_PREFIX').$user['img'];
                M('User')->where(array('user_id'=>$user['user_id']))->save(array('token'=>uniqid()));
                $user['token'] = M('User')->where(array('user_id'=>$user['user_id']))->getField('token');

                $gwd = $lag.','.$log;
                $baidu_apikey = M('System')->getFieldById(1,'baidu_apikey');
                $file_contents = file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.$baidu_apikey.'&location='.$gwd.'&output=json');
                $rs = json_decode($file_contents,true);
                M('Login_hostroy')->add(['user_id'=>$user['user_id'],'log'=>$log,'lag'=>$lag,'area'=>$rs['result']['addressComponent']['city'],'address'=>$rs['result']['formatted_address'],'intime'=>time(),'date'=>date('Y-m-d',time())]);
            }
        }else{
            $chars = "abcdefghijklmnopqrstuvwxyz123456789";
            mt_srand(10000000*(double)microtime());
            for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < 12; $i++){
                $str .= $chars[mt_rand(0, $lc)];
            }
            $filename = "/Public/admin/touxiang.png";
            $hx_password="123456";
            $date = [
                'token'=>uniqid(),
                'username'=>GetfourStr(6),
                'img'=>$filename,
                'ID'=>get_number(),
                'intime'=>time(),
                'alias'=>$str,
                'hx_username'=>$str,
                'hx_password'=>$hx_password,
            ];
            switch ($state){
                case 1:$date['openid'] = $openid;break;
                case 2:$date['qq_openid'] = $openid;break;
                case 3:$date['weibo'] = $openid;break;
            }
            if ($user_id = M('User')->add($date)){
                huanxin_zhuce($str,"123456"); //环信注册

                $gwd = $lag.','.$log;
                $baidu_apikey = M('System')->getFieldById(1,'baidu_apikey');
                $file_contents = file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.$baidu_apikey.'&location='.$gwd.'&output=json');
                $rs = json_decode($file_contents,true);
                M('Login_hostroy')->add(['user_id'=>$user_id,'log'=>$log,'lag'=>$lag,'area'=>$rs['result']['addressComponent']['city'],'address'=>$rs['result']['formatted_address'],'intime'=>time(),'date'=>date('Y-m-d',time())]);

                $user = M('User')->find($user_id);
                $user['img'] = C('IMG_PREFIX').$user['img'];

                //添加消息
                $no = M('Notice')->where(['state'=>2,'is_del'=>1])->select();
                foreach ($no as $k=>$v){
                    M('Message')->add(['type'=>1,'user_id2'=>$user_id,'content'=>$v['content'],'intime'=>time(),'date'=>date('Y-m-d',time())]);
                }

            }else{
                error('注册失败!');
            }
        }
        success($user);
	}
    /**
     * @找回密码
     */
    public function forgetpwd(){
       extract(I('post.'));
       $phone = I('phone');
       $yzm = I('yzm');
       $newpwd = I('newpwd');
		(empty($phone)  ||  empty($yzm) || empty($newpwd)) ? error('参数错误!') : true;
       $me = M('User')->where(array('phone'=>$phone))->find();
       if ($me){
           if ($yzm==$_SESSION['mobile_code']){
			   $data = array(
				   "user_id"=>$me['user_id'],
				   "pwd"=>md5($newpwd),
				   "uptime"=>time()
			   );
               if (M('User')->save($data)){
                  success('密码已修改!');
               }else {
                  error('密码修改失败!');
               }
           }else {
               error('验证码错误!');
           }
       }else {
          error('未注册!');
       }
    }

    /**
     * @修改昵称和头像
     */
    public function up_user(){
        $user_id = I('user_id'); $nickname = I('nickname');
        M('User')->where(['nickname'=>$nickname])->find() ? error('昵称已存在!') : true;
        $config = [
            'maxSize'	=> 30*3145728,
            'rootPath'	=> './Uploads/image/touxiang/',
            'savePath'	=> '',
            'saveName'	=> ['uniqid',''],
            'exts'		=> ['png','jpg','jpeg','git','gif'],
            'autoSub'	=> true,
            'subName'	=> '',
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info){
            foreach($info as $file){
                $a = '/Uploads/image/touxiang/'.$file["savename"];
                $img = $a;
            }
        }
        if (M('User')->where(['user_id'=>$user_id])->save(['img'=>$img,'nickname'=>$nickname,'uptime'=>time()])){
            M('User')->where(array('user_id'=>$user_id))->save(array('token'=>uniqid()));
            $user = M('User')->where(['user_id'=>$user_id])->find();
            $img_prefix = C('IMG_PREFIX');
            $user['img'] = $img_prefix.$user['img'];
            success($user);
        }else{
            error('失败!');
        }
    }


    public function add(){
        echo GetfourStr(4);die;
        $dataList[] = array('user_id'=>'1','keywords'=>'thinkphp@gamil.com');

        M('Search')->addAll($dataList);
    }


    //上传图片
    public function upload_img(){
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
        $info = $Upload->upload();
        if ($info) {
            foreach ($info as $file) {
                $img_path = trim($config['rootPath'], '.') . $file['savepath'] . $file['savename'];
            }
            success($img_path);
        } else {
            exit($Upload->getError());
        }
    }
}
