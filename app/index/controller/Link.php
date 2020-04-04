<?php
/**
 * Created by IntelliJ IDEA.
 * User: fan
 * Date: 2019/11/14
 * Time: 下午 08:59
 */

namespace app\index\controller;


use app\common\controller\Frontend;
use think\facade\Request;
use think\facade\View;

class Link extends Frontend
{

    function  index(){
        return View::fetch();
    }

    function  clazz(){
        if (Request::isPost()){
            $pid = Request::post('pid','','trim');

            $goods= \app\common\model\Goods::getList(['pid'=>$pid],0,[]);
            if ($goods==null) {
                return "商品不存在";
            }else{
                $this->success(1,'',$goods);
            }
        }else{
            $id = Request::get('pid');

            View::assign('pid',$id);
            return View::fetch();
        }




    }





    function  detail(){
        if (Request::isPost()){
            $id = Request::post('id','','trim');

            $goods= \app\common\model\Goods::getOne($id);
            if ($goods==null) {
                return "商品不存在";
            }else{
                $this->success(1,'',$goods);
            }
        }else{
            $id = Request::get('id');

            View::assign('id',$id);
            return View::fetch();
        }




    }
}