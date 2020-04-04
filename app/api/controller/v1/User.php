<?php

namespace app\api\controller\v1;

use think\facade\Request;
use lemo\api\Api;
use lemo\api\validate\ValidataBase;
use app\admin\model\Admin;
use app\admin\model\AuthGroup;

class User extends Api
{
    /**
     * 不需要鉴权方法
     * index、save不需要鉴权
     * ['index','save']
     * 所有方法都不需要鉴权
     * [*]
     */
    protected $noAuth = ['login'];
    //    案例
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        //通用参数验证
        ValidataBase::validateCheck(['name' => 'require', 'password' => 'require'], Request::param()); //参数验证
        //通用分页
        dump($this->uid);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        echo "create";
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        echo "save";
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        echo "read";
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        echo "edit";
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        echo "update";
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        echo "delete";
    }


    public function address($id)
    {
        echo "address-";
    }

    public function login()
    {

        $where['username'] = input("username");
        $password = input("password");
        $info = Admin::where($where)->find();

        if (!$info) {
            return self::returnMsg(0,lang('please check username or password'));
        }
        if ($info['status'] == 0) {
            return self::returnMsg(0,lang('account is disabled'));
        }
        if (!password_verify($password, $info['password'])) {

            return self::returnMsg(0,lang('please check username or password'));

        }


        return self::returnMsg(1, '', ['token'=>$info['token']]);


    }
}
