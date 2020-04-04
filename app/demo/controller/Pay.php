<?php

// +----------------------------------------------------------------------
// | ThinkApiAdmin
// +----------------------------------------------------------------------

namespace app\demo\controller;


use service\HttpService;
use service\JpushServer;
use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use think\Url;
use controller\BasicAdmin;


/**
 * 系统权限管理控制器
 * Class Pay
 * @package app\demo\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/07/10 18:13
 */
class Pay extends Controller
{

    public function index()
    {

        $pub_key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxJ3tai5AgqRYjhyqkhOF+aHiE9b9dNYdp74ql1ZOv8BBv3NVPau6w3qkT9HOb5uJmz8m2Mt2lUteov7LKY00z54oKNwTkJ7d9FdB51lx8gME19Xs0HsrabwSyd8Ey5lyKWyaxfDvGaOgVYpTvV9cSzkEJ6T4ze/fjptkJq34JAcqbnMijjXkPrvz9bu5ZJFGHONlZTzta3BK8ut9cgWdspKKiqx8ykRFGpTJ/G9M3qq89VDYTB/oj8GKoHekNYhZPRyP0GJs+oy4TGus0wlqK49vB+0h0QreHLKHVnS25Tc7jG1sJhg9P7gz7pQDt79lAkeCPhjLRP4wSVkrh4HY4wIDAQAB";
        $ali_pub_key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmjmiqiBl6tjCqdeJMhzuV56ezB9X2wp23TJJnQfRkr8rd+tBf+YbEW5CwMlhij66ZYtQl+J+IF9FNxKM1eK4lwUoTdh5pnTmYVgXwkIJUa9JoR8fdEdH52HDpA1nIsyRKqjqYX7VSiP47/LA3H5Vt9CnXFJFAVK3FP/99JlE0MafmsX5hiIK+hNZCTlpj706luP0I+XB74kmo9P5VwnDhOnPr4K5JRU8TM14kw45pCildcAc5yMGhOikFxhah2qC9y/tXntkBe8oV2X5b2DhVY890xW90BZ2Vq+/w5XdelejIPg8xSISY9IAJNLj6jlFXIIVZSoq9/snTjabjL/9twIDAQAB";
        $pri_key = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC6dDlUi/dAgspd8NttuW+XW7tHY7ej96+2ez7LExWQXIp3KhAKL18EkS9von3neE071ql+8vABPabsP1GzMq78WogTlVUbxjwXQImS6cV8eG6El6feY6oP5P8LRqdaC4yjTnPLwmp+cqXR88t5NhfVnRQQ2FiawPKo2gcES/bXH7WEj4bJ9ALBb+s7IvLeIrcxpJZDBqXwAOzFq5fQCigOmTFAT+IBY9BXeSflEeshJOCfLIVDGfTM90iZwAS/Qk4tDbyEpozTv4WsMC7anj91pBbN/UIx7NEu0GUFnhirCBe9eLtFyDi206oZM87nXW5DtE5bR7VD/fZNJ9o7g4dLAgMBAAECggEACdLn1mGNhHOfr/Vpyk0z6PinVGqXmWcKdgXmWBLIVxPZI9AuiirWycnQgwQ/t/tprlZwFcU9CgOykM4BQvzPpXIrq0R5+H2oWt1Golv40McTo57N9HSx2CaLnY96b88d+NjAVyMGM2VdAUPkG53TAalGDmEnwfeakXTzW7GY7tqgqOwYdj5nvMHfn4kS2WSlpEKln4opVTFXAZGxZiZM77rpvCvtxD/6gme2fA3mjjKctbpJZj8C7l2TI4PXShfTIeVqGxT/ItO3g7V31qNwWGAWsFEsrx3+Pqs5oh6UTD6krrTfq0tKOkiQjwuk0vfjEYaYWMnvr+hR+zU3LSm2uQKBgQD2LnzKnwPVHyeFeflcgnJZzNp7nUCmKstE0J7sbLhwEielwY4/roszKQf1PbAjJ+q0IthwxbvY2FiFzMRo0JGwVohjI8ESEfdXV9NFcDVzfH+a5eX4i9aH+bYXiAZxbi1OtNsHT8othoRDGyLvUEcPBpTYOzQ6g6uPpBbG1eHjPwKBgQDB4+sK4KlNplCsOtiZgvIMg/r4TsA5y2MpjtW8OqO1nIUCQjXQRrTSrlCC0JMJuh99pF0Klv0ydzbuBEqnuawmytcypDTdmLhdOkR6yxC4tgPbvM3j+72p6LSP95vGKp72An7VYoi30Inow3tFjy0OP74gioSUVzFq69nQbAP09QKBgQCefxuVEH/VLPOy+e7T4qBgtIVN5NaEpTStn2tSaETu0qF4FH/S/DteuuIGwKqwV7jCNVUIdiYU3GVR753cpbvGvk+dFJ3vVmXadA7vu+iN6+/z+GPxe4apkNntmIQdb4P4EnSZ5oMSgHKTCduaHCNDx0b5WFSt/6vrFQgdCAMg3QKBgEsniETYHmftzplj2e9vFVmKku5KwDHmx1Ilfm2OoURVHi1o8qj/rzl2vdhm9oevsGMoIbRoIE6+bPlHipSG4NbMiyujAKbgep4QZtjd+2ZNjyNOhNQZEURZ4htn0+a1QJaFExqtLedqGvqxQwgMXl+Gj7DXYFqLjakvMGecZtA9AoGBANBIYfA7y0hXE8FcJCCNPnGm9vBJjgFR80YwvI6qt1RYf/ziWIqxcAcMVljFGECr890YM1gLF5J9g3G4ozg/hxfIyMCssepeZXfX9gv0/b4CvKJm377ebvT3ZOAosxbfmZmLMG3gY4mzJudH3odQqhwNQoUbqubp3Ni5knRY2Pgp';

        $param['app_id'] = '2017062807593271';
        $param['format'] = 'JSON';

        $param['method'] = 'alipay.trade.wap.pay';

        $param['return_url'] = 'http://baidu.com';
        $param['charset'] = 'utf-8';
        $param['sign_type'] = 'RSA2';
        $param['timestamp'] = date("Y-m-d H:i:s", time());
        $param['version'] = '1.0';
        $param['notify_url'] = 'http://baidu.com';
        $param['total_amount'] = '1';
        $param['body'] = 'test';
        $param['subject'] = 'test1';
        $param['out_trade_no'] = 'test' . time();
        $param['product_code'] = 'QUICK_WAP_WAY';
        $param['goods_type'] = '0';
        $param['passback_params'] = '1';
        $param['quit_url'] = 'http://baidu.com';
        $param['timeout_express'] = '90m';
        $biz = array(
            'enable_pay_channels'=>'balance,moneyFund,bankPay',
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

        /**
         * app_id=2018061360410257&biz_content={"body":"test","subject":"test1","out_trade_no":"test1581425208","timeout_express":"90m","total_amount":"1","product_code":"QUICK_WAP_WAY","quit_url":"http:\/\/baiducom","passback_params":"1","goods_type":"0"}&body=test&charset=utf-8&format=JSON&goods_type=0&method=alipay.trade.wap.pay¬ify_url=&out_trade_no=test1581425208&passback_params=1&product_code=QUICK_WAP_WAY&quit_url=http://baiducom&return_url=http://baidu.com&sign_type=RSA2&subject=test1&timeout_express=90m×tamp=2020-02-11 20:46:48&total_amount=1&version=1.0
         */
        /**
         * app_id=2018061360410257&biz_content={"body":"test","subject":"test1","out_trade_no":"test1581424910","timeout_express":"90m","total_amount":"1","product_code":"QUICK_WAP_WAY","quit_url":"http:\/\/baiducom","passback_params":"1","goods_type":"0"}&body=test&charset=utf-8&format=JSON&goods_type=0&method=alipay.trade.wap.pay¬ify_url=&out_trade_no=test1581424910&passback_params=1&product_code=QUICK_WAP_WAY&quit_url=http://baiducom&return_url=http://baidu.com&sign_type=RSA2&subject=test1&timeout_express=90m×tamp=2020-02-11 20:41:50&total_amount=1&version=1.0
         */



        $param['biz_content'] = json_encode($biz);

        ksort($param);

        $param['sign'] = rsaSign2($param, $pri_key);

        $ret = HttpService::send_alipay_post(
            'https://openapi.alipay.com/gateway.do', $param);
var_dump($ret);
       // $ret = iconv('gbk', 'utf-8', $ret);
        echo($ret);

    }

    //MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAh1HvNLpz2MEa6Sqqu4s1ncmONhPJlZsBrQnx+U4gNc4YpgOIp3gRqlGL4yTGo68Spa5TIiqFN32lpiGxa89v51Xzpvj/ZTT6ktAa/dY9S3LLUYJmLGj6gwMKIesKmsa5uCBPBP10PNHbku0OPfzlcPfAzij8GrDidpdrhlP0aPvYi5ASG+uG7avYAthW+Llr/bwW0JVVeK1JedUVRTlk3CzQhVhb+otNrN/0jEy8KLTaj5Q2dZDgAEiH3VlGhju+FpaSAjstVSgQfoYvf+KB9YrfMtKs3UkdAynGHrtrQdr1MSgNhk8X72gDJsLK1/x05WsAg3l9/Bm/09BG9ZPOuQIDAQAB


//MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjuI0nIgQ2dDlnNTjgOLcexIx0pGBEFYK5OKTx4Wh6k5VgmQm5TPk8V1PRZxOIlTGJHdlZggLeNRRfoLPpMmMidFphJlQPy+cDGQMEVH/UUot1fDK9XMhIT3A71QmQn2z7qdLQ2EnsZKFGyrb1t9mcAVOolRuaWTbSqKsaWRMxIRD083FmkXqsJoc78L8ulNvw5uE7fpt0qhH1XbVf7dWVc/fM//iU7QemHho/ER5/Q9uHj/RDBQB0kYiWIa4D82iVUlFPuiNMMwII7/rcEJYJEtA99Ef0kRqGVm9uuV+zahbf9PleH9AID6SVEv289zyrHGI4dvN9G6za/VQd3JzLwIDAQAB
    public function notify()
    {
        echo 'success';
        exit;
    }


}
