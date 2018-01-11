<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/29
 * Time: 22:54
 */

namespace app\api\service;


use app\api\model\User;
use app\lib\exception\OrderException;
use think\Log;

class DeliveryMessage extends WxMessage
{
    const DELIVERY_MSG_ID1 = 'IkeU-15IAj1wDl-Dvwz32JzS8QCsorz9UTgaIYXpCpE';// 小程序模板消息ID号
    const DELIVERY_MSG_ID2 = 'yVKRvVIkiz6KCLWSa3wf1AHMhKeOqoNsPClqT1TL8Ko';// 小程序模板消息ID号
    const DELIVERY_MSG_ID3 = '9By46PeFpTqbtGGSsK6V_1Mt8tKRstBEY52m735egoE';// 小程序模板消息ID号
    const DELIVERY_MSG_ID4 = 'MycKHXn_22KWrKv43Q1egXZ93kdwGy7tl9PPGsQDNmk';// 小程序模板消息(新订单)ID号
    const DELIVERY_MSG_ID5 = 'IkeU-15IAj1wDl-Dvwz32JzS8QCsorz9UTgaIYXpCpE';// 小程序模板消息ID号

    //    private $productName;
    //    private $devliveryTime;
    //    private $IntegralItem

    /**
     * @param $IntegralItem
     * @param string $tplJumpPage
     * @param string $msgtype
     * @return bool
     * @throws OrderException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function sendDeliveryMessage($IntegralItem, $tplJumpPage = '',$msgtype = '')
    {

        if (!$IntegralItem) {
            throw new OrderException();
        }
        $user = User::get($IntegralItem->uid);

        //是否有模板ID
        if(input('form_id')){
            $this->formID = input('form_id');
        }else{
            $IntegralItem->OrderNum =  $IntegralItem->pay_sn;    //
            $this->formID    =  $IntegralItem->prepay_id;
        }
        $this->page = $tplJumpPage;
        $this->prepareMessageData($IntegralItem,$user,$msgtype);
        //$this->emphasisKeyWord='keyword1.DATA';//放大消息
        return parent::sendMessage($user['openId']);
    }

    /**
     * 消息模板
     * @param $IntegralItem
     */
    private function prepareMessageData($IntegralItem,$user,$msgtype)
    {
        switch($msgtype){
            case 'tpl1':
                $data = $this->tp1($IntegralItem);
                $this->emphasisKeyWord='keyword1.DATA';//放大消息
                $this->tplID = self::DELIVERY_MSG_ID1;
                break;
            case 'tpl2':
                $data = $this->tp2($IntegralItem,$user);
                $this->emphasisKeyWord='keyword1.DATA';//放大消息
                $this->tplID = self::DELIVERY_MSG_ID2;
                break;
            case 'tpl3':
                $data = $this->tp3($IntegralItem,$user);
                $this->tplID = self::DELIVERY_MSG_ID3;
                break;
            case 'tpl4':
                $data = $this->tp4($IntegralItem,$user);
                $this->emphasisKeyWord='keyword1.DATA';//放大消息
                $this->tplID = self::DELIVERY_MSG_ID4;
                break;
            default :
                $data = $this->tp1($IntegralItem);
                $this->tplID = self::DELIVERY_MSG_ID1;
                break;
        }
        $this->data = $data;
    }

    /**
     * 充值模板
     * @param $IntegralItem
     * @return array
     */
    private function tp1($IntegralItem){

        return   [
            'keyword1' => [
                'value' => $IntegralItem->points,
            ],
            'keyword2' => [
                'value' => $IntegralItem->OrderNum,
                'color' => '#27408B'
            ],
            'keyword3' => [
                'value' => $IntegralItem->direction,
            ]
            ,
            'keyword4' => [
                'value' => '待支付',
            ],
            'keyword5' => [
                'value' => config('wx.wx_payTips')
            ]
        ];
    }

    /**
     * 充值成功通知
     * @param $IntegralItem
     * @param $user
     * @return array
     */
    private function tp2($IntegralItem,$user){
        $dt = new \DateTime();
        return   [
            'keyword1' => [
                'value' => ($IntegralItem->points)*0.10,
            ],
            'keyword2' => [
                'value' => $IntegralItem->OrderNum,
                'color' => '#27408B'
            ],
            'keyword3' => [
                'value' => $IntegralItem->direction,
            ]
            ,
            'keyword4' => [
                'value' => $dt->format("Y-m-d H:i"),
            ],
            'keyword5' => [
                'value' => $user->nickName.'('.$user->nickName.')',
            ],
            'keyword6' => [
                'value' => config('wx.wx_payTips')
            ]
        ];
    }
    //下单模板
    //资源概要
    //{{keyword1.DATA}}
    //发布状态
    //{{keyword2.DATA}}
    //用户名
    //{{keyword3.DATA}}
    //信息分类
    //{{keyword4.DATA}}
    //发布时间
    //{{keyword5.DATA}}
    private function tp3($IntegralItem,$user){

        $dt = new \DateTime();
        return   [
            'keyword1' => [
                'value' => $IntegralItem->address,
            ],
            'keyword2' => [
                'value' => '发布成功',
                'color' => '#27408B'
            ],
            'keyword3' => [
                'value' => $user->name,
            ]
            ,
            'keyword4' => [
                'value' => '取快递',
            ],
            'keyword5' => [
                'value' => $dt->format("Y-m-d H:i")
            ]
        ];
    }
    //订单总价
    //{{keyword1.DATA}}
    //订单号
    //{{keyword2.DATA}}
    //订单类型
    //{{keyword3.DATA}}
    //收货人
    //{{keyword4.DATA}}
    //收货地址
    //{{keyword5.DATA}}
    //备注
    //{{keyword6.DATA}}
    private function tp4($IntegralItem,$user){

        return   [
            'keyword1' => [
                'value' => $IntegralItem->price,
            ],
            'keyword2' => [
                'value' => $IntegralItem->OrderNum,
                'color' => '#27408B'
            ],
            'keyword3' => [
                'value' => '取快递',
            ]
            ,
            'keyword4' => [
                'value' => $user->name,
            ],

            'keyword5' => [
                'value' => $IntegralItem->title,
            ],
            'keyword6' => [
                'value' => '接单了请注意查看交接时间噢~信用挂钩噢~！'
            ]
        ];
    }
}