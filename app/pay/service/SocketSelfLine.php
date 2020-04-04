<?php

namespace app\pay\service;

use service\HttpService;
use Think\Db;
use think\Url;
use think\Request;

/**
 * SOCKET链接
 * Class SocketSelfLine
 * @package app\pay\service
 */
class SocketSelfLine extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);

        db('pay_queue')->where(['id' => $queue['id']])->update(['isNeedSend' => 0]);
        $ret = HttpService::get('http://127.0.0.1:521/pay', ['queueId' => $queue['id']]);
    //    db('pay_queue')->where(['id' => $queue['id']])->update(['status' => 10]);
        $param = json_decode($ret, true);
        $url = $param['url'];
        $result['url'] = url('/pay/unionpay','','',request()->domain()).'?id='.$queue['id'];
        //pay/unionpay
        $result['extra'] = $queue['realNamed'];
        return $result;
    }


}
