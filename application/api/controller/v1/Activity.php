<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 18:20
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\model\Activity as ActivityModel;

class Activity extends Base
{
    
    public function getAbstract($page = '1'){
        $data = (new ActivityModel())
            ->getRecent($page);
        if(!$data){
            return show(0, '没有最新活动:(',  $data);
        }

        return show(1, '获取成功',  $data);
    }

}