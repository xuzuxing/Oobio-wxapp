<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/24
 * Time: 22:47
 */

namespace app\api\validate;
/**
 * ID必须是整数
 * @package app\api\validate
 *
 */
class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];
}
