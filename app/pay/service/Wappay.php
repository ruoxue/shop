<?php

namespace app\pay\service;


use service\HttpService;
use Think\Db;
use think\Url;
use think\Request;

/**
 *支付宝手机支付2
 * @package app\pay\service
 *
 *
 */
class Wappay extends BasePay
{
    /**
     * @param $queue
     * @return array|mixed
     * @throws \think\db\exception\DbException
     *
     *
     *
     * $pub_key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxJ3tai5AgqRYjhyqkhOF+aHiE9b9dNYdp74ql1ZOv8BBv3NVPau6w3qkT9HOb5uJmz8m2Mt2lUteov7LKY00z54oKNwTkJ7d9FdB51lx8gME19Xs0HsrabwSyd8Ey5lyKWyaxfDvGaOgVYpTvV9cSzkEJ6T4ze/fjptkJq34JAcqbnMijjXkPrvz9bu5ZJFGHONlZTzta3BK8ut9cgWdspKKiqx8ykRFGpTJ/G9M3qq89VDYTB/oj8GKoHekNYhZPRyP0GJs+oy4TGus0wlqK49vB+0h0QreHLKHVnS25Tc7jG1sJhg9P7gz7pQDt79lAkeCPhjLRP4wSVkrh4HY4wIDAQAB";
     * $ali_pub_key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmjmiqiBl6tjCqdeJMhzuV56ezB9X2wp23TJJnQfRkr8rd+tBf+YbEW5CwMlhij66ZYtQl+J+IF9FNxKM1eK4lwUoTdh5pnTmYVgXwkIJUa9JoR8fdEdH52HDpA1nIsyRKqjqYX7VSiP47/LA3H5Vt9CnXFJFAVK3FP/99JlE0MafmsX5hiIK+hNZCTlpj706luP0I+XB74kmo9P5VwnDhOnPr4K5JRU8TM14kw45pCildcAc5yMGhOikFxhah2qC9y/tXntkBe8oV2X5b2DhVY890xW90BZ2Vq+/w5XdelejIPg8xSISY9IAJNLj6jlFXIIVZSoq9/snTjabjL/9twIDAQAB";
     * $pri_key = 'MIIEowIBAAKCAQEAxJ3tai5AgqRYjhyqkhOF+aHiE9b9dNYdp74ql1ZOv8BBv3NVPau6w3qkT9HOb5uJmz8m2Mt2lUteov7LKY00z54oKNwTkJ7d9FdB51lx8gME19Xs0HsrabwSyd8Ey5lyKWyaxfDvGaOgVYpTvV9cSzkEJ6T4ze/fjptkJq34JAcqbnMijjXkPrvz9bu5ZJFGHONlZTzta3BK8ut9cgWdspKKiqx8ykRFGpTJ/G9M3qq89VDYTB/oj8GKoHekNYhZPRyP0GJs+oy4TGus0wlqK49vB+0h0QreHLKHVnS25Tc7jG1sJhg9P7gz7pQDt79lAkeCPhjLRP4wSVkrh4HY4wIDAQABAoIBAFSo18B/l+Fo2ISgjUWGyIpbjRgT22DCjVRcCsTNMe1Y4DaKd6qtViazmSqRYX0nENJbF+QDf9T69g3i/iGvHo1DQXGOiZIdBd8e706pcenOKPiysVx3nJLTm5d2wv7T3Jc/n0kZ3CpDDVenzzTcacYuD77uRv+NiMpD2JTbFdyUxBYYBdWWtGIkP89yUMlBd1hVPRa3zJUVuWreczDqXBBThxsxCtEeXD4DHVPHNoUqf64K++FpeM/wkYGBEl/L86gBp8k73Oghtz+Oubm5XcRYDpxpQJuQzxvcK7OKwNHRMleh0CLOTUlqCjxrl+50IC9r+lm2g6VzYSw78WZywGkCgYEA4ptnYdN+/6TwfmO+eMc6eOjgm51/ULGUKO4Cjt79+C3WPNZ/N66IEQnER9jhZOq4GQokKks5335950oYH9qJ2EX4Vwu+92vyDU+87oqDvwHaynuwclTRgdS9j6cM4YEEGzyShT9Sdk8MKYyOYl6kKAyzJ5eCiAUNB5xQDAs06ZcCgYEA3h6vumaTo8rgiKISlmAMCNpNeS1Bmwx75xUtULzyNbgfXWHyoQz0l08Urj0F+FCwj91luXnS5pcpes2wyxTIo8ii1xU35HO37jBwIrIzLGBrfMNlPJmvkf+yWPKY2IQa/lbWy2PMLAWWwIGYAJD6zzGBy4kDh85zyeMaZjKNvJUCgYEA0M6MG7uMCbsNlBkK+TXzrlhAJ4SgPVX0hSaUGtxlv5tVDyhDf6aGYNTUFbRGNPyH+SK8InDA+i0PjOFci3WPkUcgR+1d4ZWJLDTujgv6zDXEStJgy2lWyEClD8rJdr2wb/yHstqffL7oIR0QUpqMvw68wt28fOPSltG1fPOSpS8CgYAvLZVloiP5xj2qx8Hq7rufpb6O3m3w4NXwXy8g1wEbJ+CBlSxTyMyq1sEtwQhjlb/qzrusiZOiJrlIvCew+tki8Jql1HvqqimHDgLDW9ZCtrgd5+K43GvONuFKwzxzwJt7Kja1PJ1BPG6otN30QNcE6x8GfDOjxNYTK4mP62zvgQKBgDJnoW04WIR6rRMNr/sNDNwurPIZCagpGQ/TP+gIQtIp98D5dk10vieQ+AIBxiuAq/XXEjOcHF3uIAkSuNojU7bHom/mMY/G81iX7k+wb3oXUlKoaibUN3gKhmoziUY1WARZ9Yf7VKaIqy3s34JJDvLsHSZVTrndUQVSm3FuQ965';
     * $queue['mark']='L'.date("YmdHis", time()).$queue['mark'];
     * $param['app_id'] = $queue['userId'];//'2018061360410257';
     *
     */
    public function pay($queue)
    {
        parent::pay($queue);

        if ($queue == null) {
            $result['msg'] = '无可用帐号';
            return $result;
        }
        $queue['pay_url_android'] = url('/pay/index/wap',
            ['id' => $queue['id']], '', true)->build();
        $queue['pay_url'] = $queue['pay_url_android'];


        $ret = send_post("http://127.0.0.1:521/pay", ['id' => $queue['id']]);
        $ret = json_decode($ret, true);

        $result['url'] = url('/pay/index/wap')
            ->vars(['id' => $queue['id']])
            ->domain(true)->build();
        \think\facade\Db::name('order_pay')->where(['id' => $queue['id']])
            ->update(['info'=>$ret['info'],'pay_url'=>$result['url']]);
        $result['extra'] = $queue['realNamed'];
        return $result;
    }

}
