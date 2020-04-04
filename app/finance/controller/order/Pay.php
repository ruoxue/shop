<?php
/**
 * lemocms
 * ============================================================================
 * 版权所有 2018-2027 lemocms，并保留所有权利。
 * 网站地址: https://www.lemocms.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/9/4
 */

namespace app\finance\controller\order;

use app\common\controller\Backend;
use app\common\model\OrderPay;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;


class Pay extends Backend
{

    public function initialize()
    {
        parent::initialize();

    }


    /**
     ************************支付管理****************************
     */
    public function index()
    {

        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('order_pay')->alias('a')
                ->join('order', 'order.id=a.orderId', 'left')
                ->join('pay_channel channel', 'channel.id=a.channelId')
                ->join('pay_account account', 'account.id=a.aid')
                ->join('admin', 'admin.id=a.adminId')
                ->where(['admin.id'=>session('admin.id')])
                ->field('a.*,admin.username,channel.title,account.realNamed')
                ->order('a.id desc')
                ->cache(30)
                ->
                paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];
        }
        return view();

    }


    public function stastic()
    {
        if (Request::isPost()) {
            $group = Request::post('group', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;


            if (empty($group)) {
                $group = 1;
            }


            if ($group == 1) {
                $group = "account.realNamed";

            } else if ($group == 2) {
                $group = "user.username";
            } else if ($group == 3) {
                $group = "FROM_UNIXTIME(order.cTime,'%Y-%m-%d')";

            } else if ($group == 4) {
                $group = "FROM_UNIXTIME(order.cTime,'%Y-%m')";
            } else if ($group == 5) {

                $group = "FROM_UNIXTIME(order.cTime,'%Y')";
            } else if ($group == 6) {
                $group = "toAccount.userId";
            } else if ($group == 7) {
                $group = 'admin.username';
            } else if ($group == 8) {
                $group = "account.realNamed,FROM_UNIXTIME(order.cTime,'%Y-%m-%d')";
            } else if ($group == 9) {
                $group = "account.realNamed,FROM_UNIXTIME(order.cTime,'%Y-%m')";
            } else if ($group == 10) {
                $group = "toAccount.realNamed,FROM_UNIXTIME(order.cTime,'%Y-%m-%d')";
            } else if ($group == 11) {
                $group = "toAccount.realNamed,FROM_UNIXTIME(order.cTime,'%Y-%m')";
            }


            if ($group == 6) {

                $type = 'inner';
            } else {
                $type = 'left';
            }

            $list = Db::name('order')
                ->alias('order')
                ->join('pay_channel channel', 'order.channelId = channel.id')
                ->join('user user', 'order.uId = user.id')
                ->join('order_pay queue', 'order.id = queue.orderId', 'left')
                ->join('pay_account account', 'queue.userId = account.userId', 'left')
                ->join('pay_account toAccount', 'queue.toUserId = toAccount.userId', $type)
                ->join('goods_sku sku', 'sku.id = order.skuId', 'left')
                ->join('admin admin', 'admin.id = sku.adminId', 'left')
                ->field([
                    'sum(order.orderMoney)' => 'dayHaveMoney',
                    'sum(order.orderMoney)' => 'dayHadMoney',
                    "count(order.orderId)" => 'haveOrder',
                    "sum(if(order.status='1' ,1,0))" => 'hadOrder',
                    "sum(if(order.status='1' ,orderMoney,0))" => 'HadOrderMoney',
                    'count(queue.mark)' => 'qMark',
                    'count(queue.userId)' => 'accountId',
                    'count(account.realNamed)' => 'accountName',
                    'sum(order.channelMoney)' => 'channelMoney',
                    "concat(account.realNamed,'-',account.userId)"=>'realNamed',
                    "concat(toAccount.realNamed,'-',toAccount.userId)"=>'toAccountRealNamed',
                    $group => 'id'

                ])
                ->group($group)
                ->order('id desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();


            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];
        }
        return view();

    }

    public function delete()
    {
        $id = Request::post('id');
        if ($id) {
            OrderPay::destroy($id);
            $this->success(lang('delete success'));

        } else {
            $this->error('非法数据');
        }

    }

    public function twicePay($id)
    {

        $orderPay = OrderPay::getOne($id);


        $data['mark'] = $orderPay['mark'];

        $data['trade_status'] = 'SUCCESS';
        $data['orderId'] = $orderPay['orderId'];
        $data['no'] = $orderPay['mark'];

        ksort($data);

        $data['sign'] = md5(http_build_query($data));

        $ret = send_post("http://127.0.0.1:521/payNotify/" . $orderPay['mark'] . '/TwiceService', $data);
        $this->success(json_encode($ret), url('index'));
    }

    public function forcePay($id)
    {
        $orderPay = OrderPay::getOne($id);

        $data['mark'] = $orderPay['mark'];
        $data['trade_status'] = 'SUCCESS';
        $data['orderId'] = $orderPay['orderId'];
        $data['no'] = $orderPay['mark'];

        ksort($data);

        $data['sign'] = md5(http_build_query($data));

        $ret = send_post("http://127.0.0.1:521/payNotify/" . $orderPay['mark'] . '/ForceService', $data);
        $this->success(json_encode($ret), url('index'));

    }

    public function detail($id)
    {
        if (Request::isPost()) {

            $orderPay = OrderPay::getOne($id);
            $this->success('', '', $orderPay);
        } else {

            return view('detail');
        }

    }


}