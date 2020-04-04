<?php

namespace addons\pay\service;

use service\JpushServer;
use service\NodeService;
use service\PayRetService;
use Think\Db;

/**
 * 代付提交
 * Class OutPay
 * @package app\pay\service
 */
class OutPay  extends BasePay
{
    public function pay($queue)
    {parent::pay($queue);
        $user = db('user')->where(array('id' => $queue['uId'], 'is_deleted'=>0,'status' => 'normal'))->find();
        if ($user == null) {
            $ret['msg'] = '用户不存在';
            return $ret;
        }
        if ($queue['tradeMoney'] <= 0) {
            $ret['msg'] = '金额不符合';
            return $ret;
        }
        $ret['msg'] = '代付已提交';
        return $ret;


    }
}