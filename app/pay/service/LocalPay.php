<?php

namespace app\pay\service;

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

        if ($queue == null) {
            $result['msg'] = '无可用帐号';
            return $result;
        }
        $queue['pay_url_android'] = url('/pay/index/wap',
            ['id' => $queue['id']], '', true)->build();
        $queue['pay_url'] = $queue['pay_url_android'];
        $ret = send_post("http://127.0.0.1:521/pay", ['id' => $queue['id']]);

        $result['url'] = url('/pay/index/pay')
            ->vars(['id' => $queue['id']])
            ->domain(true)->build();

        $result['extra'] = $queue['realNamed'];
        return $result;
    }
}

 