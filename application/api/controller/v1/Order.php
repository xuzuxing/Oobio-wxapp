<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/29
 * Time: 22:54
 */

namespace app\api\controller\v1;


use app\api\controller\Base;

use app\api\model\Order as OrderModel;

use app\api\service\Order as OrderService;
use app\api\service\Token;

use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;

use app\lib\exception\MissException;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;

class Order extends Base
{
    protected $uid;

    public function __construct()
    {
        //获取全局uid,这里就是可以进行token验证了
        $uid = Token::getCurrentUid();
        $this->uid = $uid;
    }

    /**
     * 下单操作
     * @url /order
     * @HTTP POST
     */
    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
        $order = new OrderService();
        $data = $order->place($this->uid,input('post.'));
        if(!$data){
            throw new MissException([
                'msg'   => '订单生成错误'
            ]);
        }
        return show(1, '接单成功',$data);

    }

    /**
     * 扫码成交
     */
    public function DealOrder(){
        $order = new OrderService();
        $data = $order->deal($this->uid,input('post.key'));
        if(!$data){
            return show(0,'结单异常',$data);
        }
        return show(1, '结单成功',$data);

    }

    /**
     * 获取订单详情
     */
    public function getDetailById()
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = (new OrderModel())
            ->getOrderDetailById(input('get.id'));
        if (!$orderDetail)
        {
            return show(0, '获取订单失败',$orderDetail);
        }
        return show(1, '获取订单成功',$orderDetail);
    }

    /**
     * 未完成订单数目
     * @url /mycount
     * @return array
     */
    public function getCountsByUser(){

        $uid = Token::getCurrentUid();

        $data = [
            'uid'       => $uid,
            'status'    => 1
        ];
        $data = (new OrderModel())
            ->where($data)
            ->count();
        if(!$data){
            return show(0, '请求数据不存在',  $data);
        }
        return show(1, '获取成功',  $data);
    }

    /**
     * 获取用户订单列表
     * @return array
     */
    public function getOrdersByUser(){

        $uid = Token::getCurrentUid();
        $data = (new OrderModel())
            ->getOrderListByUser($uid);

        if(!$data){
            return show(0, '暂无订单',  $data);
        }

        return show(1, '获取成功',  $data);
    }

    /**
     * 根据用户id分页获取订单列表（简要信息）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByUser($page = 1, $size = 15)
    {
        (new PagingParameter())->goCheck();
        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
//        $collection = collection($pagingOrders->items());
//        $data = $collection->hidden(['snap_items', 'snap_address'])
//            ->toArray();
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];

    }

    /**
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($page=1, $size = 20){
        (new PagingParameter())->goCheck();
//        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    public function delivery($id){
        (new IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id);
        if($success){
            return new SuccessMessage();
        }
    }
}