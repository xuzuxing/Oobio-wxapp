<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 21:02
 */

namespace app\api\model;


class IntegralItem extends BaseModel
{
    /**
     * @param $pay_sn
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkOrder($pay_sn){
            $data = $this->where('pay_sn',$pay_sn)
                ->find();
            return $data;
    }
}