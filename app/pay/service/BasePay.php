<?php

namespace app\pay\service;


use app\common\model\PayAccount;
use think\facade\Db;


/**
 * 是否策略支付 如果策略支付 降维
 * Class AliUnion
 * @package app\pay\service
 */
class BasePay
{
    public function pay($queue)
    {
        if ($queue['strategy'] == 1) {
           $account= PayAccount::where(['userId'=>$queue['userId']])->find();
            $queue = $this->resetMoney($queue, 0,$account['unit']);
            if ($queue == null) {
                return array("msg" => '可用账号不存在');
            }
            Db::name('order_pay')->where(['id'=>$queue['id']])->
            update(['tradeMoney'=>$queue['tradeMoney']]);
        }

    }


    function resetMoney($queue, $times = 0,$unit=5)
    {
        if ($times >= 10) {
            return null;
        }
        $ret = Db::name('order_pay')->where([
            'tradeMoney' => $queue['tradeMoney'],
            'userId'=>$queue['userId']
        ])
            ->where('cTime','>',time()-$unit*60)
            ->find();


        if ($ret == null) {
            return $queue;
        } else {
            $times++;
            $queue['tradeMoney'] = $queue['tradeMoney'] - 0.01;
            return $this->resetMoney($queue, $times);
        }
    }


}

 