<?php
/**
 * Created by 七月
 * Author: 七月
 * 微信公号: 小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/28
 * Time: 18:12
 */

namespace app\api\service;


use app\api\model\Demand;
use app\api\model\Integral;
use app\api\model\IntegralItem;
use app\api\model\Order;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

//Loader::import('WxPay.WxPay', EXTEND_PATH, '.Data.php');

/***
 *  WxNotify  微信消息回调
 * @package app\api\service
 */
class WxNotify extends \WxPayNotify
{
    //    protected $data = <<<EOD
    //<xml><appid><![CDATA[wxaaf1c852597e365b]]></appid>
    //<bank_type><![CDATA[CFT]]></bank_type>
    //<cash_fee><![CDATA[1]]></cash_fee>
    //<fee_type><![CDATA[CNY]]></fee_type>
    //<is_subscribe><![CDATA[N]]></is_subscribe>
    //<mch_id><![CDATA[1392378802]]></mch_id>
    //<nonce_str><![CDATA[k66j676kzd3tqq2sr3023ogeqrg4np9z]]></nonce_str>
    //<openid><![CDATA[ojID50G-cjUsFMJ0PjgDXt9iqoOo]]></openid>
    //<out_trade_no><![CDATA[A301089188132321]]></out_trade_no>
    //<result_code><![CDATA[SUCCESS]]></result_code>
    //<return_code><![CDATA[SUCCESS]]></return_code>
    //<sign><![CDATA[944E2F9AF80204201177B91CEADD5AEC]]></sign>
    //<time_end><![CDATA[20170301030852]]></time_end>
    //<total_fee>1</total_fee>
    //<trade_type><![CDATA[JSAPI]]></trade_type>
    //<transaction_id><![CDATA[4004312001201703011727741547]]></transaction_id>
    //</xml>
    //EOD;

    /**
     * 微信支付回调
     * @param array $data
     * @param string $msg
     * @return bool| //true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($data, &$msg)
    {

//        充值10积分返回数据包
//        array (
//        'appid' => 'wx1c32cda245563ee1',
//        'bank_type' => 'CFT',
//        'cash_fee' => '100',
//        'fee_type' => 'CNY',
//        'is_subscribe' => 'N',
//        'mch_id' => '1493758822',
//        'nonce_str' => 'ikhoeo1i9a113y17veser48md5ilh0ws',
//        'openid' => 'obw730EFiL3c42aZeC4FQ2P_s1WU',
//        'out_trade_no' => 'B11186085174580219',
//        'result_code' => 'SUCCESS',
//        'return_code' => 'SUCCESS',
//        'sign' => '4509D74E2C2839ECCD0890BD60E15FAC',
//        'time_end' => '20180111235451',
//        'total_fee' => '100',
//        'trade_type' => 'JSAPI',
//        'transaction_id' => '4200000060201801112350701734',
//)
        if ($data['result_code'] == 'SUCCESS') {

            $pay_sn     = $data['out_trade_no'];  //商户订单号
            $total_fee  = $data['total_fee'];//返回数据100为分 1元 = 10 积分 = 100分

            Db::startTrans();
            try {
                //查找积分数据表
                $IntegralgralItem = (new IntegralItem)->where(['pay_sn' => $pay_sn])
                    ->lock()
                    ->find();

                if ($IntegralgralItem->status == 0) {
                    //获取关联用户积分表
                    $service = new Integral();
                    $Integral = $service->where('id',$IntegralgralItem->pid)
                        ->find();
                    if ($Integral->id) {
                        $this->reduceIntegral($total_fee,$Integral->id);
                        $this->updateOrderStatus($IntegralgralItem->id, true);//更新支付状态
                        //下发消息通知
                        (new DeliveryMessage())->sendDeliveryMessage($IntegralgralItem,'pages/wallet/index?from=pay','tpl2');
                    } else {
                        $this->updateOrderStatus($IntegralgralItem->id, false);
                    }
                }
                Db::commit();
            } catch (Exception $ex) {
                Db::rollback();
                Log::error($ex);
                // 如果出现异常，向微信返回false，请求重新发送通知
                return false;
            }
        }
        return true;
        //已知支付，但是失败，还是需要向微信服务器返回true  不然微信会重复返回错误信息
    }


    /**
     * 更新用户积分数据
     * @param $total_fee
     * @param $id
     * @throws Exception
     */
    private function reduceIntegral($total_fee,$id)
    {
        //分值转换
        $total = $total_fee*0.1;
        (new Integral())->where(['id' => $id])
            ->setInc('total_points',$total);
    }

    /**
     * 更新支付单状态
     * @param $id
     * @param $success
     */
    private function updateOrderStatus($id, $success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID;
        (new IntegralItem)->where(['id' => $id])
            ->update([ 'status' => $status ]);


    }
}