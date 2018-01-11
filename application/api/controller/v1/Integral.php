<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 21:14
 */

namespace app\api\controller\v1;
use app\api\controller\Base;
use app\api\service\Token;
use app\api\model\Integral as IntegralModel;
use app\api\service\WxNotify;

/**
 * Class Integral
 * @package app\api\controller\v1
 */
class Integral extends Base
{
    /**
     * 获取用户积分信息
     * @return array
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPointsByUser(){
        $uid = Token::getCurrentUid();
        $data = IntegralModel::getPoinsts($uid);
        if(!$data){
            return show(0,'查询失败',$data);
        }
        return show(1,'查询成功',$data);

    }

    /**
     * 账户充值入口
     * @param $val
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function RechargeForUser($val){
        $uid = Token::getCurrentUid();
        //先存入数据库生成支付记录
        //返回支付记录订单ID
        //进行支付
        $payOrder = IntegralModel::Recharge($uid,$val);//传入 Value
        $res = (new Pay())->getPreOrder($payOrder->pay_sn); //预订单支付
        if(!$res){
            return show(0,'订单已经支付');
        }else{
            return $res;  //返回的支付数据包一定要是string
        }
    }

    /**
     * 二次支付
     * @param $pay_sn
     * @return array
     */
    public function RechargeOnce($pay_sn){
        $res = (new Pay())->getPreOrder($pay_sn); //预订单支付
        if(!$res){
            return show(0,'订单已经支付');
        }else{
            return $res;  //返回的支付数据包一定要是string
        }
    }

}