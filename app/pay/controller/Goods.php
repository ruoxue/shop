<?php


namespace app\pay\controller;


use app\common\controller\Frontend;
use app\common\model\GoodsSku as SkuModel;
use app\common\model\User as UserModel;
use app\common\model\Oauth2Client as Oauth2ClientModel;

/**
 * Class Goods
 * @package app\pay\controller
 */
class Goods extends Frontend
{

    /**
     * 302跳转
     * 验证ip 风险ip禁止
     */
    public function sku($channelId,$skuId,$uid,$money,$num=1)
    {
        $sku = SkuModel::getOne($skuId);

        if ($sku == null) {
            echo 'null';
        } else {

            $data['orderId'] = time();
            $data['orderDate'] = time();
            $data['ip'] = request()->ip();

            $user= UserModel::getOne($uid);
            if ($user==null){
                return null;
            }
          $oauth2Client=  Oauth2ClientModel::where(['uid'=>$uid])->find();

            if ($money<0){
                $money=$sku['price']*$num;
            }
            if ($money<0){
                return;
            }
            $data['returnUrl']='';
            $data['notifyUrl']='';


            $data['money']=$money;
            $data['merId'] = $oauth2Client['appId'];
            $data['type'] = $channelId;
            $data['skuId']=$skuId;
            $data['num']=$num;
            $data['mIp'] = request()->ip();

            ksort($preArr);


            $preArr = http_build_query($preArr);
            // exit(($preArr));

            $data['sign'] = md5(urldecode($preArr));

            dump(url('/api/v1.pay/index',[],false,request()->domain())->build());
            var_dump(http_build_query($data));

            $ret = send_post(url('/api/v1.pay/index',[],false,request()->domain())->build()
                ,$data);




        }

    }

}