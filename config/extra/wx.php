<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/24
 * Time: 21:54
 */

return [
    //  +---------------------------------
    //  微信相关配置
    //  +---------------------------------
    'img_prefix'    =>'https://www.momocamous.com/',
//    // 小程序app_id
//    'app_id' => 'wx7cdb08ac4ae735f9',
//    // 小程序app_secret
//    'app_secret' => '0b333ef3864739dcce7384b60c55ac45',
    // 小程序app_id
    'app_id' => 'wx1c32cda245563ee1',
    // 小程序app_secret
    'app_secret' => '8c919d413f97a81a8ca675d8a2f0aacd',
    //保存时长
    'token_expire_in'   => 7200,

    // 微信使用code换取用户openid及session_key的url地址
    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

    // 微信获取access_token的url地址
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",

    // +--------------------------------
    // 微信支付相关
    // +--------------------------------

    'wx_payName'    => 'MomoCampus积分充值',
    'Rate'          =>  '10'


];
