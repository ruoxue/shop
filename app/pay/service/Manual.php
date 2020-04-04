<?php

namespace app\pay\service;


use app\code\model\pay\Queue;
use Think\Db;
use think\Url;
use think\Request;

/**
 * 人工充值
 * Class ZeJPay
 * @package app\pay\service
 *
 *
 */
class Manual  extends BasePay
{
    public function pay($queue)
    {parent::pay($queue);
        $mark = $queue['mark'];
        $money = $queue['tradeMoney'];
        $domain = $queue['domain'];
        $url = Url::build('/pay/manual', [], '', $domain). '?id='.$queue['id'];
        $queue['pay_url_android'] = $url;
        $queue['pay_url'] = $url;



        db('pay_queue')->where(['id' => $queue['id']])->update(['pay_url_android' => $queue['pay_url_android'], 'pay_url' => $queue['pay_url'], 'status' => 10, 'mark' => $mark]);

        $result['url'] = $url;
        $result['extra'] = $queue['realNamed'];
        return $result;
    }

}
