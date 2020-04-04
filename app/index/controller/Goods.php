<?php
/**
 * Created by IntelliJ IDEA.
 * User: fan
 * Date: 2019/11/14
 * Time: 下午 08:59
 */

namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsSku as GoodsSkuModel;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;

class Goods extends Frontend
{

    public function buy($pid)
    {
        if (Request::isPost()) {
          //  $pid = Request::post('pid', '', 'trim');

            $goods = \app\common\model\PayChannel::getList(['status' => 1,'pid'=>$pid], 0, []);
            if ($goods == null) {
                return "不能支付不存在";
            } else {
                $this->success(1, '', $goods);
            }
        } else {
           // $id = Request::get('pid');

            View::assign('pid', $pid);
            return View::fetch();
        }
    }

    function index()
    {
        return View::fetch();
    }

    function clazz()
    {
        if (Request::isPost()) {
            $pid = Request::post('pid', '', 'trim');
            if ($pid == -1) {
                $goods =
                    Db::name('goods')->alias('goods')
                        ->join('goods_clazz clazz' ,'clazz.id=goods.pid')
                        ->where(['goods.status' => 1])
                        ->field('goods.unit,goods.id,goods.title,goods.description,goods.price,clazz.title clazzTitle')
                        ->select();
            } else {
                $goods =  Db::name('goods')->alias('goods')
                    ->join('goods_clazz clazz' ,'clazz.id=goods.pid')
                    ->where(['goods.status' => 1,'goods.pid'=>$pid])
                    ->field('goods.unit,goods.id,goods.title,goods.description,goods.price,clazz.title clazzTitle')
                    ->select();
            }

            if ($goods == null) {
                return "商品不存在";
            } else {
                $this->success(1, '', $goods);
            }
        } else {
            $id = Request::get('pid');

            View::assign('pid', $id);
            return View::fetch();
        }


    }


    function detail($id)
    {
        if (Request::isPost()) {
          //  $id = Request::post('id', '', 'trim');

            $goods =  Db::name('goods')->alias('goods')
                ->join('goods_clazz clazz' ,'clazz.id=goods.pid')
                ->where(['goods.status' => 1,'goods.id'=>$id])
                ->field('goods.thumb,goods.stock,goods.content,goods.unit,goods.id,goods.title,goods.description,goods.price,clazz.title clazzTitle')
                ->find();


            if ($goods == null) {
                return "商品不存在";
            } else {
                $this->success(1, '', $goods);
            }
        } else {
         //   $id = Request::get('id');

            $article=GoodsModel::getOne($id);

            $config=getConfig();
//            var_dump($article);
            $article->getModel();

            $config['title']=$article['title'];

            $config['site_seo_desc']=$article['description'];
            $config['site_seo_keywords']=$article['keywords'];
//            $config['site_name']=$article['description'];
            \think\facade\View::assign('config', $config);


            View::assign('id', $id);
            return View::fetch();
        }
    }



    function getSku($id)
    {
        if (Request::isPost()) {
            //  $id = Request::post('id', '', 'trim');

            $goods =  Db::name('goods_sku')->alias('sku')
                 ->where(['sku.status' => 1,'sku.goodsId'=>$id])
                ->field('sku.*')
                ->select();


            if ($goods == null) {
                return "商品不存在";
            } else {
                $this->success(1, '', $goods);
            }
        } else {
            //   $id = Request::get('id');

            $article=GoodsModel::getOne($id);

            $config=getConfig();
//            var_dump($article);
            $article->getModel();

            $config['title']=$article['title'];

            $config['site_seo_desc']=$article['description'];
            $config['site_seo_keywords']=$article['keywords'];
//            $config['site_name']=$article['description'];
            \think\facade\View::assign('config', $config);


            View::assign('id', $id);
            return View::fetch();
        }
    }



}