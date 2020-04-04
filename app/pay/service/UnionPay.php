<?php


namespace app\pay\service;


/**
 * 线下转账
 * @package  addons/pay/service/AliIndividualPay
 */
class UnionPay extends  BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);
        $result['url'] = url('/pay/index/union')
            ->vars(['id' => $queue['id']])
            ->domain(true)->build();
        \think\facade\Db::name('order_pay')->where(['id' => $queue['id']])
            ->update(['status'=>8,'pay_url'=>$result['url']]);
        $result['extra'] = $queue['realNamed'];
        return $result;

    }
}