<?php

namespace app\pay\service;

use service\HttpService;
use Think\Db;
use think\Url;
use think\Request;

/**
 * 转转
 * Class SocketSelfLine
 * @package app\pay\service
 */
class ZhuanPay extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);
        $domain = $queue['domain'];


        $buyUser = db('pay_autoacc')->where(['type' => $queue['type'] + 1, 'status' => 1, 'is_deleted' => 0,
       //     'uId'=>array('neq',$queue['uId']),
//            'idCard' => array('neq', $queue['idCard'])
        ])->order('mTime asc')->find();
        if ($buyUser == null) {
            $result['msg'] = '可用买家账号空';
            return $result;
        }
        db('pay_autoacc')->where(['userId' => $buyUser['userId']])->update(['mTime' => time()]);


        db('pay_queue')->where(['id' => $queue['id']])->update(['isNeedSend' => 0, 'toUserId' => $buyUser['userId']]);
        $ret = HttpService::get('http://127.0.0.1:521/pay', ['queueId' => $queue['id']]);
        //    db('pay_queue')->where(['id' => $queue['id']])->update(['status' => 10]);
        $param = json_decode($ret, true);
        if (!isset($param['url'])){

            //    $result['url'] = $url;
            //pay/unionpay
            $result['msg'] = $param['msg'];
            return $result;
        }
        $url = $param['url'];
        $queue['pay_url_android'] = $url;
        $queue['pay_url'] = $url;

        db('pay_queue')->where(['id' => $queue['id']])->update([
            'pay_url_android' => $queue['pay_url_android'],
            'pay_url' => $queue['pay_url'],
            'status' => 10
        ]);
        if ($queue['subtype'] < 3) {
            if ($queue['subtype']==2){
                $info = json_decode($queue['info'], true);

                $query=$info['query'];
                $payUrl=         (urldecode($info['gateway_url'])."?".http_build_query($query));
            }else{
                $payUrl = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];
            }

        } else {
            $payUrl = Url::build('/pay/wechat', [], '', $domain) . '?id=' . $queue['id'];
        }


        $result['url'] = $payUrl;
        //    $result['url'] = $url;
        //pay/unionpay
        $result['extra'] = $queue['realNamed'];
        return $result;
    }


}
