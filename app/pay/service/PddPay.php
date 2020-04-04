<?php
/**
 * Created by IntelliJ IDEA.
 * User: fan
 * Date: 2019/9/23
 * Time: 上午 11:22
 */

namespace app\pay\service;


use service\PddService;
use think\Url;

/**
 * 拼多多
 * @package  addons/pay/service/AliIndividualPay
 */
class PddPay extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);
        $domain = $queue['domain'];

        $info = json_decode(($queue['info']));

        $pdd = new   PddService;
        $account = new \stdClass();
        $account->uid = $info->uid;
        $account->auth_info = json_encode(["uid" => $info->uid, "address_id" => $info->address_id, "accesstoken" => $info->accesstoken,]);
        $account->order_info = json_encode(["uid" => $info->uid, "address_id" => $info->address_id, "accesstoken" => $info->accesstoken,]);


        $goods = $pdd->getGoodsInfoByUrl($queue['userId'], $account);

        $goodsParams = new \stdClass();
        $goodsParams->sku_id = $goods->sku_id;
        $goodsParams->group_id = $goods->group_id;
        $goodsParams->goods_id = $goods->goods_id;
        $account->goods_id = $goods->goods_id;

        $ret = $pdd->createOrder($goodsParams, 1, $account);

        if (isset($ret->error_code) && !empty($ret->error_code)) {

            return array("msg" => '账号出错');
        }


        if ($queue['subtype'] > 2) {


            $ali = $pdd->prePay($ret->order_sn, $account, 2);
            $query = (array)$ali->query;

            if ($query['total_fee'] != $queue['tradeMoney']) {

                return array("msg" => '金额不符');
            }//mweb_url=https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id=wx29230806445566541d9d30e91504800732&package=276101975
//https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id=wx20161110163838f231619da20804912345&package=1037687096
//
//作者：站在大神的肩膀上看世界
//链接：https://www.jianshu.com/p/6b9acdd10de6
//来源：简书
//著作权归作者所有。商业转载请联系作者获得授权，非商业转载请注明出处。
            /**
            {"respData":{"thirdPayInfodata":[{"payMethod":"0","tradeType":"APP","payData":{"timeStamp":"1575211795",
             * "mchId":"1001","appId":"wx6f1a8464fa672b11","sign":"4F247229C90AD5010F89884FA6B8A9B5","partnerId":"1259268201","prepayId":"wx012249554857850c1e747f521863362300"
             * ,"payId":"1201151421674023609","nonceStr":"M0IB55D9ITZQLI8ZOEH5034FFZCA3W4F","packageSign":"Sign=WXPay"}}],"returnCode":"SUCCESS","returnMsg":"OK"},"respCode":"0"}
             */

            $wx = $pdd->getWeixinDeeplink($ali->mweb_url, $ret->order_sn);

            $url = $wx->deeplink;
            $queue['pay_url_android'] = $url;
            $queue['pay_url'] = $url;
            db('pay_queue')->where(['id' => $queue['id']])->update([
                'pay_url_android' => $queue['pay_url_android'],
                'pay_url' => $queue['pay_url'],
                'status' => 10,
                'tradeNo'=>$query['out_trade_no'],
                'info' => json_encode($account),
                'mark' => str_replace('订单编号','',$query['subject'])]);
            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];
            return $result;
        } else {//http://pay.wowotou01.com/pay/index?id=1020  %3Fid%3D9   3Fid%3D1024


            $ali = $pdd->prePay($ret->order_sn, $account, 1);



            $pdd->getStoreItems('586795062',$account);

            $query = (array)$ali->query;

            if ($query['total_fee'] != $queue['tradeMoney']) {

                return array("msg" => '金额不符');
            }

            $url = $ali->alipay_url;
            $queue['pay_url_android'] = $url;
            $queue['pay_url'] = $url;

            db('pay_queue')->where(['id' => $queue['id']])->update([
                'pay_url_android' => $queue['pay_url_android'],
                'pay_url' => $queue['pay_url'],
                'status' => 10,
                'tradeNo'=>$query['out_trade_no'],
                'info' => json_encode($account),
                'mark' => str_replace('订单编号','',$query['subject'])]);
            $payUrl = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];
            $result['url'] = $payUrl;
            $result['extra'] = $queue['realNamed'];
            return $result;

        }


    }


}