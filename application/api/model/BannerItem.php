<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 0:12
 */

namespace app\api\model;


class BannerItem extends BaseModel
{
    protected $hidden = ['pid', 'img_id', 'banner_id', 'delete_time'];

    public function img()
    {
        return $this->belongsTo('Images', 'img_id', 'id');
    }

}