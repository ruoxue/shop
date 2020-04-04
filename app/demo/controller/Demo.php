<?php

// +----------------------------------------------------------------------
// | ThinkApiAdmin
// +----------------------------------------------------------------------

namespace app\demo\controller;


use app\BaseController;
use http\Env\Url;
use service\HttpService;
use think\Controller;

use think\Exception;
use think\facade\Db;
use think\facade\View;
use think\Request;


/**
 * 系统权限管理控制器
 * Class Pay
 * @package app\demo\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/07/10 18:13
 */
class Demo extends BaseController
{


    /**
     * 文件上传
     * @return \think\response\View
     * @throws \Exception
     */
    public function index()
    {


        if (!request()->isPost()) {
            $returnUrl = url('/demo/pay/notify')->build();


            $view = ['banks' => Db::name('Bank')->where(['is_deleted' => 0, 'status' => 1])->select(),
                'types' => Db::name("PayChannel")->where(['is_deleted' => 0, 'status' => 1])->select(),
                'returnUrl' => 'http://www.baidu.com',
                'extra' => ''];
            View::assign($view);
            return view();
        } else {
//{"bankCode":"ICBC","bankInfo":"1","userName":"1","phoneNum":"1","ID":"11"}
            $data = input();
            if (isset($data['bankCode']) && !empty($data['bankCode']))

                $data['bank'] = base64_encode(json_encode(['bankCode' => $data['bankCode']]));
            if (isset($data['extra']))
                $data['extra'] = ($data['extra']);
            $data['orderId'] = time();
            $data['orderDate'] = time();
            $data['ip'] = request()->ip();
            $data['merId'] = 'lemocms';

            if (strpos(request()->domain(), 'demo') !== false) {
                $preArr = array_merge($data, ['key' => 'vfZGc4bh5Wz38UI5Pgl6zgkTSETbQqRE']);
            } else {
                $preArr = array_merge($data, ['key' => 'WEmUZltnEfmN8cZKayQkX8S8ejzg1']);
            }

            ksort($preArr);
            $preArr = http_build_query($preArr);
            // exit(($preArr));
            $data['notifyUrl'] = url('/demo/pay/notify')->build();

            $data['returnUrl'] =url('/demo/pay/notify')->build();

            $data['mIp'] = request()->ip();
            $data['sign'] = md5(urldecode($preArr));
            $data['skuId'] = 1;
            dump(url('/api/v1.pay/index', [], false, request()->domain())->build() . '?' . http_build_query($data));
            var_dump(http_build_query($data));

            $ret = send_post(url('/api/v1.pay/index', [], false, request()->domain())->build()
                , $data);

            //  $retUrl = Url::build('/pay/pay/qrPay', '', '', Request::instance()->domain());
            $data = json_decode(html_entity_decode($ret), true)['data'];


            if ($data != null && isset($data['url'])) {
                // $this->redirect($data['url']);
            } else {
                $this->error(json_encode($data), '', [], 90);
            }


        }
    }


    public function edit()
    {
        return $this->_form('api_desc', 'edit');
    }

    public function notify()
    {
        echo 'success';
        exit;
    }


}
