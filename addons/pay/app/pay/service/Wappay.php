<?php

namespace addons\pay\service;


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
class Wappay extends BasePay
{
    public function pay($queue)
    {
        parent::pay($queue);

        if ($queue == null) {
            $result['msg'] = '无可用帐号';
            return $result;
        }

          $queue['mark']='L'.date("YmdHis", time()).$queue['mark'];
        $param['info'] = $queue['realNamed'];//'2018061360410257';


        $mark=$queue['mark'];
        $domain=$queue['domain'];

        $queue['pay_url_android'] = Url::build('/pay/alipay/alipay',
                [], '', $domain) . '/id/' . $queue['id'];
        $queue['pay_url']=$queue['pay_url_android'];


       $goods= db('goods')->where(['status'=>1])->find();
       $desc='';
        if ($goods==null){
            $desc=$queue['realNamed'];
        }else{
             db('goods')->where(['id'=>$goods['id']])->update(['mTime'=>time()]);
            $desc=$goods['name'];

        }

        db('pay_queue')->where(['id' => $queue['id']])
            ->update(['pay_url_android' => $queue['pay_url_android'],
                'pay_url' => $queue['pay_url'], 'status' => 10,
                'queueChannel'=>$desc,
                'mark' => $mark]);



        $ret= HttpService::get("http://127.0.0.1:521/pay?queueId=".$queue['id']);

        $param = json_decode($ret, true);
        $info = $param['info'];



        db('pay_queue')->where(['id' => $queue['id']])
            ->update(['pay_url_android' => $queue['pay_url_android'],
                'pay_url' => $queue['pay_url'], 'status' => 10,
                'info'=>$info,
                'mark' => $mark]);

        if ($queue['subtype'] > 2) {

            $queue['pay_url'] = $queue['pay_url_android'];

            $url = $result['url'] = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];
            $result['url'] = $url;
            $result['extra'] = $queue['realNamed'];

            return $result;
        }else{
            $queue['pay_url_android'] =    $queue['pay_url_android'] = Url::build('/pay/alipay/alipay',
                    [], '', $domain) . '/id/' . $queue['id'];
            $queue['pay_url'] = $queue['pay_url_android'];

            $url = $result['url'] = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];



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
