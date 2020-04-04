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
 * Date: 2019/8/2
 */
namespace app\admin\controller\sys;

use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\admin\model\Admin;
use app\common\controller\Backend;
use app\common\model\GoodsClazz;
use lemo\helper\SignHelper;
use lemo\helper\StringHelper;
use lemo\helper\TreeHelper;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Auth extends Backend
{
    public $uid = '';
    public function initialize()
    {
        $this->uid = Session::get('admin.id');
        parent::initialize(); // TODO: Change the autogenerated stub
    }
    /*-----------------------管理员管理----------------------*/
    // 管理员列表
    public function adminList()
    {
        if(Request::isPost()){
            $where=$this->request->post();
            $map=[];
            $map1=[];
            $map2=[];
            if(isset($where['keys'])) {
                $map = [
                    ['a.username', 'like', "%" . $where['keys'] . "%"],

                ];
                $map1 = [
                    ['a.email', 'like', "%" . $where['keys'] . "%"],

                ];
                $map2= [
                    ['a.mobile', 'like', "%" . $where['keys'] . "%"],
                ];

            }
            $list=Db::name('admin')->alias('a')
                ->join('auth_group ag','a.group_id = ag.id','left')
                ->field('a.*,ag.title')
                ->whereOr($map,$map1,$map2)
                ->select();

//            foreach ($list['data'] as $k => $v) {
//                $list['data'][$k]['lay_is_open'] = false;
//            }

            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list];
        }

        return view();
    }

    // 管理员添加
    public function adminAdd()
    {
        if (Request::isPost()) {
            $data = $this->request->post();
            try{
                $this->validate($data, 'Admin');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            $data['password'] = StringHelper::filterWords($data['password']);
            if(!$data['password']){
                $data['password']='123456';
            }
            $data['password'] = password_hash($data['password'],PASSWORD_BCRYPT, SignHelper::passwordSalt());
            //添加
            $model = new Admin();
            $data['token']=md5(uniqid(microtime(true),true));
            $result = $model->add($data);
            if ($result) {
                $this->success(lang('add success'), url('sys.Auth/adminList'));
            } else {
                $this->error(lang('add fail'));
            }
        }
        $info = '';
        $auth_group = AuthGroup::where('status', 1)
            ->select();
        $admin = Admin::where('status', 1)->select()->toArray();
        $view = [
            'info'  =>$info,
            'admin'=>$admin,
            'authGroup' => $auth_group,
            'title' => lang('add'),
        ];
        View::assign($view);
        return view();

    }

    // 管理员删除
    public function adminDel()
    {
        $id = $this->request->post('id');
        if ($id > 1) {
            Admin::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('supper man cannot delete'));

        }
    }



    // 管理员状态修改
    public function adminState()
    {
        if (Request::isPost()) {
            $data = $this->request->post();
            $id = $this->request->post('id');
            if (empty($id)) {
                $this->error('id'.lang('not exist'));
            }
            if ($id == 1) {
                $this->error(lang('supper man cannot edit state'));
            }
            $model = new Admin();
            $model->state($data);
            $this->success(lang('edit success'));
        }
    }

    /**
     * 管理员修改
     */
    public function adminEdit()
    {
        if (Request::isPost()) {
            $data = $this->request->post();
            if(!$data['username']) $this->error(lang('username').lang('cannot null'));
            if(!$data['password']) $this->error(lang('password').lang('cannot null'));
            if(!$data['group_id']) $this->error(lang('adminGroup').lang('cannot null'));
            $admin = Admin::find($data['id']);
            if(password_verify($data['password'],$admin['password'])){
                unset($data['password']);
            }else{
                $data['password'] = $this->request->post('password', '123456', 'lemo\helper\StringHelper::filterWords');
                $data['password'] = password_hash($data['password'],PASSWORD_BCRYPT, SignHelper::passwordSalt());
            }
            $model = new Admin();
            $model->edit($data);
            if($this->uid==$data['id']){
                session('admin',null);
            }
            $this->success(lang('edit success'), url('sys.Auth/adminList'));

        } else {
            $id = Request::param('id')?Request::param('id'):$this->uid;
            if ($id) {
                $auth_group = AuthGroup::select();
                $admin = Admin::find($id);
                $adminList = Admin::where('status', 1)->select()->toArray();

                $view = [
                    'admin'=>$adminList,
                    'info' => $admin,
                    'authGroup' => $auth_group,
                    'title' => lang('edit'),
                ];
                View::assign($view);
                return view('admin_add');
            }
        }
    }

    /********************************权限管理*******************************/
    // 权限列表
    public function adminRule()
    {
        if(Request::isPost()){
            $uid = $this->uid;

            $arr = Db::name('auth_rule')
                ->order('pid asc,sort asc')
                ->select()->toArray();
            foreach($arr as $k=>$v){
                $arr[$k]['lay_is_open']=false;
            }
            cache('authRuleList_'.$uid, $arr, 3600);

            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$arr,'is'=>true,'tip'=>'操作成功'];
        }
        return view('admin_rule');
    }

    // 权限菜单显示或者隐藏
    public function ruleState()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $info = AuthRule::find($id);
            $info->menu_status = $info['menu_status'] == 1 ? 0 : 1;
            $info->save();
            $this->success(lang('edit success'));

        }
    }

    // 设置权限是否验证
    public function ruleOpen()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $info = AuthRule::find($id);
            $info->auth_open = $info['auth_open'] == 1 ? 0 : 1;
            $info->save();
            $this->success(lang('edit success'));
        }
    }

    // 设置权限排序
    public function ruleSort()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $sort = Request::param('sort');
            $info = AuthRule::find($id);
            $info->sort = $sort;
            $info->save();
            $this->success(lang('edit success'));
        }
    }

    // 权限删除
    public function ruleDel()
    {
        $id = Request::param('id');
        $child = AuthRule::where('pid',$id)->find();
        if ($id && !$child) {
            AuthRule::destroy($id);
            $this->success(lang('delete success'));
        }elseif($child){
            $this->error(lang('delete child first'));

        }else{
            $this->error('id'.lang('not exist'));
        }
    }

    // 权限批量删除
    public function ruleSelectDel()
    {
        $ids = Request::param('ids');
        if ($ids) {

            AuthRule::destroy($ids);
            $this->success(lang('delete success'));
        }

    }

    // 权限增加
    public function ruleAdd()
    {
        if (Request::isPost()) {
            $data = $this->request->post();
            if (empty($data['title'])) {
                $this->error(lang('rule name cannot null'));
            }
            if (empty($data['sort'])) {
                $this->error(lang('sort').lang(' cannot null'));
            }
            $data['icon'] = $data['icon']?$data['icon']:'fa fa-adjust';
            if (AuthRule::create($data)) {
                $this->success(lang('add success'), url('sys.Auth/adminRule'));
            } else {
                $this->error(lang('add fail'));
            }
        } else {
            $list = Db::name('auth_rule')
                ->order('sort ASC')
                ->select();
            $list = TreeHelper::cateTree($list);
            $pid = Request::param('id') ? Request::param('id') : 0;
            $rule = '';
            if(Request::get('rule_id')){
                $rule = Db::name('auth_rule')
                    ->find(Request::get('rule_id'));
            }

            $view = [
                'info' => null,
                'pid' => $pid,
                'ruleList' => $list,
                'rule' =>$rule,
            ];

            View::assign($view);
            return view('rule_add');
        }
    }

    //权限修改
    public function ruleEdit()
    {
        if (request()->isPost()) {
            $data = Request::param();
            $data['icon'] = $data['icon']?$data['icon']:'fa fa-adjust';
            $where['id'] = $data['id'];
            AuthRule::update($data);
            $this->success(lang('edit success'), url('sys.Auth/adminRule'));
        } else {
            $list = Db::name('auth_rule')
                ->order('sort asc')
                ->select();
            $list = TreeHelper::cateTree($list);
            $id = Request::param('id');
            $info = AuthRule::find($id)->toArray();
            $rule = '';
            if(Request::get('rule_id')){
                $rule = Db::name('auth_rule')
                    ->find(Request::get('rule_id'));
            }
            $view = [
                'info' => $info,
                'ruleList' => $list,
                'rule' => $rule,
            ];
            View::assign($view);
            return view('rule_add');
        }
    }


    /*-----------------------用户组管理----------------------*/

    // 用户组管理
    public function group()
    {
        if(Request::isPost()){
            //条件筛选
            $title = Request::param('title');
            //全局查询条件
            $where = [];
            if ($title) {
                $where[] = ['title', 'like', '%' . $title . '%'];
            }
            //显示数量
            $pageSize = Request::param('page_size', Config::get('app.page_size'));

            //查出所有数据
            $list = AuthGroup::where($where)
                ->paginate(
                    $this->pageSize, false,
                    ['query' => Request::param()]
                )->toArray();
            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list['data']];
        }
        return view();
    }

    // 用户组删除
    public function groupDel()
    {
        $id = $this->request->post('id');
        if ($id > 1) {
            AuthGroup::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('supper man cannot delete'));
        }

    }

    // 用户组添加
    public function groupAdd()
    {
        if (Request::isPost()) {
            $data = $this->request->post();
            try {
                $this->validate($data, 'AuthGroup');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $result = AuthGroup::create($data);
            if ($result) {
                $this->success(lang('add success'), 'Auth/adminGroup');
            } else {
                $this->error(lang('add fail'));
            }

        } else {
            $view = [
                'info' => null
            ];
            View::assign($view);
            return view('group_add');
        }
    }

    // 用户组修改
    public function groupEdit()
    {
        if (Request::isPost()) {
            $data = $this->request->post();
            if($data['id']==1){
                $this->error(lang('supper man cannot edit'));
            }
            try{
                $this->validate($data, 'AuthGroup');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            $where['id'] = $data['id'];
            $res = AuthGroup::update($data, $where);
            if($res){

                $this->success(lang('edit success'), url('sys.Auth/group'));
            }else{
                $this->error(lang('edit fail'));

            }

        } else {
            $id = Request::param('id');
            $info = AuthGroup::find(['id' => $id]);
            $view = [
                'info' => $info,
                'title' => lang('edit')
            ];
            View::assign($view);
            return view();
        }
    }

    // 用户组状态修改
    public function groupState()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $info = AuthGroup::find($id);
            $info->status = $info['status'] == 1 ? 0 : 1;
//            if($this->uid==3){
//                $this->error(lang('test data cannot edit'));
//            }
            $info->save();
            $this->success(lang('edit success'));

        }
    }

    // 用户组批量删除
    public function groupSelectDel()
    {
        $id = $this->request->post('id');
        if ($id > 1) {
            AuthGroup::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('delete fail'));
        }
    }

    // 用户组显示权限
    public function groupAccess()
    {
        $list = Cache::get('AuthChecked');
        if(!$list){
            $admin_rule = AuthRule::field('id, pid, title')
                ->where('status',1)
                ->order('sort asc')->cache(3600)
                ->select()->toArray();
            $rules = AuthGroup::where('id', Request::param('id'))
                ->where('status',1)->cache(3600)
                ->value('rules');
            $list = TreeHelper::authChecked($admin_rule, $pid = 0, $rules);
            Cache::set('AuthChecked',$list,3600);

        }
        $group_id = Request::param('id');
        $idList = AuthRule::column('id');
        sort($idList);
        $view = [
            'list' => $list,
            'idList' => $idList,
            'group_id' => $group_id,
        ];
        View::assign($view);
        return view('group_access');
    }
    // 用户组保存权限
    public function groupSetaccess()
    {
        $rules = $this->request->post('rules');
        if (empty($rules)) {
            $this->error(lang('please choose rule'));
        }
        $data = $this->request->post();
        $rules = TreeHelper::authNormal($rules);
        $rls = '';
        foreach ($rules as $k=>$v){
            $rls.=$v['id'].',';
        }
        $where['id'] = $data['group_id'];
        $where['rules'] = $rls;

        if (AuthGroup::update($where)) {
            $admin = Session::get('admin');
            $admin['rules'] = $rls;
            session('admin', $admin);

            $this->success(lang('rule assign success'),url('sys.Auth/group'));
        } else {
            $this->error(lang('rule assign fail'));
        }
    }



}