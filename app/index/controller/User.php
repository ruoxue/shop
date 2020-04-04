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

namespace app\index\controller;


use app\common\controller\Privates;
use app\common\model\Address as AddressModel;


use app\common\model\Area as AreaModel;
use app\common\model\User as UserModel;

use think\facade\Db;
use think\facade\Request;
use think\facade\View;

class User extends Privates
{

    public function index()
    {
        return view();
    }


    public function getUser()
    {
    }

    public function center()
    {

        if (Request::isPost()) {

            $userId = session('user.id');

            $user = UserModel::where(['id' => $userId])->find();
            unset($user['password']);
            unset($user['paypwd']);
            unset($user['token']);
            $this->success('', '', $user);


        } else {
            return view();
        }

    }


    public function order($status = 0)
    {

        if (Request::isPost()) {

            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('order')
                ->alias('order')
                ->join('goods_sku sku', 'sku.id=order.skuId')
                ->field('order.*,sku.title skuName')
                ->where(['order.uid' => session('user.id')])
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();

            return $result = ['code' => 0, 'msg' => lang('get info success'),
                'data' => $list['data'], 'count' => $list['total']];


        } else {
            return view();
        }

    }

    public function address($status = 0)
    {

        if (Request::isPost()) {

            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('address')
                ->alias('address')
                ->join('area area', 'address.areaId=area.id')
                ->field('address.*,area.name areaName')
                ->where(['address.uid' => session('user.id')])
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();

            return $result = ['code' => 0, 'msg' => lang('get info success'),
                'data' => $list['data'], 'count' => $list['total']];


        } else {
            return view();
        }

    }


    public function addAddress()
    {
        $data['uid'] = $this->uid;
        if (Request::isPost()) {
            $data = Request::post();

            $res = AddressModel::create($data);
            if ($res) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }

        } else {
            $info['province']=0;
            $info['areaId']=-1;
            $info['city']=0;
            $view = [
                'info' => $info,

                'title' => lang('add'),
            ];
            View::assign($view);
            return view('add_address');
        }
    }


    public function editAddress()
    {

        if (Request::isPost()) {
            $data = Request::post();
            $res = AddressModel::update($data);
            if ($res) {
                $this->success(lang('add success'),url('address'));
            } else {
                $this->error(lang('add fail'));

            }

        } else {
            $info = AddressModel::getOne(input('id'));
            $area= AreaModel::getOne($info['areaId']);
            $info['city']=$area['pid'];
            $city= AreaModel::getOne( $info['city']);
            $info['province']=$city['pid'];
            $view = [
                'info' => $info,

                'title' => lang('add'),
            ];
            View::assign($view);
            return view('add_address');
        }
    }


    public  function  getArea($pid=0){
       $list= Db::name('area')->where(['status'=>1,'pid'=>$pid])->select();
        if ($list) {
            $this->success(lang('add success'),'',$list);
        } else {
            $this->error(lang('add fail'));

        }
    }


}