<?php


namespace app\mer\controller\order;


use app\common\controller\Backend;
use app\common\model\Bank as BankModel;
use app\common\model\Area as AreaModel;
use app\common\model\PayAccount as PayAccountModel;
use app\common\model\User as UserModel;
use lemo\helper\TreeHelper;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;

class Bank extends Backend
{
    public function index()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
             $list = cache('bank');
            if (!$list) {
            $list = Db::name('bank')
                ->alias('bank')

                //  ->where('nickname', 'like', '%' . $keys . '%')
                ->field('bank.*')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();


             cache('bank', $list, 10);
              }


            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();
    }


    public function add()
    {

        if (Request::isPost()) {

            $data = Request::post();
            $res = BankModel::create($data);
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

            $res = BankModel::update($data);
            if ($res) {
                $this->success(lang('edit success'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');

            $params['name'] = 'container';
            $params['content'] = '';
            $info = BankModel::find($id);


            $view = [
                'info' => $info,
                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch('add');
        }


    }




    public function state()
    {
        $id = Request::post('id');
        if ($id) {
            $where['id'] = $id;

            $link = BankModel::find($id);
            $where['status'] = $link['status'] ? 0 : 1;
            BankModel::update($where);

            $this->success(lang('edit success'));

        } else {
            $this->error(lang('edit fail'));

        }

    }





}