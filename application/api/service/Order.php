<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/10/27
 * Time: 13:45
 */
namespace app\api\service;


use app\api\model\Images;
use app\api\model\Info as InfoModel;
use app\api\model\Demand;
use app\api\model\Order as OrderModel;
use think\Exception;

/**
 * 订单类
 * 下单流程
 * 1.用户下单
 * 2.检测是否已被接单
 * 3.创建订单数据
 * 4.结单支付
 */
class Order
{

    protected $oInfos;//下单信息
    protected $infos;//需求信息
    protected $uid;//接单用户ID

    /**
     * 下单
     * @param int $uid 用户id
     * @param array $oInfos 订单商品列表
     * @return array 订单商品状态
     * @throws Exception
     */
    public function place($uid, $oInfos)
    {
        $this->oInfos = $oInfos;
        $this->uid = $uid;
        //查询该需求基本信息
        $this->infos = $this->getInfoByOrder( $this->oInfos['iid']);
        //判断订单状态
        if ($this->infos['status'] == 2){
            return false;
        }else{
            $id = self::createOrderByTrans();
            //完成下单，下发站内消息
            msg('notice',$this->infos['uid'],'10000',"您的需求已被".$this->oInfos['name']."接单了");
            //返回订单ID
            return $id;
        }
    }

    public function deal($uid,$key){
        $order = new OrderModel();

        $where['puid'] = $uid;
        $where['order_key'] = $key;

        $res = $order->where($where)->find();
        if($res == 'NULL'){
            return false;
        }else{
            //修改订单状态
            $order->where($where)->update(['status'=> 2]);
            //修改需求状态
            $i = new Demand();
            $i->where('id',$res['iid'])->update(['status' => 3]);
            return $res['id'];
        }

    }
    // 根据订单查找需求信息
    private function getInfoByOrder($iid)
    {
        // 为了避免循环查询数据库
        $data = (new Demand())->where('id',$iid)
            ->field('uid,name,address,price,status,remark')
            ->find();
        return $data;
    }

    /**
     * 创建订单
     * @return array
     * @throws Exception
     */
    private function createOrderByTrans()
    {
        try {
            //订单信息
            $order = new OrderModel();
            $orderKey = $this->makeOrderKey($order);
            $order->OrderNum    = makeOrderNo();   //订单号
            $order->iid         = $this->oInfos['iid'];     //需求信息ID
            $order->uid         = $this->oInfos['uid'];     //下单用户ID
            $order->puid        = $this->infos['uid'];    //需求用户ID
            $order->title       = $this->infos['address'];   //需求交接位置
            $order->price       = $this->infos['price'];   //佣金价格
            $order->order_key      = $orderKey;  //结单key
            $order->create_time = time();               //时间
            $order->save();
            //修改需求状态为已接单
            $i = new Demand();
            $i->where('id',$order->iid)->update(['status'=> 2]);

            //完成下单，下发模板消息
            (new DeliveryMessage())->sendDeliveryMessage($order,'/pages/order/details?id='. $order->id);
            //返回订单ID
            return $order->id;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    /**
     * 结单Key
     * 包含需求用户ID 订单号
     * 验证过程
     * 1.扫码传入uid  key  MD5加密
     * 2.验证比对数据库
     * 3.结果
     * @param $order
     * @return string
     */
    private function makeOrderKey($order){
        $key = $order->OrderNum;
        $uid = $order->uid;
        return md5($key.$uid);
    }
}