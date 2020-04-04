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

namespace app\mer\controller\order;

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






}