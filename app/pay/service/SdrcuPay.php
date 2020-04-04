<?php

namespace app\pay\service;


use service\HttpService;
use Think\Db;
use think\Url;
use think\Request;

/**
 *  山东
 * @package app\pay\service
 *
 *
 */
class SdrcuPay  extends BasePay
{
    public function pay($queue)
    {parent::pay($queue);

        $userId = $queue['userId'];

        $mark = $queue['mark'];

        $domain = $queue['domain'];


        $dom = HttpService::get($userId);


        $doms = new  \DOMDocument();
        $doms->loadHTML($dom);


        $list = $doms->getElementById("formid")->getElementsByTagName('input');

        $acceptBizNo = $doms->getElementById("formid")->getElementsByTagName('input')->item(1)->getAttribute("value");
        $merNo = $doms->getElementById("formid")->getElementsByTagName('input')->item(2)->getAttribute("value");


        if (isset($queue['subtype']) && $queue['subtype'] <= 2) {


            $url = $result['url'] = Url::build('/pay/index', [], '', $domain) . '?id=' . $queue['id'];;

            $queue['pay_url_android'] = Url::build('/pay/rcupay', [], '', $domain) . '?id=' . $queue['id'];
            $queue['pay_url'] = $queue['pay_url_android'];


            db('pay_queue')->where(['id' => $queue['id']])->update(['pay_url_android' => $queue['pay_url_android'],
                'pay_url' => $queue['pay_url'], 'status' => 10, 'mark' => $mark, 'info' => json_encode(['acceptBizNo' => $acceptBizNo, 'merNo' => $merNo]),]);
            if ($queue['subtype'] == 1) {
                $result['url'] = $url;
            } else {
                $result['url'] = $queue['pay_url'];
            }
            $result['subtype'] = $queue['realNamed'];

            return $result;

        } else {

            $url = $result['url'] = Url::build('/pay/wechat', [], '', $domain) . '?id=' . $queue['id'];;
            $queue['pay_url_android'] = Url::build('/pay/rcupay', [], '', $domain) . '?id=' . $queue['id'];;
            $queue['pay_url'] = $queue['pay_url_android'];

            db('pay_queue')->where(['id' => $queue['id']])->update(['pay_url_android' =>
                $queue['pay_url_android'], 'pay_url' => $queue['pay_url'], 'status' => 10, 'info' => json_encode(['acceptBizNo' => $acceptBizNo, 'merNo' => $merNo]),
                'mark' => $mark]);
            $queue['status'] = 10;

            $result['url'] = $url;
            $result['subtype'] = $queue['realNamed'];

            return $result;


        }

    }

}
