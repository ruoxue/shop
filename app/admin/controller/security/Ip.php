<?php
/**
 * Created by IntelliJ IDEA.
 * User: fan
 * Date: 2019/11/4
 * Time: 下午 08:40
 */

namespace app\admin\controller\security;


use app\common\controller\Backend;
use app\common\model\Ip as IpModel;
use app\common\model\IpRecord as IpRecordModel;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;

/**
 * 抖音链接作为一个商品出售
 * Class Goods
 * @package app\admin\controller
 */
class Ip extends Backend
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
            $list = Db::name('ip')
                ->alias('ip')
                //  ->where('nickname', 'like', '%' . $keys . '%')
                ->field('ip.* ,INET_ATON(ip.startIp) startIpAddress,INET_ATON(ip.endIp) endIpAddress')
                ->order('id desc')
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

            $res = IpModel::create($data);
            if ($res) {
                $this->success(lang('add success'), url('index'));
            } else {
                $this->error(lang('add fail'));

            }
        } else {

            $ipInfo = IpModel::where('status', 1)->select()->toArray();
            $params['name'] = 'container';
            $params['content'] = '';
            $view = [
                'info' => '',
                'bank' => $ipInfo,
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
            // dump($data);
            $res = IpModel::update($data);
            if ($res) {
                $this->success(lang('edit success'), url('index'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');

            $params['name'] = 'container';
            $params['content'] = '';
            $info = IpModel::find($id);
            $view = [
                'info' => $info,
                'title' => lang('add'),
                'ueditor' => build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch('add');
        }


    }


    public function delete()
    {
        $id = Request::post('id');
        if ($id) {

            IpModel::destroy($id);
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

            $link = IpModel::find($id);
            $where['status'] = $link['status'] ? 0 : 1;
            IpModel::update($where);

            $this->success(lang('edit success'));

        } else {
            $this->error(lang('edit fail'));

        }


    }

    public function record()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            // $list = cache('account');
            //if (!$list) {
            $list = Db::name('ip_record')
                ->alias('record')
                //  ->where('nickname', 'like', '%' . $keys . '%')
                ->field('record.*')

                ->order('id desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();


            // cache('account', $list, 10);
            //  }


            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];

        }
        return View::fetch();
    }


    public function grey()
    {
        $id = Request::post('id');
        if ($id) {
            $ipRecord = IpRecordModel::find($id);
            $ipModel = IpModel::where('INET_ATON(startIp) <= INET_ATON("'.$ipRecord['ip'].'")')
                ->where('INET_ATON(endIp) >=INET_ATON("'.$ipRecord['ip'].'")')
                ->find();
            if (!$ipModel) {

                $ipModel['startIp'] = $ipRecord['ip'];
                $ipModel['endIp'] = $ipRecord['ip'];
                $ipModel['info'] = $ipRecord['url'];
                $ipModel['mTime'] = time();
                $ipModel['cTime'] = time();
                $ipModel['status'] = 0;
                $res = IpModel::create($ipModel);
            } else {
                $where['id'] = $ipModel['id'];
                $where['status'] = 0;
                $res = IpModel::update($where);
            }
            if ($res) {
                $this->success(lang('修改成功'));
            } else {
                $this->success(lang('修改失败'));
            }

        } else {
            $this->error(lang('delete fail'));

        }
    }

    public function black()
    {
        $id = Request::post('id');
        if ($id) {
            $ipRecord = IpRecordModel::find($id);
            $ipModel = IpModel::where('INET_ATON(startIp) <= INET_ATON("'.$ipRecord['ip'].'")')
                ->where('INET_ATON(endIp) >=INET_ATON("'.$ipRecord['ip'].'")')
                ->find();
            if (!$ipModel) {

                $ipModel['startIp'] = $ipRecord['ip'];
                $ipModel['endIp'] = $ipRecord['ip'];
                $ipModel['info'] = $ipRecord['url'];
                $ipModel['mTime'] = time();
                $ipModel['cTime'] = time();
                $ipModel['status'] = 10;
                $res = IpModel::create($ipModel);
            } else {
                $where['id'] = $ipModel['id'];
                $where['status'] = 10;
                $res = IpModel::update($where);
            }
            if ($res) {
                $this->success(lang('修改成功'));
            } else {
                $this->success(lang('修改失败'));
            }

        } else {
            $this->error(lang('delete fail'));

        }
    }


    public function white()
    {
        $id = Request::post('id');
        if ($id) {
            $ipRecord = IpRecordModel::find($id);
            $ipModel =IpModel::where('INET_ATON(startIp) <= INET_ATON("'.$ipRecord['ip'].'")')
                ->where('INET_ATON(endIp) >=INET_ATON("'.$ipRecord['ip'].'")')
                ->find();
            if (!$ipModel) {

                $ipModel['startIp'] = $ipRecord['ip'];
                $ipModel['endIp'] = $ipRecord['ip'];
                $ipModel['info'] = $ipRecord['url'];
                $ipModel['mTime'] = time();
                $ipModel['cTime'] = time();
                $ipModel['status'] = 1;
                $res = IpModel::create($ipModel);
            } else {
                $where['id'] = $ipModel['id'];
                $where['status'] = 1;
                $res = IpModel::update($where);
            }
            if ($res) {
                $this->success(lang('修改成功'));
            } else {
                $this->success(lang('修改失败'));
            }

        } else {
            $this->error(lang('delete fail'));

        }
    }
}