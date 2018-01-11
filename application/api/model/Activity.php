<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 18:21
 */

namespace app\api\model;


class Activity extends BaseModel
{
    public function img()
    {
        return $this->belongsTo('Images', 'pic_url', 'id');
    }
    public function getRecent($page){

        //页数
        $page_count = 20;
        $limit = ($page-1)*$page_count;

        $data = $this->with('img')
            ->limit($limit,$page_count)
            ->order('create_time desc')
            ->select();
        return $data;
    }

}