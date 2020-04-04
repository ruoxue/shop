<?php


namespace app\admin\validate;


use think\Validate;

class Article extends validate
{
    protected $rule = [

        'content|广告图片' => [
            'require' => 'require',
        ],


    ];
}