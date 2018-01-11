<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/29
 * Time: 19:39
 */

namespace app\api\model;


class Order extends BaseModel
{
    /**
     * 关联二维码
     * @return mixed
     */
    public function img()
    {
        return $this->belongsTo('Images', 'closing_qrcode', 'id');
    }
    /**
     * 获取用户所有订单,关联需求信息,以及需求用户信息
     * @param $uid
     * @return mixed
     */
    public function getOrderListByUser($uid){
        $page = input('page','1');
        $page_count = 20;
        $limit = ($page-1)*$page_count;
        $lists =  $this->alias('a')
            ->where('a.uid' ,$uid)
            ->field('a.*,b.name,b.phone,b.remark,b.address,b.initial_time,b.end_time')
            ->join('__DEMAND__ b','a.iid = b.id','LEFT')
            ->limit($limit,$page_count)
            ->order('a.create_time desc')
            ->select();
        $lists = TimeToDate($lists);
        return $lists;
    }

    /**
     * 获取详细信息
     * @param $id
     * @return mixed
     */
    public function getOrderDetailById($id){
//        return self::where('id',$id)
//            ->with(['img','info'])
//            ->find();
        $lists =  $this->alias('a')
            ->where('a.id' ,$id)
            ->field('a.*,b.name,b.phone,b.remark,b.address,b.initial_time,b.end_time,c.url')
            ->join('__DEMAND__ b','a.iid = b.id','LEFT')
            ->join('__IMAGES__ c','a.closing_qrcode = c.id','LEFT')
            ->order('a.create_time desc')
            ->find();
        $lists['end_time']      = date('H:i',$lists['end_time']);
        $lists['initial_time']  = date('H:i',$lists['initial_time']);
        $lists['url']  = config('wx.img_prefix').$lists['url'];
        return $lists;
    }
}