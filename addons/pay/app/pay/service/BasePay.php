<?php

namespace app\admin\service;

use service\JpushServer;
use Think\Db;
use think\Url;
use think\Request;

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
            $queue = $this->resetMoney($queue, 0);
            if ($queue == null) {

                return array("msg" => '可用账号不存在');
            }
            db('pay_queue')->where(['id'=>$queue['id']])->where(['tradeMoney'=>$queue['tradeMoney']]);
        }

    }


    function resetMoney($queue, $times = 0)
    {
        if ($times >= 10) {
            return null;
        }
        $ret = db('pay_queue')->where(['tradeMoney' => $queue['tradeMoney'],'userId'=>$queue['userId'],'cTime'=>array('>',time()-5*60)])->find();
        if ($ret == null) {
            return $queue;
        } else {
            $times++;
            $queue['tradeMoney'] = $queue['tradeMoney'] - 0.01;
            return $this->resetMoney($queue, $times);
        }
    }


}

 