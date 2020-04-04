<?php
/**
 * Created by IntelliJ IDEA.
 * User: fan
 * Date: 2019/11/4
 * Time: 下午 08:40
 */

namespace app\mer\controller\order;


use app\common\controller\Backend;

use app\common\model\Post as PostModel;
use app\common\model\PayChannel as PayChannelModel;
use lemo\helper\TreeHelper;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

/**
 * 快递
 *
 * 虚拟商品不需要
 * Class POST
 * @package app\admin\controller
 */
class Post extends Backend
{


    /**
     * 商品分类
     * @return array|string
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            // $list = cache('account');
            //if (!$list) {
            $list = Db::name('post')
                ->alias('post')
//                ->join('admin' ,'admin.id=account.adminId')
                //  ->where('nickname', 'like', '%' . $keys . '%')
                    ->field('post.*')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();

            foreach ($list['data'] as $k => $v) {
                $list['data'][$k]['lay_is_open'] = false;
            }
            // cache('account', $list, 10);
            //  }


            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();
    }


    public function add()
    {

        if (Request::isPost()) {

            $data = Request::post();
            $data['adminId'] = Session::get('admin.id', 0);
            $res = PostModel::create($data);
            if ($res) {
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


    public function edit()
    {
        if (Request::isPost()) {
            $data = Request::post();
            if (!$data['id']) {
                $this->error(lang('invalid data'));
            }

            $res = PostModel::update($data);
            if ($res) {
                $this->success(lang('edit success'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');
            $payAccount = PostModel::where('status', 1)->select()->toArray();

            $params['name'] = 'container';
            $params['content'] = '';
            $info = PostModel::find($id);
            $view = [
                'info' => $info,

                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch('add');
        }


    }


    public function del()
    {
        $id = Request::post('id');
        if ($id) {

            PostModel::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('delete fail'));

        }
    }

    public function state()
    {
        $id = Request::post('id');
        if ($id) {
            $where['id'] = $id;

            $link = PostModel::find($id);
            $where['status'] = $link['status'] ? 0 : 1;
            PostModel::update($where);

            $this->success(lang('edit success'));

        } else {
            $this->error(lang('edit fail'));

        }


    }

    /**
     * 订单
     */
    public  function  order(){
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            // $list = cache('account');
            //if (!$list) {
            $list = Db::name('post_order')
                ->alias('postOrder')
//                ->join('admin' ,'admin.id=account.adminId')
                //  ->where('nickname', 'like', '%' . $keys . '%')
                ->field('postOrder.*')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();

            foreach ($list['data'] as $k => $v) {
                $list['data'][$k]['lay_is_open'] = false;
            }
            // cache('account', $list, 10);
            //  }


            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();

    }




}