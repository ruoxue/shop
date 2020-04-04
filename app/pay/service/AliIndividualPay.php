<?php

namespace app\pay\service;


use Think\Db;

use think\Request;

/**
 * 个人
 * @package  addons/pay/service/AliIndividualPay
 */
  class AliIndividualPay extends BasePay
{
    public function pay($queue)
    {
      parent::pay($queue);

        if (isset($queue['extra']) && ($queue['extra'] == 'qrcode')) {

            $retUrl = url('/pay/index', '', '', Request::instance()->domain())->build();

            $url = $retUrl . '?id=' . $queue['id'];

            db('pay_queue')->where(['id' => $queue['id']])->update([
                'pay_url_android' => $queue['userId'],
                'pay_url' =>$queue['userId'],
                'status' => 10,
                'mark' => $queue['mark']]);

            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];
            return $result;
        } else {


            $retUrl =url('/pay/index', '', '', Request::instance()->domain())->build();

            $url = $retUrl .  '?id=' . $queue['id'];
            $result['url'] = $url;


            db('pay_queue')->where(['id' => $queue['id']])->update([
                'pay_url_android' => $queue['userId'],
                'pay_url' =>$queue['userId'],
                'status' => 10,
                'mark' => $queue['mark']]);


            $result['extra'] = $queue['realNamed'];
            return $result;

        }

       

        }
    }

 