<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        echo  $this->makeOrderNo(10008);
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    /**
     * 生成订单号
     * @param $uid
     * @return string
     */
    public function makeOrderNo($uid = '0')
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5) . substr(microtime(), 2, 5)
            .substr($uid,3,5). sprintf('%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     * 生成结单二维码
     * @param $key
     * @return int|string
     */
    public function getorderqrcode($key){

        Vendor('phpqrcode.phpqrcode');
        //生成二维码图片
        $object = new \QRcode();
        // $url='asfnanegnwl/';//网址或者是文本内容
        $level=3;
        $size=10;
        //创建文件夹
        $dir = iconv("UTF-8", "GBK", '/uploads/order_qrcode/'.$key.'/');
        if (!file_exists($dir)){
            mkdir ($dir,0777,true);
        }
        $ad = $dir.$key.'.jpg';//保存地址
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($key, $ad, $errorCorrectionLevel, $matrixPointSize,2);

    }

}
