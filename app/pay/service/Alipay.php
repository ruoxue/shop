<?php

namespace app\pay\service;


use service\HttpService;
use Think\Db;
use think\Url;
use think\Request;

/**
 *支付宝手机支付
 * @package app\pay\service
 *
 *
 */
class Alipay extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);

        if ($queue == null) {
            $result['msg'] = '无可用帐号';
            return $result;
        }

        $domain=$queue['domain'];


        $param['method'] = 'alipay.trade.wap.pay';

        $param['return_url'] = 'http://baidu.com';
        $param['notify_url'] = Url::build('/alipaynotify', [], '', $domain.':521') ;

        $param['charset'] = 'utf-8';
        $param['sign_type'] = 'RSA2';
        $param['timestamp'] = date("Y-m-d H:i:s", time());
        $param['version'] = '1.0';

        $param['total_amount'] = $queue['tradeMoney'];
        $param['body'] = $queue['realNamed'];
        $param['subject'] = $queue['realNamed'];
        $param['out_trade_no'] = $queue['mark'];
        $param['product_code'] = 'QUICK_WAP_WAY';
        $param['goods_type'] = '0';
        $param['passback_params'] = '1';
        $param['quit_url'] = 'http://baidu.com';
        $param['timeout_express'] = '90m';
        $biz = array(

            'body' => $param['body'],
            'subject' => $param['subject'],
            'out_trade_no' => $param['out_trade_no'],
            'timeout_express' => $param['timeout_express'],
            'total_amount' => $param['total_amount'],
            'product_code' => $param['product_code'],
            'quit_url' => $param['quit_url'],
            'passback_params' => $param['passback_params'],
            'goods_type' => $param['goods_type']


        );





        unset($param['out_trade_no']);


        unset($param['body']);

        unset($param['subject']);
        unset($param['total_amount']);
        unset($param['out_trade_no']);
        unset($param['timeout_express']);
        unset($param['total_amount']);
        unset($param['quit_url']);
        unset($param['product_code']);
        unset($param['goods_type']);
        unset($param['passback_params']);

        $param['biz_content'] = json_encode($biz);

        ksort($param);

        $param['sign'] = rsaSign2($param, $queue['token']);

        $ret = HttpService::send_alipay_post(
            'https://openapi.alipay.com/gateway.do', $param);

        $mark=$queue['mark'];

        if ($queue['subtype'] > 2) {
            $queue['pay_url_android'] = Url::build('/pay/alipay/alipay',
                    [], '', $domain) . '/id/' . $queue['id'];
            $queue['pay_url'] = $queue['pay_url_android'];

            $url = $result['url'] = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];

            db('pay_queue')->where(['id' => $queue['id']])
                ->update(['pay_url_android' => $queue['pay_url_android'],
                    'pay_url' => $queue['pay_url'], 'status' => 10,
                    'info'=>$ret,
                    'mark' => $mark]);
//
            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];

            return $result;
        }else{
            $queue['pay_url_android'] =    $queue['pay_url_android'] = Url::build('/pay/alipay/alipay',
                    [], '', $domain) . '/id/' . $queue['id'];
            $queue['pay_url'] = $queue['pay_url_android'];

            $url = $result['url'] = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];

            db('pay_queue')->where(['id' => $queue['id']])->
            update(['pay_url_android' => $queue['pay_url_android'],
                'info'=>$ret,
                'pay_url' => $queue['pay_url'],
                'status' => 10, 'mark' => $mark]);

            if ($queue['subtype'] == 1) {
                $result['url'] = $queue['pay_url'];
            } else {
                $result['url'] =$url;
            }
           // $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];

            return $result;
        }


    }

}
