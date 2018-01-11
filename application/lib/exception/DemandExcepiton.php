<?php
/**
 * Created by PhpStorm.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/10/3
 * Time: 13:23
 */

namespace app\lib\exception;


class DemandExcepiton extends BaseException
{
    public $code = 404;
    public $msg = '指定需求不存在，请检查需求ID';
    public $errorCode = 20000;
}