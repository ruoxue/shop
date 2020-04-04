<?php

namespace addons\pay\service;

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
    public function pay($args)
    {

        $order = db("pay_order");
        $user = db('user')->field('id')->where(array('id' => $args['merId'], 'status' => 'normal'))->find();

        if ($user == null) {
            return array("msg" => '商户不存在');

        }
        if ($order->where(array('OrderID' => $args['orderId'], 'uId' => $args['merId']))->count() == 0) {

            $channelRet = db('pay_merchannel')
                ->alias('m')
                ->join('pay_achannel ac', 'm.acId = ac.id')
                ->join('pay_subtype t', 'ac.subtype = t.id')
                ->where(array('m.uId' => $args['merId'],
                        'm.status' => 1,
                        'm.is_deleted' => 0,
                        'ac.status' => 1,
                        'ac.is_deleted' => 0,
                        't.status' => 1,
                        't.is_deleted' => 0,
                        't.type' => $args['subtype']
                    )
                )
                ->field(['m.id' => 'mId',
                    'm.outtype' => 'mOuttype',
                    'm.min' => 'min',
                    'm.max' => 'max',
                    'm.uId' => 'mUId',
                    'm.rate' => 'mRate',
                    'ac.id' => 'acId',
                    'ac.cId' => 'acId',
                    't.id' => 'subtype'
                ])->orderRaw('m.useTime/m.weight  asc,m.mTime asc')
                ->find();

            if ($channelRet == null) {

                return array("msg" => '通道未配置');
            }


            if (isset($channelRet['max']) && $channelRet['max'] != 0 && $channelRet['max'] < $args['orderMoney']) {

                return array("msg" => '订单金额超过通道限制');
            }
            if (isset($channelRet['min']) && $channelRet['min'] > $args['orderMoney']) {
                return array("msg" => '订单金额低于通道限制');

            }

            db('pay_merchannel')->where(['id' => $channelRet['mId']])->update(['mTime' => time()]);

            if (isset($args['notifyUrl'])) {
                $args['notifyUrl'] = urldecode($args['notifyUrl']);

            }
            if (isset($args['returnUrl'])) {
                $args['returnUrl'] = urldecode($args['returnUrl']);
            }


            $data['mIp'] = $args['mIp'];
            $data['orderId'] = $args['orderId'];
            $data['uId'] = $channelRet['mUId'];
            $data['orderDate'] = time();
            $data['tradeMoney'] = $args['orderMoney'];
            $data['ip'] = $args['ip'];
            $data['orderMoney'] = $args['orderMoney'];
            $data['returnUrl'] = $args['returnUrl'];
            $data['notifyUrl'] = $args['notifyUrl'];
            $data['merChannel'] = $channelRet['mId'];
            $data['acId'] = $channelRet['acId'];
            $data['cTime'] = time();
            $data['mTime'] = 0;
            $data['productName'] = $args['productName'];
            $data['subtype'] = $args['subtype'];


            if (isset($args['bank'])) {
                $data['bank'] = base64_decode($args['bank']);
            }

            if (!empty($args['extra'])) {
                $data['extra'] = $args['extra'];
            }

            $ret = $order->insertGetId($data);
            db('pay_merchannel')->where('id', $channelRet['mId'])->setInc('useTime');


            $data['id'] = $ret;

            $queue = getAliAccount($data);//寻找序列

            if ($queue == null) {
               return array("msg" => '无可用账号或已关闭支付通道');
            }

            if (!isset($queue['api'])) {
                 return array("msg" => '支付通道未开启');
            }
            $class = new ReflectionClass($queue['api']); //
            $instance = $class->newInstanceArgs();
            return $instance->pay($queue);

        } else {
            return array("msg" => '订单已存在');
        }
    }


}