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
 * Date: 2019/11/7
 */

namespace addons\pay;
// 注意命名空间规范
use think\Addons;

/**
 * 插件测试
 *
 */
class Plugin extends Addons    // 需继承think\Addons类
{
    // 该插件的基础信息
    public $info = [
        'name' => 'pay',    // 插件标识唯一
        'title' => '订单管理',    // 插件名称
        'description' => '900',    // 插件简介
        'status' => 1,    // 状态
        'author' => 'ruoxue',
        'require' => '0.2',
        'version' => '0.2',
        'website' => ''

    ];
    public $menu = [
        [
            'href' => 'admin/order',
            'title' => '支付管理',
            'status' => 1,
            'auth_open' => 1,
            'menu_status' => 1,
            'icon' => 'fa fa-pay',
            'menulist' => [
                [
                    'href' => 'admin/order.pay/index',
                    'title' => '支付记录',
                    'status' => 1,
                    'menu_status' => 1,
                    'icon' => 'fa fa-comments-o',
                    'menulist' => [
                        ['href' => 'admin/pay.pay/add', 'title' => '添加支付订单', 'status' => 1,
                            'menu_status' => 0,],
                        ['href' => 'admin/pay.pay/edit', 'title' => '编辑支付订单', 'status' => 1,
                            'menu_status' => 0,],
                        ['href' => 'admin/pay.pay/delete', 'title' => '删除支付订单', 'status' => 1,
                            'menu_status' => 0,],
                        ['href' => 'admin/pay.pay/state', 'title' => '状态', 'status' => 1,
                            'menu_status' => 0,],

                    ]
                ],
                [
                    'href' => 'admin/order.order/index',
                    'title' => '订单管理',
                    'status' => 1,
                    'menu_status' => 1,
                    'icon' => 'fa fa-comments-o',
                    'menulist' => [
                        ['href' => 'admin/pay.order/getWxAccount', 'title' => '商品订单', 'status' => 1,
                            'menu_status' => 0,],
                        ['href' => 'admin/pay.order/changeApp', 'title' => '商品订单', 'status' => 1,
                            'menu_status' => 0,],
                        ['href' => 'admin/pay.order/addWeixinMenu', 'title' => '添加菜单', 'status' => 1,
                            'menu_status' => 0,],
                        ['href' => 'admin/pay.order/updataWechatMenu', 'title' => '更新菜单', 'status' => 1,
                            'menu_status' => 0,],


                    ]
                ],

            ]
        ]
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {

        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 插件使用方法
     * @return bool
     */
    public function enabled()
    {

        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disabled()
    {
        return true;
    }


    /**
     * 实现的testhook钩子方法
     * @return mixed
     */
    public function testhook($param)
    {
        // 调用钩子时候的参数信息
        dump($param);
        // 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        dump($this->getInfo());
        dump($this->getConfig(true));
        // 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        return $this->fetch('info');
    }

}