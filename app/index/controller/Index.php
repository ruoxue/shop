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


use app\common\controller\Frontend;
use app\common\model\Adv;

use app\common\model\Clazz;
use app\common\model\GoodsClazz;
use think\facade\Db;
use think\facade\View;
use think\route\Domain;
use function Sodium\add;

class Index extends Frontend
{

    public function index()
    {

        return view();
    }


    /**
     * @return App
     * 1级分类
     */
    public function getNav($pid)
    {

        $navList = GoodsClazz::getList(['status' => 1, 'pid' => $pid],
            0, ['sort'=>'asc','id'=>'asc']);

        $this->success('1', '', $navList);

    }

    public function getAd()
    {

        $adList = Adv::getList([], 0, []);
        $this->success(1, '', $adList);
    }

    /**
     * 获取首页展示
     */
    public function getHomeGoods()
    {


        $homeGoods = Db::name('goods')->alias('goods')
            ->join('goods_clazz clazz' ,'clazz.id=goods.pid')
            ->where(['goods.status' => 1, 'home' => 1])
            ->field('goods.thumb,goods.unit,goods.id,goods.title,goods.description,goods.price,clazz.title clazzTitle')
            ->select();




        $this->success(1, '', $homeGoods);
    }

    /**
     *
     */
    public function getHomeClass()
    {

        $homeClass = GoodsClazz::getList(['pid' => array('neq', 0)], 10, []);
        $this->success(1, '', $homeClass);

    }

    public  function  getLink(){
        $homeClass = \app\common\model\Link::getList(['status' => 1], 10, []);
        $this->success(1, '', $homeClass);

    }

}