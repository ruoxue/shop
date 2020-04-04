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
use app\common\model\Article as ArticleModel;

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
class Article extends Backend
{

    public function edit()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try {
                $this->validate($data, 'article');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $res = ArticleModel::update($data);
            if ($res) {
                $this->success(lang('edit success'), url('index'));
            } else {
                $this->error(lang('edit fail'));
            }
        }else {


            $info = ArticleModel::find(Request::get('id'));

            $params['name'] = 'container';
            $params['content'] = $info['content'];
            $view = [
                'info' => $info,

                'title' => lang('edit'),
                'ueditor' => build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch('add');
        }
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
            $list = Db::name('article')->alias('article')

                 ->field('article.*')
                ->order('article.sort desc,article.id desc')
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
            $ret = ArticleModel::create($data);



            if ($ret) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }


        } else {
              $params['name'] = 'container';
            $params['content'] = '';
            $view = [
                'info' => '',

                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];

            View::assign($view);
            return View::fetch();
        }
    }

    // 广告状态修改
    public function state()
    {
        if (Request::isPost()) {
            $id = Request::post('id');
            if (empty($id)) {
                $this->error('id'.lang('not exist'));
            }
            $aeticle= ArticleModel::find($id);
            $status = $aeticle['status'] == 1 ? 0 : 1;
            $aeticle->status = $status;
            $aeticle->save();
            $this->success(lang('edit success'));
        }
    }


}