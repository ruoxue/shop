<?php

namespace app\pay\service;

use service\JpushServer;
use Think\Db;
use think\Url;
use think\Request;

/**
 * 转卡
 * Class AliUnion
 * @package app\pay\service
 */
class AliUnion extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);
        $domain = $queue['domain'];


        $realName = $queue['realNamed'];


        $bankNum = $queue['bankNum'];

        $mark = $queue['mark'];
        $money = $queue['tradeMoney'];
        $bankCode = $queue['bankCode'];

        $bankName = $queue['bankName'];////

        $appurl = Url::build('/pay/pay/strategy2', '', '', Request::instance()->domain());
        $appurl = $appurl .  '?id=' . $queue['id'];
        $queue['info'] = $appurl;
        //	$appurl='https://www.alipay.com/?sourceId=bill&bankAccount='.$realName.'&amount='.$money.'&money='.$money.'&orderSource=from&bankMark='.$bankCode.'&bankName='.$bankName.'&cardChannel=HISTORY_CARD&cardNo='.$bankNum.'&cardIndex=&actionType=toCard&cardNoHidden=true&appId=09999988&from=pc';

        //$url='https://www.alipay.com/?sourceId=bill&bankAccount='.$realName.'&amount='.$money.'&money='.$money.'&orderSource=from&bankMark='.$bankCode.'&bankName='.$bankName.'&cardChannel=HISTORY_CARD&cardNo='.$bankNum.'&cardIndex=&actionType=toCard&cardNoHidden=true&appId=09999988&from=pc';

        $url =
            ("alipays://platformapi/startapp?appId=09999988&actionType=toCard&sourceId=bill&cardNo=$bankNum&bankAccount=" . urlencode($realName) . "&money=$money&amount=$money&bankMark=$bankCode&bankName=" . urlencode($bankName));

        $queue['pay_url'] = $url;

        $queue['pay_url_android'] = $appurl;



        db('pay_queue')->where(['id' => $queue['id']])->update([
            'pay_url_android' => $queue['pay_url_android'],
            'pay_url' => $queue['pay_url'],
            'status' => 10,
            'mark' => $queue['mark']
        ]);


        if ($queue['subtype'] = 1) {


            $retUrl = Url::build('/pay/pay/strategy', '', '', $domain);

            $url = $retUrl . '?id=' . $queue['id'];
            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];
            return $result;

        } else if ($queue['subtype'] == 2) {
            $url = $queue['pay_url'];
            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];
            return $result;
        }


    }
}

 