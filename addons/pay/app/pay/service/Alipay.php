<?php

namespace app\admin\service;


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

        $pub_key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxJ3tai5AgqRYjhyqkhOF+aHiE9b9dNYdp74ql1ZOv8BBv3NVPau6w3qkT9HOb5uJmz8m2Mt2lUteov7LKY00z54oKNwTkJ7d9FdB51lx8gME19Xs0HsrabwSyd8Ey5lyKWyaxfDvGaOgVYpTvV9cSzkEJ6T4ze/fjptkJq34JAcqbnMijjXkPrvz9bu5ZJFGHONlZTzta3BK8ut9cgWdspKKiqx8ykRFGpTJ/G9M3qq89VDYTB/oj8GKoHekNYhZPRyP0GJs+oy4TGus0wlqK49vB+0h0QreHLKHVnS25Tc7jG1sJhg9P7gz7pQDt79lAkeCPhjLRP4wSVkrh4HY4wIDAQAB";
        $ali_pub_key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmjmiqiBl6tjCqdeJMhzuV56ezB9X2wp23TJJnQfRkr8rd+tBf+YbEW5CwMlhij66ZYtQl+J+IF9FNxKM1eK4lwUoTdh5pnTmYVgXwkIJUa9JoR8fdEdH52HDpA1nIsyRKqjqYX7VSiP47/LA3H5Vt9CnXFJFAVK3FP/99JlE0MafmsX5hiIK+hNZCTlpj706luP0I+XB74kmo9P5VwnDhOnPr4K5JRU8TM14kw45pCildcAc5yMGhOikFxhah2qC9y/tXntkBe8oV2X5b2DhVY890xW90BZ2Vq+/w5XdelejIPg8xSISY9IAJNLj6jlFXIIVZSoq9/snTjabjL/9twIDAQAB";
        $pri_key = 'MIIEowIBAAKCAQEAxJ3tai5AgqRYjhyqkhOF+aHiE9b9dNYdp74ql1ZOv8BBv3NVPau6w3qkT9HOb5uJmz8m2Mt2lUteov7LKY00z54oKNwTkJ7d9FdB51lx8gME19Xs0HsrabwSyd8Ey5lyKWyaxfDvGaOgVYpTvV9cSzkEJ6T4ze/fjptkJq34JAcqbnMijjXkPrvz9bu5ZJFGHONlZTzta3BK8ut9cgWdspKKiqx8ykRFGpTJ/G9M3qq89VDYTB/oj8GKoHekNYhZPRyP0GJs+oy4TGus0wlqK49vB+0h0QreHLKHVnS25Tc7jG1sJhg9P7gz7pQDt79lAkeCPhjLRP4wSVkrh4HY4wIDAQABAoIBAFSo18B/l+Fo2ISgjUWGyIpbjRgT22DCjVRcCsTNMe1Y4DaKd6qtViazmSqRYX0nENJbF+QDf9T69g3i/iGvHo1DQXGOiZIdBd8e706pcenOKPiysVx3nJLTm5d2wv7T3Jc/n0kZ3CpDDVenzzTcacYuD77uRv+NiMpD2JTbFdyUxBYYBdWWtGIkP89yUMlBd1hVPRa3zJUVuWreczDqXBBThxsxCtEeXD4DHVPHNoUqf64K++FpeM/wkYGBEl/L86gBp8k73Oghtz+Oubm5XcRYDpxpQJuQzxvcK7OKwNHRMleh0CLOTUlqCjxrl+50IC9r+lm2g6VzYSw78WZywGkCgYEA4ptnYdN+/6TwfmO+eMc6eOjgm51/ULGUKO4Cjt79+C3WPNZ/N66IEQnER9jhZOq4GQokKks5335950oYH9qJ2EX4Vwu+92vyDU+87oqDvwHaynuwclTRgdS9j6cM4YEEGzyShT9Sdk8MKYyOYl6kKAyzJ5eCiAUNB5xQDAs06ZcCgYEA3h6vumaTo8rgiKISlmAMCNpNeS1Bmwx75xUtULzyNbgfXWHyoQz0l08Urj0F+FCwj91luXnS5pcpes2wyxTIo8ii1xU35HO37jBwIrIzLGBrfMNlPJmvkf+yWPKY2IQa/lbWy2PMLAWWwIGYAJD6zzGBy4kDh85zyeMaZjKNvJUCgYEA0M6MG7uMCbsNlBkK+TXzrlhAJ4SgPVX0hSaUGtxlv5tVDyhDf6aGYNTUFbRGNPyH+SK8InDA+i0PjOFci3WPkUcgR+1d4ZWJLDTujgv6zDXEStJgy2lWyEClD8rJdr2wb/yHstqffL7oIR0QUpqMvw68wt28fOPSltG1fPOSpS8CgYAvLZVloiP5xj2qx8Hq7rufpb6O3m3w4NXwXy8g1wEbJ+CBlSxTyMyq1sEtwQhjlb/qzrusiZOiJrlIvCew+tki8Jql1HvqqimHDgLDW9ZCtrgd5+K43GvONuFKwzxzwJt7Kja1PJ1BPG6otN30QNcE6x8GfDOjxNYTK4mP62zvgQKBgDJnoW04WIR6rRMNr/sNDNwurPIZCagpGQ/TP+gIQtIp98D5dk10vieQ+AIBxiuAq/XXEjOcHF3uIAkSuNojU7bHom/mMY/G81iX7k+wb3oXUlKoaibUN3gKhmoziUY1WARZ9Yf7VKaIqy3s34JJDvLsHSZVTrndUQVSm3FuQ965';
        $queue['mark']='L'.date("YmdHis", time()).$queue['mark'];
        $param['app_id'] = $queue['userId'];//'2018061360410257';
        $param['format'] = 'JSON';

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
