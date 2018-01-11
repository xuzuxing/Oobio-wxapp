<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 20:17
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\service\Token;
use app\api\model\Message as MessageModel;
use app\lib\AliSms;
use app\lib\NotifyPush;
use think\Log;

/**
 * 消息处理类
 * @package app\api\controller\v1
 */
class Message extends Base {

    /**
     * 查询某类型消息
     * @param string $type
     * @return array
     */
    public function get($type = '',$page = '1'){
        $uid = Token::getCurrentUid();
        $data = (new MessageModel())
            ->getMsgTypeById($uid,$type,$page);
        if(!$data){
            return show(0,'没有更多消息可以查看了',$data);
        }
        return show(1,'消息加载成功',$data);
    }

    /**
     * 获取最新消息
     * @return array
     */
    public function getAll(){
        $uid = Token::getCurrentUid();
        $data = (new MessageModel())->getMsgAllById($uid);

        if(!$data){
            return show(0,'暂无最新消息',$data);
        }
        return show(1,'消息加载成功',$data);
    }


    /**
     * 天气接口测试
     */
    public function getWeather($city){
        $url = "http://www.sojson.com/open/api/weather/json.shtml?city=".$city;
        $data = curl_get($url);
        return json_decode($data);
    }


    /****************第三方消息************************/
    /**
     * 发送短信验证码
     * @param $phoneNumber
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function sendCodeBySms($phoneNumber){

        $signName = config('aliyun.signName');
        $templateCode = config('aliyun.templateCode');
        $code = getRandChar(6,'NUMBER');//生成验证码

        $templateParam = [
            'code'  => $code
        ];

        $ali = new AliSms();
        //短信模板
        $res = $ali->sendSms($signName, $templateCode , $phoneNumber, $templateParam, $outId = time().rand(99,999), $smsUpExtendCode = null
        );

        Log::record($res);
        if($res->Message !== 'OK'){
            return show(0,'发送失败','');
        }
        $uid = Token::getCurrentUid();
        //存入缓存做验证
        cache('verify_'.$uid,$code,300);
        return show(1,'获取短信验证码成功',cache('verify_'.$uid));

    }
}