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

namespace app\admin\controller\order;

use app\common\controller\Backend;
use app\common\model\GoodsSkutype;
use app\common\model\OrderPay as OrderPayModel;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use app\common\model\Order as OrderModel;
use  app\common\model\PostOrder as PostOrderModel;

class Order extends Backend
{

    public function index()
    {

        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');

            $id = Request::post('id', '', 'trim');
            $orderId = Request::post('orderId', '', 'trim');

            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('order')->alias('a')
                ->join('pay_channel channel', 'channel.id=a.type', 'left')
                ->join('user', 'user.id=a.uid', 'left')
                ->join('goods_sku sku', 'sku.id=a.skuId', 'left')
                ->join('admin', 'admin.id=sku.adminId', 'left')
                ->join('post_order postOrder', 'postOrder.orderId=a.id', 'left')
                ->join('post post', 'post.id=postOrder.post_id', 'left')
                ->order('a.id desc')
                ->field('a.*,a.mTime,sku.title as skuTitle,channel.title,
                postOrder.post_num,post.name postName,
                
                user.nickname,admin.username adminName')
                ->where('a.id', 'like', '%' . $id . '%')
                ->where('a.orderId', 'like', '%' . $orderId . '%')
                ->order('a.id desc')
                ->cache(30)
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];
        }
        return view();

    }

    /**
     * 发货
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function send()
    {
        if (Request::isPost()) {
            $data = Request::post();
            if (!$data['id']) {

                $res = PostOrderModel::create($data);
            } else {

                $res = PostOrderModel::update($data);
            }

            if ($res) {
                $this->success(lang('edit success'), url('index'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');
            $postOrder = PostOrderModel::where(['orderId' => $id])->find();

            $view = [
                'info' => $postOrder,
                'orderId' => $id,
                'post' => Db::name('post')->select(),
                'title' => lang('add'),
            ];
            View::assign($view);
            return View::fetch('send');
        }


    }

    public function notice()
    {

        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('pay_notify')->alias('a')
                ->order('a.id desc')
                ->field('a.*')
                ->order('a.id desc')
                ->cache(30)
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            return $result = ['code' => 0, 'msg' => lang('get info success'),
                'data' => $list['data'],
                'count' => $list['total']];
        }
        return view();

    }


    public function timeLine()
    {

        if (Request::isPost()) {
            $id = Request::get('id');

            $order = OrderModel::getOne($id);
            $orderPay = OrderPayModel::where(['orderId' => $id])->find();
            $postOrder = PostOrderModel::where(['orderId' => $id])->find();

            $info[0] = ['desc' => '开始生成订单,用户填写信息', 'time' => date('Y:m:d h:i:s',$order['orderDate'])];
            $info[1] = ['desc' => '创建订单', 'time' => date('Y:m:d h:i:s',$order['cTime'])];

            $info[2] = ['desc' => '更新订单', 'time' => date('Y:m:d h:i:s',$order['mTime'])];

            if ($orderPay) {
                $info[3] = ['desc' => '开始支付', 'time' => date('Y:m:d h:i:s',$orderPay['cTime'])];
                $info[4] = ['desc' => '支付成功', 'time' => date('Y:m:d h:i:s',$orderPay['mTime'])];
                if ($postOrder) {
                    $info[5] = ['desc' => '发货', 'time' => date('Y:m:d h:i:s',$postOrder['cTime'])];
                    $info[6] = ['desc' => '用户收货', 'time' =>date('Y:m:d h:i:s', $postOrder['mTime'])];

                }

            }


            if ($postOrder) {
                $info[3] = ['desc' => '发货', 'time' => date('Y:m:d h:i:s',$postOrder['cTime'])];
                $info[4] = ['desc' => '用户收货', 'time' => date('Y:m:d h:i:s',$postOrder['mTime'])];

            }


            if ($info) {
                $this->success(lang('edit success'), url('index'), $info);
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');
            $postOrder = PostOrderModel::where(['orderId' => $id])->find();

            $view = [
                'info' => $postOrder,
                'orderId' => $id,
                'post' => Db::name('post')->select(),
                'title' => lang('add'),
            ];
            View::assign($view);
            return View::fetch('time_line');
        }


    }

}