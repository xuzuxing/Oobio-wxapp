<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 21:01
 */

namespace app\api\model;


class Integral extends BaseModel
{
    /**
     * 积分记录
     * @return mixed
     */
    public function record()
    {
        return $this->hasMany('IntegralItem', 'pid', 'id')
            ->field('id,pid,pay_sn,direction,points,type,create_time');
    }

    /**
     * 获取用户积分信息
     * @param $uid
     * @return mixed
     */
    public static function getPoinsts($uid){

//        $page_count = 20;
//        $limit = (1-1)*$page_count;
//        $where['i.uid'] = $uid;
//        $data = self::table('__INTEGRAL__ m')
//            ->field('m.*,i.pid,i.pay_sn,i.direction,i.points,i.type,i.create_time')
//            ->join('__INTEGRAL_ITEM__ i ',' m.id=i.pid','LEFT')
//            ->where($where)
//            ->limit($limit,$page_count)
//            ->order('i.create_time desc')
//            ->select();
//        return $data;
        $data = self::where('uid',$uid)
            ->with('record')
            ->find();
        unset($data->prepay_id);
        return $data;
    }
    public static function setPoinsts($uid){

    }
    /**
     * 充值流程
     * 1.传入充值金额
     * 2.查询用户信息资金
     * 3.调用支付接口
     * 4.结果
     */
    public static function Recharge($uid,$val){
        $pid = self::where('uid',$uid)->find();

        $pay_sn = makeOrderNo($uid);
        $model = new IntegralItem();
        $model->pay_sn      = $pay_sn;
        $model->pid         = $pid->id;
        $model->uid         = $uid;
        $model->points      = $val;
        $model->direction   = "微信充值".$val."积分";
        $model->type        = "1";
        $model->save();

        return $model;
    }

    public function updatePay($id){
        $model = new IntegralItem();
        $model->where('id',$id)->update(['ststus'=>1]);
    }
}