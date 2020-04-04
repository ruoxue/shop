<?php

namespace addons\pay\service;

use service\JpushServer;
use Think\Db;
use think\Url;
use think\Request;
use service\HttpService;

/**
 * 本地支付宝调用(java)
 *
 * Class LocalPay
 * @package app\pay\service
 */
class LocalPay extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);
        $domain = $queue['domain'];
        if ($queue['subtype'] > 2) {
            $ret = HttpService::get('http://127.0.0.1:5210/hhpay', ['uid' => $queue['uId'], 'queueId' => $queue['id']]);
            $param = json_decode($ret, true);
            $url = $param['url'];
            $queue['pay_url_android'] = $url;
            $queue['pay_url'] = $url;
            db('pay_queue')->where(['id' => $queue['id']])->update([
                'pay_url_android' => $queue['pay_url_android'],
                'pay_url' => $queue['pay_url'],
                'status' => 10,
                'mark' => $queue['mark']]);
            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];
            return $result;
        } else {//http://pay.wowotou01.com/pay/index?id=1020  %3Fid%3D9   3Fid%3D1024
            $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=2016101202129725&scope=auth_base&state='.$queue['id'].'&redirect_uri='
                . urlencode('http://mfypay.com/alipays');
            $queue['pay_url_android'] = $url;
            $queue['pay_url'] = $url;

            db('pay_queue')->where(['id' => $queue['id']])->update([
                'pay_url_android' => $queue['pay_url_android'],
                'pay_url' => $queue['pay_url'],
                'status' => 10,
                'mark' => $queue['mark']]);
            $payUrl = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];
            $result['url'] = $payUrl;
            $result['extra'] = $queue['realNamed'];
            return $result;

        }


    }
}

 