<?php
/**
 * Created by IntelliJ IDEA.
 * User: fan
 * Date: 2019/11/4
 * Time: 下午 08:40
 */

namespace app\mer\controller\order;


use app\common\controller\Backend;

use app\common\model\PayAccount as PayAccountModel;
use app\common\model\PayChannel as PayChannelModel;
use app\common\model\Bank as BankModel;
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
class Account extends Backend
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
            $list = Db::name('pay_account')
                ->alias('account')
                ->join('admin' ,'admin.id=account.adminId')
                //  ->where('nickname', 'like', '%' . $keys . '%')
                    ->where(['adminId'=>session('admin.id')])
                    ->field('account.*,admin.username adminName')
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
            $res = PayAccountModel::create($data);
            if ($res) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }
        } else {
            $payAccount = PayAccountModel::where('status', 1)->select()->toArray();
            $payAccount = TreeHelper::cateTree($payAccount);

            $payChannel = PayChannelModel::where('status', 1)->select()->toArray();
            $payChannel = TreeHelper::cateTree($payChannel);
           $bank= BankModel::where('status', 1)->select()->toArray();
            $params['name'] = 'container';
            $params['content'] = '';
            $view = [
                'info' => '',
                'bank'=>$bank,
                'payAccount' => $payAccount,
                'payChannel' => $payChannel,
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
            $res = PayAccountModel::update($data);
            if ($res) {
                $this->success(lang('edit success'));
            } else {
                $this->error(lang('edit fail'));

            }
        } else {
            $id = Request::get('id');
            $bank= BankModel::where('status', 1)->select()->toArray();
            $payAccount = PayAccountModel::where('status', 1)->select()->toArray();
            $payAccount = TreeHelper::cateTree($payAccount);
            $payChannel = PayChannelModel::where('status', 1)->select()->toArray();
            $payChannel = TreeHelper::cateTree($payChannel);
            $params['name'] = 'container';
            $params['content'] = '';
            $info = PayAccountModel::find($id);
            $view = [
                'info' => $info,
                'bank'=>$bank,
                'payAccount' => $payAccount,
                'payChannel' => $payChannel,
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

            PayAccountModel::destroy($id);
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

            $link = PayAccountModel::find($id);
            $where['status'] = $link['status'] ? 0 : 1;
            PayAccountModel::update($where);

            $this->success(lang('edit success'));

        } else {
            $this->error(lang('edit fail'));

        }


    }


    public function bindMainAccount($id)
    {


        $ret = send_post('http://127.0.0.1:521/bindAccount', ['id' => $id]);

        $ret = json_decode($ret, true);
        if ($ret['code'] == 1) {
            PayAccountModel::update(['token' => $ret['req'], 'extra' => $ret['msg']],['id' => $id]);
            $this->success('绑定成功','',['token' => $ret['req'], 'extra' => $ret['msg']]);
        } else {
            $this->success('绑定失败');
        }


    }

    public function unBindMainAccount($id)
    {


        $ret = send_post('http://127.0.0.1:521/unbindAccount', ['id' => $id]);
        $ret = json_decode($ret, true);
        if ($ret['code'] == 1) {
            PayAccountModel::update(['token' => $ret['req'], 'extra' => $ret['msg']],['id' => $id]);
            $this->success('解绑成功','',['token' => $ret['req'], 'extra' => $ret['msg']]);
        } else {
            $this->error('解绑失败');
        }


    }

}