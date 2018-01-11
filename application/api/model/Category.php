<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/24
 * Time: 22:45
 */

namespace app\api\model;


class Category extends BaseModel
{
    /**
     * 关联子分类
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('CategoryItem', 'pid', 'id');
    }

    /**
     * 关联图片
     * @return \think\model\relation\BelongsTo
     */
    public function img()
    {
        return $this->belongsTo('Images', 'img_id', 'id');
    }

    public static function getCategories($ids)
    {
        $categories = self::with('items')
            ->with('items.img')
            ->select($ids);
        return $categories;
    }

    /**
     * XX分类
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getCategory($id)
    {
        $category = self::with(['items','img'])
            ->with('items.img')//子分类
            ->find($id);
        return $category;
    }
}