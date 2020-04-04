<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\model\User as UserModel;

use lemo\helper\SignHelper;
use think\facade\Request;
use think\facade\Session;

class Login extends Frontend
{

    /**
     * login
     */
    public function index()
    {

        if (Request::isPost()) {


                $username = Request::post('username');
                $password =  $this->request->post('password', '123456', 'lemo\helper\StringHelper::filterWords');

            $where['nickname'] = strip_tags(trim($username));
                $password = strip_tags(trim($password));
                $info = UserModel::where($where)->find();

                if (!$info) {
                    $this->error('账号不存在');
                }
                if ($info['status'] == 0) {
                    $this->error('账号被禁用');
                }

                if (!password_verify($password, $info['password'])) {

                   $this->error('密码错误');

                }


                unset($info['password']);

                Session::set('user', $info, 7 * 24 * 3600);
                Session::set('user_sign', SignHelper::authSign($info), 7 * 24 * 3600);

                $this->success('登录成功',url('/index/index/index'));




        } else {
            return view();
        }


    }
}