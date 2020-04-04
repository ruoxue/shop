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
 * Date: 2019/9/2
 */

namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'level_id|会员等级' => [
            'require' => 'require',
        ],
        'email|邮箱账号' => [
            'require' => 'require',
            'min'     => '5',
            'max'     => '100',
            'unique'  => 'user',
        ],
        'mobile|联系电话' => [
            'unique'  => 'user',
        ],
    ];
}