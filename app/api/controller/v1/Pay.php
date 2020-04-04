<?php


namespace app\api\controller\v1;


use app\common\model\OAuth2Client;
use app\common\model\Order;
use lemo\api\Api;
use lemo\api\Oauth;
use lemo\api\validate\ValidataBase;
use service\HttpService;
use think\facade\Db;
use think\facade\Route;


class Pay extends Api
{
    /**
     * 指定通道
     * 非指定通道
     * channelId
     *
     */
    /**
     * 不需要鉴权方法
     */
    protected $noAuth = ['index'];

    public function index()
    {

        $param = input();


        if ($param == null) {
            return self::returnMsg(-1, '请求参数不能为空');

        }
        $oauth = Db::name('oauth2_client')->where(['appid' => $param['merId']])->find();


        $rule = [
            'merId' => 'require',
            'orderId' => 'require',
            'orderDate' => 'require',
//            'ip' => 'require',
            'type' => 'require',
            'orderMoney' => 'require',
            'returnUrl' => 'require',
            'notifyUrl' => 'require',
            'sign' => 'require',
            'productName' => 'require',


        ];
        ValidataBase::validateCheck($rule, $param);


        $user = Db::name('user')->field(array( 'id'))
            ->where(array('id' => $oauth['uid'],
                ))->find();

        if ($user == null) {
            return self::returnMsg(-2,'账号不存在');

        }


        if (!isset($param['orderMoney']) || $param['orderMoney'] <= 0) {
            return $this->error("金额错误");
        }


      if (! Oauth::makeSign($param,$oauth['appsecret'])){
         return self::returnMsg(0,'签名不正确');
      }

//
//        $whiteIp = db('pay_black_ip')->where(array('ip' => $param['ip'], 'status' => 2))
//            ->find();
//        if ($whiteIp == null) { //白名单优先原则
//
//            $payCount = db('pay_order')->where(array('ip' => $param['ip'], 'status' => 0, 'cTime' => array('egt', time() - 10 * 60)))->count();
//
//            if ($payCount > 100) {
//                $black_ip =
//                    array(
//                        'mTime' => time(),
//                        'cTime' => time(),
//                        'ip' => $param['ip'],
//                        'status' => 1,
//                        'info' => json_encode($param),
//                        'uid' => '10000',
//                        'userip' => request()->ip(),
//                        'descript' => '支付异常,您当前ip存在过多未支付,请联系管理员进行验证'
//                    );
//                db('pay_black_ip')->insert($black_ip);
//
//            }
//
//
//            $blackIp = db('pay_black_ip')->where(array('ip' => $param['ip'], 'status' => 1))->find();
//            if ($blackIp != null) {
//                $iprecord =
//                    array(
//                        'mTime' => time(),
//                        'cTime' => time(),
//                        'ip' => $param['ip'],
//                        'info' => json_encode($param),
//                        'desc' => '黑名单记录'
//                    );
//                db('pay_black_record')->insert($iprecord);
//
//                return $this->error($blackIp['descript']);
//            }
//        }


      //  $merWhiteIp = db('pay_black_ip')->where(array('ip' => request()->ip(), 'status' => 2))->find();

//        if ($merWhiteIp == null) {
//            $merBlackIp = db('pay_black_ip')->where(array('ip' => request()->ip(), 'status' => 1))->find();
//            if ($merBlackIp != null) {
//                $iprecord =
//                    array(
//                        'mTime' => time(),
//                        'cTime' => time(),
//                        'ip' => request()->ip(),
//                        'info' => json_encode($param),
//                        'desc' => '黑名单记录'
//                    );
//                db('pay_black_record')->insert($iprecord);
//
//                return $this->error($blackIp['descript']);
//            }
//        }

//
//        $payUrl = db('pay_url')->where(
//            array(
//                'status' => 1,
//                'url' => array('NEQ', '')
//            )
//        )
//            ->orderRaw('useTime/weight  asc,mTime desc')
//            ->find();
        $orderdb = Db::name("order");

        if ($orderdb->where(array('orderId' => $param['orderId'],
                'uId' => $oauth['uid']))->count() == 0) {
            unset($param['merId']);
            $param['uid']=$oauth['uid'];

            $param['mIp'] = request()->ip();
            $param['cTime']=time();
            unset($param['sign']);
          //  $param['uId']=$oauth['uid'];
            $ret = Db::name('order')->insertGetId($param);
            $param['id'] = $ret;

            $order = $param;
        } else {
            $order = $orderdb->where(['orderId' => $param['orderId'],
                'uId' => $oauth['uid']])->find();
            if ($order['status'] == 1) {
                return self::returnMsg('0', '已支付');
            }

        }




      //  if ($payUrl == null || null == $payUrl['url'] || empty($payUrl['url'])) {
            $pay = new \app\pay\service\Pay();

            $ret = $pay->pay($order);


            if (isset($ret)&&!empty($ret['url']) ) {
                if (isset($ret['extra'])) {
                    return self::returnMsg(1, '',array('url' => $ret['url'], 'extra' => $ret['extra']));
                } else {
                    return self::returnMsg(1,'',array('url' => $ret['url']));
                }

            } else {
                return self::returnMsg(0,$ret['msg']);
            }
       // }
//        Db::name('PayUrl')->where(array('id' => $payUrl['id']))->setInc('useTime', 1);
//
//        $param['mIp'] = request()->ip();
//        $ret = HttpService::send_post($payUrl['url'], $param, 60, array('version' => 'v4.0'));
//        $ret = json_decode(html_entity_decode($ret), true);
//
//
//        if ($ret == null) {
//            return $this->error("未获取到信息,请稍后重试");
//        }
//        if (isset($ret['data']['url']) && !empty($ret['data']['url'])) {
//
//            return $this->success(array('url' => $ret['data']['url']));
//        }
//
//        if (isset($ret['msg']) && !empty($ret['msg'])) {
//
//            return $this->error($ret['msg']);
//        }
    }


}