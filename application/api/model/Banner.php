<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 0:12
 */

namespace app\api\model;


class Banner extends BaseModel
{
    public function items()
    {
        return $this->hasMany('BannerItem', 'pid', 'id');
    }

    /**
     * @param $id int banner所在位置
     * @return Banner
     */
    public function getBannerById($id)
    {
        //$data = $this->alias('a')
        //->field('a.name,b.key_word,c.url')
        //->join('__BANNER_ITEM__ b ',' b.pid = a.id','LEFT')
        //->join('__IMAGES__ c',' c.id = b.img_id','LEFT')
        //->select();
        //return $data;
        $banner = $this->with([
            'items',
            'items.img'
        ])->find($id);

        return $banner;
    }

}