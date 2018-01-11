<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/26
 * Time: 16:02
 */

namespace app\api\service;


use app\api\model\IntegralItem;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use think\Log;

//Loader::import('WxPay.WxPay', EXTEND_PATH, '.Data.php');
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');


class Pay
{
    private $pay_Sn;  //支付单号

    /**
     * Pay constructor.
     * @param $pay_Sn
     * @throws Exception
     */
    function __construct($pay_Sn)
    {
        if (!$pay_Sn)
        {
            throw new Exception('支付单号不允许为NULL');
        }
        $this->pay_Sn = $pay_Sn;
    }

    /**
     * 支付入口
     * @return array|bool
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     */
    public function pay()
    {

        $this->checkOrderValid();  //检查状态
        $order = new IntegralItem();
        $res = $order->checkOrder($this->pay_Sn);//返回订单信息
        if ($res['status'] == 1) {
            return false;
        }else{
            //传入数据
            return $this->makeWxPreOrder($res['uid'],$res['points']);
        }
    }

    /**
     * 构建微信支付订单信息
     * @param $uid
     * @param $totalPrice
     * @return array
     * @throws Exception
     * @throws TokenException
     */
    private function makeWxPreOrder($uid,$totalPrice)
    {
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid)
        {
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->pay_Sn);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * config('wx.Rate'));//
        $wxOrderData->SetBody(config('wx.wx_payName').'-'.$uid);
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));//支付回调地址

        return $this->getPaySignature($wxOrderData);
    }

    //向微信请求订单号并生成签名
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        // 失败时不会返回result_code
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
            //throw new Exception('获取预支付订单失败');
        }
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function recordPreOrder($wxOrder){
        // 必须是update，每次用户取消支付后再次对同一订单支付，prepay_id是不同的
        (new IntegralItem())->where(['pay_sn'=>$this->pay_Sn])
            ->update([
                'prepay_id' => $wxOrder['prepay_id']
            ]);
    }

    /**
     * 支付签名
     * @param $wxOrder
     * @return array
     */
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

    /**
     * 支付前检查
     * @return bool
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkOrderValid()
    {
        $order = IntegralItem::where('pay_sn', '=', $this->pay_Sn)
            ->find();
        if (!$order)
        {
            throw new OrderException();
        }
        if(!Token::isValidOperate($order->uid))
        {
            throw new TokenException(
                [
                    'msg' => '订单与用户不匹配',
                    'errorCode' => 10003
                ]);
        }
        if($order->status == 1){
            throw new OrderException([
                'msg' => '订单已支付过啦',
                'errorCode' => 80003,
                'code' => 400
            ]);
        };
        return true;
    }
}