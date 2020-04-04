<?php

namespace app\pay\service;


use Think\Db;
use think\Url;
use think\Request;

/**
 * Class QrPayStatic  转账静态码
 * @package app\pay\service
 *
 *
 */
class AliWap extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);
        $param['app_id'] = $queue['userId'];//'2019032163608779';
        $param['method']='alipay.trade.wap.pay';
        $param['return_url'] = '';

        $param['timestamp'] = date("Y-m-d H:i:s", time());

        $param['out_trade_no'] = $queue['mark'];
        $param['total_amount'] = $queue['tradeMoney'];

        $domain = $queue['domain'];
        $param['notify_url'] = Url::build('/pay/notify', ['id' => $queue['id']], '', $domain);


        db('pay_queue')->where(['id' => $queue['id']])->update(['pay_url' => $queue['rsaPri'],
            'status' => 10, 'info' => json_encode($param)]);


        $url = Url::build('/pay/alipay/payAlipay', ['id' => $queue['id']], '', $domain);

        $result['url'] = $url;

        $result['extra'] = '1';
        return $result;
    }

}
