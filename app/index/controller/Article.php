<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use think\facade\Request;
use think\facade\View;
use  app\common\model\Article as ArticleModel;

class Article extends Frontend
{

    public function buy()
    {
        if (Request::isPost()) {
            $pid = Request::post('pid', '', 'trim');

            $goods = \app\common\model\PayChannel::getList(['status' => 1, 'pid' => $pid], 0, []);
            if ($goods == null) {
                return "不能支付不存在";
            } else {
                $this->success(1, '', $goods);
            }
        } else {
            $id = Request::get('pid');

            View::assign('pid', $id);
            return View::fetch();
        }
    }

    function index($id)
    {
        if (Request::isPost()) {
            $article=ArticleModel::getOne($id);
//            dump(html_entity_decode($article['content']));
            $this->success(1, '', $article);
        } else {

            $article=ArticleModel::getOne($id);
            View::assign('pid', $id);
            $config=getConfig();
//            var_dump($article);
            $article->getModel();

            $config['title']=$article['title'];

            $config['site_seo_desc']=$article['description'];
            $config['site_seo_keywords']=$article['keywords'];
//            $config['site_name']=$article['description'];
            \think\facade\View::assign('config', $config);

            return View::fetch();
        }
    }
}