<?php
/**
 * Created by IntelliJ IDEA.
 * User: fan
 * Date: 2019/11/4
 * Time: 下午 08:40
 */

namespace app\admin\controller\shop;


use app\common\controller\Backend;
use app\common\controller\Base;
use app\common\model\GoodsClazz;
use app\common\model\GoodsSku;
use app\common\model\GoodsSkutype;
use app\common\model\Goods as GoodsModel;
use app\common\model\Link as LinkModel;
use lemo\helper\TreeHelper;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

/**
 * 抖音链接作为一个商品出售
 * Class Goods
 * @package app\admin\controller
 */
class Goods extends Backend
{

    public function edit()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try {
                $this->validate($data, 'Goods');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $res = GoodsModel::update($data);
            if ($res) {
                $this->success(lang('edit success'), url('index'));
            } else {
                $this->error(lang('edit fail'));
            }
        }
        $info = GoodsModel::find(Request::get('id'));
        $goodsClass = GoodsClazz::where('status', 1)->select()->toArray();
        $goodsClass = TreeHelper::cateTree($goodsClass);
        $goodsSkutype = GoodsSkutype::where('status', 1)->select()->toArray();
        $params['name'] = 'container';
        $params['content'] = $info['content'];
        $view = [
            'info' => $info,
            'goodsSkutype' => $goodsSkutype,
            'goodsClass' => $goodsClass,
            'title' => lang('edit'),
            'ueditor' => build_ueditor($params),
        ];
        View::assign($view);
        return View::fetch('add');

    }

    /**
     *
     *
     *
     * "pid=5&title=%E9%AD%94%E5%AE%9D&description=0&keywords=8&link=http%3A%2F%2Fbai.com&thumb=&click=8&click=8&click=8&click=8&click=8&click=8&click=8&status=1&sku%5B%5D%5Bname%5D=8&sku%5B%5D%5Bprice%5D=&sku%5B%5D%5Bname%5D=8&sku%5B%5D%5Bprice%5D=9&id="
     * @return array|string
     * @throws \think\db\exception\DbException
     */
    public function index()
    {


        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('Goods')->alias('goods')
                ->join('goods_clazz clazz', 'clazz.id=goods.pid')
                ->join('goods_sku sku', 'sku.goodsId=goods.id')
                ->join('admin', 'admin.id=goods.adminId')
                ->where('goods.title', 'like', '%' . $keys . '%')
                ->field('goods.id,goods.title,clazz.title clazzTitle,goods.keywords,
                goods.status,goods.click,
                goods.thumb,goods.price,goods.stock,admin.username')
                ->order('goods.sort desc,goods.id desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();
    }


    public function add()
    {
        if (Request::isPost()) {
            $data = Request::post();

            $data['adminId'] = Session::get('admin.id');
            $ret = GoodsModel::create($data);

            if (!empty($data['sku'])) {
                foreach ($data['sku'] as $v) {
                    $v['goods_id'] = $ret->id;
                    GoodsSku::create($v);
                }
            } else {
                $data['goodsId'] = $ret['id'];
                GoodsSku::create($data);
            }


            if ($ret) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }


        } else {
            $goodsClass = GoodsClazz::where('status', 1)->select()->toArray();
            $goodsClass = TreeHelper::cateTree($goodsClass);
            $goodsSkutype = GoodsSkutype::where('status', 1)->select()->toArray();
            $params['name'] = 'container';
            $params['content'] = '';
            $view = [
                'info' => '',
                'goodsSkutype' => $goodsSkutype,
                'goodsClass' => $goodsClass,
                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];

            View::assign($view);
            return View::fetch();
        }
    }


    /**
     * 商品分类
     * @return array|string
     * @throws \think\db\exception\DbException
     */
    public function clazz()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = cache('goodClazz');
            if (!$list) {
                $list = Db::name('goods_clazz')
                    ->order(['sort'=>'asc','id'=>'asc'])
                    ->where('title', 'like', '%' . $keys . '%')
                    ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                    ->toArray();

                foreach ($list['data'] as $k => $v) {
                    $list['data'][$k]['lay_is_open'] = false;
                }
                cache('goodClazz', $list, 60);
            }


            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();
    }


    public function addClazz()
    {

        if (Request::isPost()) {

            $data = Request::post();
            $res = GoodsClazz::create($data);
            if ($res) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }
        } else {
            $goodsClass = GoodsClazz::where('status', 1)->select()->toArray();
            $goodsClass = TreeHelper::cateTree($goodsClass);
            $params['name'] = 'container';
            $params['content'] = '';
            $view = [
                'info' => '',
                'goodsClass' => $goodsClass,
                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch();
        }
    }


    public function editClazz()
    {
        if (Request::isPost()) {
            $data = Request::post();
            if (!$data['id']) {
                $this->error(lang('invalid data'));
            }

            $res = GoodsClazz::update($data);
            if ($res) {
                $this->success(lang('edit success'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');
            $goodsClass = GoodsClazz::where('status', 1)->select()->toArray();
            $goodsClass = TreeHelper::cateTree($goodsClass);
            $params['name'] = 'container';
            $params['content'] = '';
            $info = GoodsClazz::find($id);
            $view = [
                'info' => $info,
                'goodsClass' => $goodsClass,
                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch('add_clazz');
        }


    }

    public function clazzDel()
    {
        $id = Request::post('id');
        if ($id) {
            GoodsClazz::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('delete fail'));

        }

    }


    /**
     * 规格类型
     */


    public function skutype()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('goods_skutype')->alias('a')
                ->where('a.name', 'like', '%' . $keys . '%')
                ->order('a.sort desc,a.id desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();
    }


    public function addSkutype()
    {

        if (Request::isPost()) {

            $data = Request::post();
            $res = GoodsSkutype::create($data);
            if ($res) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }
        } else {

            $view = [
                'info' => '',
                'title' => lang('add'),
            ];
            View::assign($view);
            return View::fetch();
        }
    }


    public function editSkutype()
    {
        if (Request::isPost()) {
            $data = Request::post();
            if (!$data['id']) {
                $this->error(lang('invalid data'));
            }

            $res = GoodsSkutype::update($data);
            if ($res) {
                $this->success(lang('edit success'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');


            $info = GoodsSkutype::find($id);
            $view = [
                'info' => $info,

                'title' => lang('add'),

            ];
            View::assign($view);
            return View::fetch('add_skutype');
        }


    }

    public function sku()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('GoodsSku')->alias('a')
                ->join('goods', 'a.goodsId=goods.id','left')
                ->field('a.*')
                ->where('a.title', 'like', '%' . $keys . '%')
                ->order('a.sort desc,a.id desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            return $result = ['code' => 0, 'msg' => lang('get info success'),
                'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();
    }


    public function add_sku()
    {
        if (Request::isPost()) {
            $data = Request::post();

            $data['adminId'] =4;// Session::get('admin.id');
            // var_dump($data);
            $ret = GoodsSku::create($data);

            if ($ret) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }


        } else {
            $goods = GoodsModel::where(['status'=>1])->select()->toArray();

            $goodsSkutype = GoodsSkutype::where('status', 1)->select()->toArray();
            $params['name'] = 'container';
            $params['content'] = '';
            $view = [
                'info' => '',
                'goodsSkutype' => $goodsSkutype,
                'goods' => $goods,
                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];

            View::assign($view);
            return View::fetch();
        }
    }

    public function edit_sku()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try {
                $this->validate($data, 'Goods');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $res = GoodsSku::update($data);
            if ($res) {
                $this->success(lang('edit success'), url('sku'));
            } else {
                $this->error(lang('edit fail'));
            }
        }
        $info = GoodsSku::getOne(Request::get('id'));
        $goodsClass = GoodsSkutype::where('status', 1)->select()->toArray();
        $goodsClass = TreeHelper::cateTree($goodsClass);
        $goodsSkutype = GoodsSkutype::where('status', 1)->select()->toArray();
        $params['name'] = 'container';
        $params['content'] = $info['content'];
        $view = [
            'info' => $info,
            'goods'=>GoodsModel::where(['status'=>1])->select()->toArray(),
            'goodsSkutype' => $goodsSkutype,
            'goodsClass' => $goodsClass,
            'title' => lang('edit'),
            'ueditor' => build_ueditor($params),
        ];
        View::assign($view);
        return View::fetch('add_sku');

    }

    public function del_sku()
    {
        $id = Request::post('id');
        if ($id) {
            GoodsSku::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('delete fail'));


        }
    }

    /**
     *  商品删除
     */
    public function delete()
    {
        $id = Request::post('id');
        if ($id) {
            GoodsModel::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('delete fail'));


        }
    }
    public function sku_state(){

        if (Request::isPost()) {
            $id = Request::post('id');
            if (empty($id)) {
                $this->error('id'.lang('not exist'));
            }
            $goods= GoodsSku::getOne($id);
            $status = $goods['status'] == 1 ? 0 : 1;
            $goods->status = $status;
            $goods->save();
            $this->success(lang('edit success'));
        }
    }

    public function clazzState()
    {
        if (Request::isPost()) {
            $id = Request::post('id');
            if (empty($id)) {
                $this->error('id'.lang('not exist'));
            }
            $goods= GoodsClazz::getOne($id);
            $status = $goods['status'] == 1 ? 0 : 1;
            $goods->status = $status;
            $goods->save();
            $this->success(lang('edit success'));
        }
    }

}