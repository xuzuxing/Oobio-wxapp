<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/24
 * Time: 21:58
 */

namespace app\api\model;


class User extends BaseModel
{
    protected $autoWriteTimestamp = true;
    //    protected $createTime = ;

    public function orders()
    {
        return $this->hasMany('Order', 'user_id', 'id');
    }

    public function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }
    /**
     * 用户是否存在
     * 存在返回uid，不存在返回0
     */
    public static function getByOpenID($id)
    {
        $user = User::where('id = "'.$id.'" or openId ="'.$id.'"')
            ->field('id,avatarUrl,city,gender,nickName,province,county,phone,identity')
            ->find();
        return $user;
    }

//    /**
//     * 用户是否存在
//     * 存在返回uid，不存在返回0
//     */
//    public static function getByOpenID($openid)
//    {
//        $where = 'openId = '.$openid.'or id = '.$openid;
//        //$user = User::where('openid', '=', $openid)
//        $user = User::where($where)
//            ->find();
//        return $user;
//    }
}