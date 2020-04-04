<?php

namespace addons\pay\service;


use app\code\model\pay\Queue;
use Think\Db;
use think\Url;
use think\Request;

/**
 *   浙江农商银行
 * @package app\pay\service
 *
 *
 */
class ZeJPay extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);
        if ($queue == null) {
            $result['msg'] = '无可用帐号';
            return $result;
        }

        if (strripos($queue['userId'], "=")) {
            $userId = substr($queue['userId'], strripos($queue['userId'], '=') + 1);
        } else {
            $userId = substr($queue['userId'], strripos($queue['userId'], '/') + 1);
        }


        $mark = $queue['mark'];
        $money = $queue['tradeMoney'];
        $domain = $queue['domain'];


        if (isset($queue['subtype']) && $queue['subtype'] <= 2) {
            $queue['pay_url_android'] = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=2016122004451491&scope=auth_base&redirect_uri=https://epay.zj96596.com/payWeb/pay/alipay/getAlipayUserIdAndCreatOrder1.do?totalAmount=' . $money . '&qrCode=' . $userId . '&remark=' . urlencode($mark);
            $queue['pay_url'] = $queue['pay_url_android'];

            $url = $result['url'] = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];

            db('pay_queue')->where(['id' => $queue['id']])->update(['pay_url_android' => $queue['pay_url_android'], 'pay_url' => $queue['pay_url'], 'status' => 10, 'mark' => $mark]);
//            if ($queue['subtype'] == 1) {
//                $result['url'] = $url;
//            } else {
//                $result['url'] = $queue['pay_url'];
//            }
            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];

            return $result;

        } else {
            $queue['pay_url_android'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxbfa7e9adff4131d8&redirect_uri=https://epay.zj96596.com/payWeb/pay/wx/getAccessTokenByCodeAndCreateWxOrder1.do?qrCode=' . $userId . 'totalAmount=' . $money .
                'remark=' . urlencode($mark) . '&response_type=code&scope=snsapi_base&state=' . urlencode($mark) . '&connect_redirect=1#wechat_redirect';
            $queue['pay_url'] = $queue['pay_url_android'];
            $url = Url::build('/pay/wechat', ['id' => $queue['id']], '', $domain) . '?id=' . $queue['id'];
            db('pay_queue')->where(['id' => $queue['id']])->update(['pay_url_android' => $queue['pay_url_android'], 'pay_url' => $queue['pay_url'], 'status' => 10, 'mark' => $mark]);

            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];
            return $result;


        }

    }

}
