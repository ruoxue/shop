<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\model\Address as AddressModel;
use app\common\model\Area as AreaModel;
use app\common\model\PayChannel as PayChannelModel;
use app\common\model\GoodsSku as GoodsSkuModel;
use app\common\model\Oauth2Client as Oauth2ClientModel;

use think\facade\View;

class Shop extends Frontend
{
    /**
     * 展示 产品信息
     */
    public function index()
    {

    }


    public function cart()
    {

    }

    /**
     *  收银台
     */
    public function buy($money=0, $skuId, $num = 1, $sign = '',$addressId=0)
    {

        $sku = GoodsSkuModel::getOne($skuId);
        if ($money < 0 && $sku == null) {
            exit("err");
        }


        if ($money < 0) {
            if ($num < 1) {
                exit("err2");
            }
            $money = $sku['price'] * $num;
        }


        if (\think\facade\Request::isPost()) {
            $pid = \think\facade\Request::post('pid', '0', 'trim');

            $goods = PayChannelModel::getList(['pid' => $pid], 0, []);
            if ($goods == null) {
                return "不能支付不存在";
            } else {

                $token = session('payToken');
                $sign2 = md5($token . $money);
                if ($sign == $sign2) {
                    $oauth2Client = Oauth2ClientModel::where(['uid' => 1])->find();

                    $data['orderId'] = time();
                    $data['orderDate'] = time();
                    $data['ip'] = request()->ip();
                    $data['merId'] = $oauth2Client['appid'];
                    $data['type'] = $pid;
                    $data['orderMoney'] = $money;
                    $data['productName'] = $sku['title'];
                    $data['addressId']=$addressId;

                    $preArr = array_merge($data, ['key' => $oauth2Client['appScript']]);


                    ksort($preArr);
                    //  exit(json_encode($preArr));
                    $preArr = http_build_query($preArr);
                    // exit(($preArr));
                    $data['mIp'] = request()->ip();
                    $data['sign'] = md5(urldecode($preArr));
                    $data['skuId'] = $skuId;
                    $data['num'] = $num;


                    $data['returnUrl'] = url('/api/v1.pay/index', [],
                        false, request()->domain())->build();

                    $data['notifyUrl'] = url('/api/v1.pay/index', [],
                        false, request()->domain())->build();
// exit(http_build_query($data));

                    $ret = send_post(url('/api/v1.pay/index', [], false, request()->domain())->build()
                        , $data);

                    $ret = json_decode(html_entity_decode($ret), true);


                    if ($ret['code'] == 1) {

                        $this->success('ok', $ret['data']['url'], $ret['data']);
                    } else {
                        //var_dump($ret['message']);
                        if (empty($ret['message'])) {
                            $this->error('请稍后重试');
                        } else {
                            $this->error($ret['message']);
                        }

                    }


                } else {
                    $this->error(0, '', 'err');
                }


            }
        } else {
            $id = \think\facade\Request::get('pid');
            $token = getRand();
            session('payToken', $token);
//            var_dump($token . $money);
            View::assign('pid', $id);
            View::assign('skuId', $skuId);
            View::assign('money', $money);
            View::assign('sign', md5($token . $money));
            return View::fetch();
        }

    }

    function buyDetail($skuId = 1,$num=1)
    {

        if (\think\facade\Request::isPost()) {

            $sku = GoodsSkuModel::where(['id'=>$skuId])->select();

            $this->error('','',$sku);

        } else {

            $sku = GoodsSkuModel::getOne($skuId);
            $sku['num']=$num;

           $address= AddressModel::where(['uid'=>$this->uid,'isDefault'=>1])->find();
           $sku['areaInfo']=$address['info'];


            $area= AreaModel::getOne($address['areaId']);
            $sku['city']=$area['pid'];
            $city= AreaModel::getOne( $sku['city']);
            $sku['province']=$city['pid'];
            $sku['skuId']=$skuId;
            $sku['addressId']=$address['id'];


            View::assign('info', $sku);

            return view('buy_detail');
        }

    }
    function goBuy($skuId = 1,$num=1,$addressId=1)
    {

        if (\think\facade\Request::isPost()) {

            $sku = GoodsSkuModel::where(['id'=>$skuId])->find();

            $total=$sku['price']*$num;
            if ($total<=0){
                $this->error('金额不能小于0');
            }else{
                $this->success('ok',url('shop/buy',
                    ['skuId'=>$skuId,'addressId'=>$addressId]),'ok');
            }



        } else {

            $sku = GoodsSkuModel::getOne($skuId);
            $sku['num']=$num;
            View::assign('info', $sku);

            return view('buy_detail');
        }

    }
}