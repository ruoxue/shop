<?php


namespace app\mer\controller\order;


use app\common\controller\Backend;
use lemo\helper\TreeHelper;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use  app\common\model\Address as AddressModel;
use  app\common\model\Area as AreaModel;
use  app\common\model\User as UserModel;






class Address extends Backend
{
    public function index()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            // $list = cache('account');
            //if (!$list) {
            $list = Db::name('address')
                ->alias('address')
                ->join('user','user.id=address.uid')
                ->join('area','area.id=address.areaId')

                //  ->where('nickname', 'like', '%' . $keys . '%')
                ->field('address.*,user.nickname')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();


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
            $res = AddressModel::create($data);
            if ($res) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }
        } else {
            $area = AreaModel::where('status', 1)->select()->toArray();

            $user = UserModel::where('status', 1)->select()->toArray();
            $area = TreeHelper::cateTree($area);



            $params['name'] = 'container';
            $params['content'] = '';
            $view = [
                'info' => '',
                'area'=>$area,
                'user'=>$user,

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

            $res = AddressModel::update($data);
            if ($res) {
                $this->success(lang('edit success'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');

            $params['name'] = 'container';
            $params['content'] = '';
            $info = AddressModel::find($id);
            $area = AreaModel::where('status', 1)->select()->toArray();
            $area = TreeHelper::cateTree($area);
            $user = UserModel::where('status', 1)->select()->toArray();

            $view = [
                'info' => $info,
                'area'=>$area,
                'user'=>$user,

                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch('add');
        }


    }




    public function area()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            // $list = cache('account');
            //if (!$list) {
            $list = Db::name('area')
                ->alias('area')


                //  ->where('nickname', 'like', '%' . $keys . '%')
                ->field('area.*')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();

//            foreach ($list['data'] as $k => $v) {
//                $list['data'][$k]['lay_is_open'] = false;
//            }
            // cache('account', $list, 10);
            //  }


            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();
    }




}