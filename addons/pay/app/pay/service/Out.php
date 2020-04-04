<?php

namespace addons\pay\service;

use Think\Db;
use app\util\ApiLog;
use service\PayRetService;
use think\Exception;

/**
 * 代付
 * Class Out
 * @package app\pay\service
 *
 */
class Out  extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);
        $order = db("pay_outorder");

        if ($order->where(array('OrderID' => $queue['orderId'], 'uId' => $queue['merId']))->select() == null) {

            $channelRet = Db::table('pay_merchannel')
                ->alias('m')
                ->join('pay_achannel ac', 'm.acId = ac.id')
                ->join('pay_channel c', 'ac.cId = c.id')
                ->join('pay_type t', 'c.type = t.id')
                ->where(array('m.uId' => $queue['merId'],
                        'm.status' => 1,
                        'm.is_deleted' => 0,
                        'ac.status' => 1,
                        'ac.is_deleted' => 0,
                        'c.status' => 1,
                        'c.is_deleted' => 0,
                        't.status' => 1,
                        't.is_deleted' => 0,
                        't.type' => $args['type']
                    )
                )
                ->field(['m.id' => 'mId',

                    'm.outtype' => 'mOuttype',
                    'm.min' => 'min',
                    'm.max' => 'max',
                    'm.uId' => 'mUId',

                    'm.naturl' => 'mNaturl',
                    'm.descript' => 'mDescript',
                    'm.create_time' => 'mCreateTime',
                    'm.update_time' => 'mUpdateTime',
                    'm.rate' => 'mRate',
                    'ac.id' => 'acId',
                    'c.name' => 'cName',
                    'c.channelRate' => 'channelRate',
                    'c.api' => 'api',
                    'ac.cId' => 'cId',
                    'c.appId' => 'appId',
                    'c.url' => 'url',
                    'c.param' => 'param',
                    'c.notify_url' => 'cNotifyUrl'

                ])->orderRaw('m.useTime/m.weight  asc,m.update_time asc')
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
            $user = db('system_user')->where(array('id' => $args['merId'], 'status' => 1, 'is_deleted' => 0))->find();

            if ($user == null) {
                return array("msg" => '商户不存在');

            }


            Db::startTrans();

            try {
                $moneying = db('pay_outorder')->where(['uId' => $args['merId']])->where('status', '<', 2)->sum('tradeMoney');
                $amount = db('pay_order')->where(['uId' => $args['merId']])->where('status', 1)->sum('merMoney');
                $canUseMoney = $amount - $user['nominee'] - $moneying;
                if ($canUseMoney < $args['orderMoney']) {
                    return array("msg" => '可提现余额不足');
                }


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

                $data['channelId'] = $channelRet['cId'];
                $data['returnUrl'] = $args['returnUrl'];
                $data['notifyUrl'] = $args['notifyUrl'];
                $data['merChannel'] = $channelRet['mId'];
                $data['acId'] = $channelRet['acId'];
                $data['cTime'] = time();
                $data['mTime'] = 0;
                $data['productName'] = $args['productName'];


                if (isset($args['bank'])) {
                    $data['bank'] = base64_decode($args['bank']);
                }


                if (!empty($args['extra'])) {
                    $data['extra'] = base64_decode($args['extra']);
                }
                $data['type'] = $args['type'];
                $ret = $order->insertGetId($data);

                Db::table('pay_merchannel')->where('id', $channelRet['mId'])->setInc('useTime');
                Db::commit();
            } catch (\Exception $exception) {
                Db::rollback();
                return array("msg" => '提现失败');
            }

            $data['id'] = $ret;
            $class = new \ReflectionClass($channelRet['api']); //
            $instance = $class->newInstanceArgs();

            return $instance->pay($data, $channelRet);

        } else {
            return array("msg" => '订单已存在');
        }
    }


}