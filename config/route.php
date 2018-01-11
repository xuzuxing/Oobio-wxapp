<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::post('api/:ver/user/login','api/:ver.User/login');

Route::any('api/:ver/valid/weixin','api/:ver.Token/getWeixin');


/**
 * Upload
 */
Route::group('api/:ver/upload',function (){
    Route::any('/up','api/:ver.Upload/index');
    Route::post('/identity','api/:ver.Upload/identity');
});
/**
 * Token
 */
Route::group('api/:ver/token',function () {
    Route::post('/user', 'api/:ver.Token/getToken');
    Route::post('/app', 'api/:ver.Token/getAppToken');
    Route::post('/verify', 'api/:ver.Token/verifyToken');
});

//User
Route::post('api/:ver/user/info','api/:ver.User/getUserInfoById');
Route::post('api/:ver/user/editUser','api/:ver.User/editUser');
Route::post('api/:ver/user/qrcode','api/:ver.User/getQrcode');

/**
 * 需求
 */
Route::group('api/:ver/demand',function () {
    Route::get('/latest','api/:ver.Demand/getLatest');
    Route::get('/mycount','api/:ver.Demand/getMycount');
    Route::get('/mylist','api/:ver.Demand/getMylist');
    Route::post('/add','api/:ver.Demand/addDemand');
    Route::get('/info','api/:ver.Demand/getDemandDetails');
});

//分类
Route::group('api/:ver/category',function(){
    Route::get('getAll', 'api/:ver.category/getAllCate');
    Route::get('/get', 'api/:ver.category/getCategory');
});

//活动
//Banner
Route::get('api/:ver/banner/:id', 'api/:ver.Banner/getBanner');  //:id  参数绑定
Route::get('api/:ver/activity', 'api/:ver.Activity/getAbstract');

/**
 * 消息
 */
Route::group('api/:ver/msg',function (){
    Route::get('/all','api/:ver.Message/getAll');
    Route::get('/get','api/:ver.Message/get');
    Route::get('/weather','api/:ver.Message/getWeather');
    Route::post('/sendSms','api/:ver.Message/sendCodeBySms');
});

/**
 * 积分
 */
Route::group('api/:ver/integral',function () {
    Route::post('/recharge', 'api/:ver.Integral/RechargeForUser');
    Route::post('/repay', 'api/:ver.Integral/RechargeOnce');
    Route::post('/payreturn', 'api/:ver.Integral/rePayOrder');
    Route::get('/myposit', 'api/:ver.Integral/getPointsByUser');
});

Route::post('api/:ver/pay/notify', 'api/:ver.Pay/receiveNotify');
Route::post('api/:ver/pay/re_notify', 'api/:ver.Pay/redirectNotify');
Route::post('api/:ver/pay/concurrency', 'api/:ver.Pay/notifyConcurrency');
/**
 * 收藏
 */
Route::group('api/:ver/fav',function (){
    Route::get('/isFav','api/:ver.Favor/isFav');
    Route::get('/myFav','api/:ver.Favor/myFav');
    Route::post('/addFav','api/:ver.Favor/addFav');
    Route::post('/delfav','api/:ver.Favor/delFav');
});

/**
 * 评论
 */
Route::group('api/:ver/comment',function (){
    Route::get('/get_count','api/:ver.Comment/get_count');
    Route::get('/get','api/:ver.Comment/get');
    Route::get('/getdetail','api/:ver.Comment/getdetail');
    Route::post('/add','api/:ver.Comment/add');
    Route::post('/zan','api/:ver.Comment/zan');
});

/**
 * 动态
 */
Route::group('api/:ver/dynamic',function (){
    Route::post('/add','api/:ver.Dynamics/add');
    Route::post('/getlist','api/:ver.Dynamics/getlist');
});

/**
 * 订单
 */
Route::group('api/:ver/order',function (){
    Route::post('/place','api/:ver.Order/placeOrder');//下单
    Route::get('/getDetail','api/:ver.Order/getDetailByID');//详情
    Route::get('/mycount','api/v1.Order/getCountsByUser');
    Route::get('/mylists','api/:ver.Order/getOrdersByUser');
    Route::post('/deal','api/:ver.Order/DealOrder');
});
