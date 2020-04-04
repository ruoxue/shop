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
use app\common\model\GoodsSkutype;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use app\common\model\Order as OrderModel ;
use  app\common\model\PostOrder as PostOrderModel;

class Order extends Backend {

    public function index(){

        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('order')->alias('a')
                ->join('pay_channel channel','channel.id=a.type','left')
                ->join('user','user.id=a.uid','left')
                ->join('goods_sku sku','sku.id=a.skuId','left')
                ->join('admin','admin.id=sku.adminId','left')
                ->order('a.id desc')
                ->field('a.*,a.mTime,sku.title as skuTitle,channel.title,
                
                user.nickname,admin.username adminName')
                ->where(['sku.adminId'=>session('admin.id')])
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
                $this->error(lang('invalid data'));
            }

            $res = PostOrderModel::update($data);
            if ($res) {
                $this->success(lang('edit success'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');

         $postOrder=   PostOrderModel::where(['orderId'=>$id])->find();

            $view = [
                'info' => $postOrder,
                'orderId'=>$id,
                'post'=>Db::name('post')->select(),
                'title' => lang('add'),
            ];
            View::assign($view);
            return View::fetch('send');
        }


    }

}