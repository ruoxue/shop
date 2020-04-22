<?php


namespace app\pay\controller;


use app\common\controller\Frontend;
use app\common\model\Bank;
use app\common\model\OrderPay as OrderPayModel;
use app\common\model\OrderPay;
use app\common\model\PayAccount;
use app\common\model\PayChannel;
use think\facade\Db;
use think\facade\Request;

class Index extends Frontend
{

    /**
     * 302跳转
     * 验证ip 风险ip禁止
     */
    public function index($payId)
    {
        if (!$payId){
            $this->redirect('https://google.com',[],404);
        }
        if ($payId<=0){
            $this->redirect('https://google.com',[],404);
        }
        $orderPay = OrderPayModel::getOne($payId);


        if ($orderPay['isPay']){
            exit('已支付');
        }
        if ($orderPay == null) {
            echo 'null';
        } else {
            if ($orderPay['canGo']) {
                $orderPay = OrderPay::getOne($payId);
                $payChannel = PayChannel::getOne($orderPay['channelId']);
                $class = new \ReflectionClass($payChannel['api']); //
                $instance = $class->newInstanceArgs();
                $ret = $instance->pay($orderPay);
                $this->redirect($ret['url']);
            } else {

                $this->redirect(url('pay/index/pay')->vars(['payId' => $payId])
                    ->domain(true)->build());
            }


        }

    }


    public function pay($payId)
    {
        $orderPay = OrderPay::getOne($payId);
        if (isMobile()) {
            if ($this->isAliClient()) {
                if ($orderPay['pay_url']) {
                    $this->redirect($orderPay['pay_url']);
                } else {
                    $this->redirect(url('pay/index/alipay')->vars(['payId' => $payId])
                        ->domain(true)->build());
                }
            } else {

                if ($orderPay['canGo']) {

                    if ($orderPay['pay_url']) {
                        $this->redirect($orderPay['pay_url']);
                    } else {
                        $this->redirect(url('pay/index/alipay')->vars(['payId' => $payId])
                            ->domain(true)->build());
                    }


                } else {

                    if ($orderPay['pay_url']) {
                       return  view('mobile',
                            ['url' =>$orderPay['pay_url'], 'name' => $orderPay['show'],
                                'orderId' => $orderPay['mark'], 'money' => $orderPay['tradeMoney']]);

                    } else {
                        return view('mobile',
                            ['url' =>url('pay/index/alipay')->vars(['payId' => $payId])
                                ->domain(true)->build(), 'name' => $orderPay['show'],
                                'orderId' => $orderPay['mark'], 'money' => $orderPay['tradeMoney']]);

                    }


                }

            }

        } else {
            return view('pc', ['url' => url('pay/index/pay')->vars(['payId' => $payId])
                ->domain(true)->build(), 'name' => $orderPay['show'],
                'orderId' => $orderPay['mark'], 'money' => $orderPay['tradeMoney']]);

        }
    }

    function isAliClient()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'Alipay') !== false;
    }

    /**
     * 支付宝
     */
    public function alipay($payId)
    {
        $orderPay = OrderPay::getOne($payId);
        $payChannel = PayChannel::getOne($orderPay['channelId']);
        $class = new \ReflectionClass($payChannel['api']); //
        $instance = $class->newInstanceArgs();
        $ret = $instance->pay($orderPay);
        $orderPay2 = OrderPay::getOne($payId);
        return view('mobile',
            ['url' => $orderPay2['pay_url'], 'name' => $orderPay['show'],
                'orderId' => $orderPay['mark'], 'money' => $orderPay['tradeMoney']]);


    }

    public function wechat()
    {
    }


    public function wap($id)
    {

        $ret = OrderPay::getOne($id);


        if ($ret==null||$ret['isPay']==1){
            exit('已支付或订单不存在');
        }

        if ($ret['cTime']+10*60<time()){
            exit('已支付或订单不存在1');
        }


        echo $ret['info'];
    }



    public  function  union($id){

      $orderPay=  OrderPay::getOne($id);


      if ($orderPay==null||$orderPay['isPay']==1){
          exit('已支付或订单不存在');
      }

      if ($orderPay['cTime']+10*60<time()){
          exit('已支付或订单不存在1');
      }


      $account=PayAccount::where(['userId'=>$orderPay['userId']])->find();
        $bank=Bank::where(['id'=>$account['bankId']])->find();
        $where['id'] = $id;

       OrderPay::where(['id'=>$orderPay['id']])->where(['mTime'=>time()]);
        return view('union',['userId'=>$account['userId'],
            'tradeMoney'=>$orderPay['tradeMoney'],
            'realNamed'=>$account['realNamed'],
            'bankName'=>$bank['name'],
            'return'=> url('pay/index/localreturn')->vars(['mark' => $orderPay['mark']])
                ->domain(true)->build()
        ]);
    }



    public function localreturn($mark=''){

        if (Request::isPost()){
            if (empty($mark)){
                $this->error('订单不存在','');
            }

           $orderPay= Db::name('order_pay')
               ->alias('pay')
               ->join('order','order.id=pay.orderId')
               ->where(['mark'=>$mark])
               ->field('pay.isPay,pay.orderId,order.returnUrl')
               ->find();

           if ($orderPay){

               $this->success('','',$orderPay);
           }else{
               $this->error('订单不存在','');
           }


        }else{

            return view('return');
        }


    }

}