<?php
namespace App\Controller;

use Think\Controller;
use Pingpp\Charge;
use Pingpp\Pingpp;

class PingxxController extends CommonController
{
    private $system=array();
    function _initialize(){
        $this->system=M("system")->where("id=1")->find();
    }

    /**
     * pingxx支付
     * @param $orderNo
     * @param $type
     * @param $openid
     */
    public function ping(){
    	$user = checklogin();
        $amount = I('money');  //金额
        $diamond = I('diamond');  //钻石
        $type = I('type');
        (empty($amount) || $amount==0 || $diamond==0) ? error('参数错误!') : true;
        $pay_number = date('YmdHis').rand(100,999);
        if($type==null)
            $type="wx";
        }
        M('Recharge_record')->add(array('user_id'=>$user['user_id'],'pay_number'=>$pay_number,'amount'=>$amount,'diamond'=>$diamond,'pay_on'=>'','pay_return'=>'','pay_type'=>$type,'intime'=>time()));
        $number = $pay_number.rand(100, 999);
        $this->pings($type,$number,I("openid"));
    }


    function pings($type,$number,$openid)
    {
        vendor("Pingpp.init");
        Pingpp::setApiKey($this->system['secretkey']);

        $amount = M('Recharge_record')->where(array('pay_number'=>substr($number, 0, 17)))->getField('amount');
        if($amount==null){
            $amount=1;
        }else{
            $amount *= 100;
        }

        if($number==null){
            $number="m".time();
        }
        try { 
        	$extra = array();
            
            if($type=="alipay_wap"){
                $extra["success_url"]="http://www.baidu.com";
            }else if($type=="wx_pub"){
                $extra["open_id"]=$openid;
            }
            $ch = Charge::create([
                'order_no' => $number,
                'amount' => $amount,
                'channel' => $type,
                'currency' => 'cny',
                'client_ip' => get_client_ip(),
                'subject' => "充值",
                'body' => 'Your Body',
                'app' => ['id' => $this->system['apiid']],
                'extra'=> $extra
            ]);

            echo '{"status": "ok","data":'.$ch.'}';
        } catch (\Pingpp\Error\Base $e) {
            header('Status: ' . $e->getHttpStatus());
            echo('{"status":"pending","error":'.$e->getHttpBody()."}");
        }
    }

    /**
     * 订单返回值
     */
    public function callback()
    {   
        $result = file_get_contents('php://input');
        $arr = json_decode($result, true);
        if ($arr['data']['object']['order_no']) {
            $data = array(
                "pay_on"=>$arr['data']['object']['order_no'],
                "pay_return"=>$result,
                "uptime"=>time()
            );
            $rec = M('Recharge_record')->where(array('pay_number'=>substr($arr['data']['object']['order_no'], 0, 17)))->find();
            M('Recharge_record')->where(array('recharge_record_id'=>$rec['recharge_record_id']))->save($data); //支付成功!
            $money = (M('User')->where(array('user_id'=>$rec['user_id']))->getField('money'))+$rec['diamond'];
            M('User')->where(array('user_id'=>$rec['user_id']))->save(array('money'=>$money,'uptime'=>time()));  //修改用户钻石数
        }
    }

    /**
     * 账户余额
     */
    public function balance()
    {
        $map['member_id'] = $this->member_id;
        $map['type'] = 1;
        $result = M('recharge')->where($map)->sum('amount');
        if(empty($result)){
            apiSuccess(0);
        } else {
            apiSuccess($result);
        }
    }

    /**
     * 充值、提取记录
     * type = 1充值
     */
    public function charges()
    {
        $map['member_id'] = $this->member_id;
        $list = M('recharge')->where($map)->select();

        apiSuccess([
            'chargeList' => $list
        ]);
    }

    /**
     * 提现,个人中心提现
     * type = 0提现
     * status = 0待审核
     */
    public function takeback()
    {
        $data['order_no'] = date('YmdHis');
        $data['member_id'] = $this->member_id;
        $data['amount']  = I('amount');
        $data['type'] = 0;
        $data['status'] = 0;
        $data['create_time'] = time();
        $result = M('recharge')->add($data);
        if($result){
            apiSuccess('提现申请成功');
        } else {
            apiSuccess('提现申请失败');
        }
    }

    /**
     * 充值,个人中心充值
     */
    public function recharge()
    {
        $amount = I('amount') * 100;
        vendor("Pingpp.init");
        Pingpp::setApiKey($this->apiKey);
        try {
            $ch = Charge::create([
                'order_no' => $this->member_id.'d'.date('YmdHis'),
                'amount' => $amount,
                'channel' => 'wx',
                'currency' => 'cny',
                'client_ip' => get_client_ip(),
                'subject' => '充值',
                'body' => '充值',
                'app' => ['id' => $this->appID]
            ]);

            echo $ch;
        } catch (\Pingpp\Error\Base $e) {
            header('Status: ' . $e->getHttpStatus());
            echo($e->getHttpBody());
        }
    }


    public function rechargeHook()
    {
        $post = file_get_contents('php://input');
        $result = json_decode($post, true);
        if(strpos($result['data']['object']['order_no'], 'd') < 0){
            http_response_code(200);
            exit;
        }

        if ($result['type'] == 'charge.succeeded') {
            $arr = explode('d', $result['data']['object']['order_no']);
            $data['member_id'] = $arr[0];
            $data['order_no'] = $arr[1];
            $data['amount'] = $result['data']['object']['amount'];
            $data['type'] = 1;
            $data['status'] = 1;
            $data['create_time'] = time();
            $result = M('recharge')->add($data);
            if($result) {
                http_response_code(200);
            } else {
                http_response_code(400);
            }
        }
    }

}