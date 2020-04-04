<?php

namespace app\pay\service;

use ReflectionClass;
use service\PayService;
use Think\Db;
use app\util\ApiLog;

/**
 * 支付 (禁用)
 * Class Pay
 * @package app\pay\service
 */
class Pay
{
    /**
     * 通过提交参数 查询 子类型支持的账号 通过账号找api反射接口类
     * @param $args
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    public function pay($order)
    {
            $queue = getPayAccount($order);//寻找序列

            if ($queue == null) {
               return array("msg" => '无可用账号或已关闭支付通道');
            }

            if (!isset($queue['api'])) {
                 return array("msg" => '支付通道api未开启');
            }


            return array('url'=> url('pay/index/index')->vars(['payId'=>$queue['id']])
                ->domain(true)->build()
            ,'extra'=>'test');


    }


}