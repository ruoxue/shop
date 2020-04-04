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

use app\common\controller\Backend;
use app\common\model\AdvPosition;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use app\common\model\Adv as AdvModel;
class Adv extends Backend {

    public function initialize(){
        parent::initialize();
    }
    /*-----------------------广告管理----------------------*/
    // 广告列表
    public function index()
    {
        if(Request::isPost()){
            $keys = Request::post('keys','','trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list=Db::name('adv')->alias('a')
                ->join('adv_position ap','a.pid = ap.id','left')
                ->field('a.*,ap.position_name,ap.position_desc')
                ->where('a.ad_name','like','%'.$keys.'%')
                ->order('a.sort desc,a.id desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list['data'],'count'=>$list['total']];
        }

        return View::fetch();
    }

    // 广告添加
    public function add()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try{
                $this->validate($data, 'Adv');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            if($data['time']){
                $time = explode(' - ',$data['time']);
                $data['start_time'] = strtotime($time[0]);
                $data['end_time'] = strtotime($time[1]);
            }else{
                $data['start_time'] = '';
                $data['end_time'] = '';
            }

            //添加
            $result = AdvModel::create($data);
            if ($result) {
                $this->success(lang('add success'), url('index'));
            } else {
                $this->error(lang('add fail'));
            }
        } else {
            $info = '';
            $posGroup = AdvPosition::where('status', 1)->select();
            $view = [
                'info'  =>$info,
                'posGroup' => $posGroup,
                'title' => lang('add'),
            ];
            View::assign($view);
            return View::fetch();
        }
    }
    /**
     * 广告修改
     */
    public function edit()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try{
                $this->validate($data, 'Adv');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            AdvModel::update($data);
            $this->success(lang('edit success'), url('index'));

        } else {
            $id = Request::param('id');
            if ($id) {
                $posGroup = AdvPosition::where('status', 1)->select();
                $info = AdvModel::find($id);
                $info['time'] = date('Y-m-d',$info['start_time']).' - '.date('Y-m-d',$info['end_time']);
                $view = [
                    'info' => $info,
                    'posGroup' => $posGroup,
                    'title' => '编辑',
                ];
                View::assign($view);
                return View::fetch('add');
            }
        }
    }


    // 广告删除
    public function delete()
    {
        $id = Request::post('id');
        AdvModel::destroy($id);
        $this->success(lang('delete success'));

    }



    // 广告状态修改
    public function state()
    {
        if (Request::isPost()) {
            $id = Request::post('id');
            if (empty($id)) {
                $this->error('id'.lang('not exist'));
            }
            $adv = AdvModel::find($id);
            $status = $adv['status'] == 1 ? 0 : 1;
            $adv->status = $status;
            $adv->save();
            $this->success(lang('edit success'));
        }
    }


    /*-----------------------广告位置管理----------------------*/

    // 广告位置管理
    public function pos()
    {
        if(Request::isPost()){
            //条件筛选
            $keys = Request::param('keys');

            //查出所有数据
            $list = AdvPosition::where('position_name','like','%'.$keys.'%')
                ->order('id desc')
                ->paginate(
                    $this->pageSize, false,
                    ['query' => Request::param()]
                )->toArray();
            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list['data'],'count'=>$list['total']];

        }


        return View::fetch();

    }



    // 广告位置添加
    public function posAdd()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try {
                $this->validate($data, 'AdvPosition');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $result = AdvPosition::create($data);
            if ($result) {
                $this->success(lang('add  success'), url('pos'));
            } else {
                $this->error(lang('add fail'));
            }

        } else {
            $view = [
                'info' => null,
                'title' => lang('add')
            ];
            View::assign($view);
            return View::fetch('pos_add');
        }
    }

    // 广告位置修改
    public function posEdit()
    {
        if (Request::isPost()) {
            $data = Request::post();

            try{
                $this->validate($data, 'AdvPosition');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            $where['id'] = $data['id'];
            $res = AdvPosition::update($data, $where);
            if($res){

                $this->success(lang('edit success'), url('pos'));
            }else{
                $this->error(lang('edit fail'));

            }

        } else {
            $id = Request::param('id');
            $info = AdvPosition::find(['id' => $id]);
            $view = [
                'info' => $info,
                'title' => lang('edit')
            ];
            View::assign($view);
            return View::fetch('pos_add');
        }
    }

    // 广告位置状态修改
    public function posState()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $info = AdvPosition::find($id);
            $info->status = $info['status'] == 1 ? 0 : 1;
            $info->save();
            $this->success(lang('edit success'));

        }
    }
    // 广告位置删除
    public function posDel()
    {
        $id = Request::post('id');

        AdvPosition::destroy($id);
        $this->success(lang('delete success'));


    }

   }