<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 0:19
 */


namespace app\api\controller\v1;

use app\api\controller\Base;
use app\lib\exception\TokenException;
use app\api\service\Token as TokenService;
use app\api\service\AccessToken;
use app\api\model\User as UserMode;
use app\lib\exception\UserException;

class User extends Base
{
    /**
     * 获取用户详细信息
     * @url /user
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws UserException
     */
    public function getUserInfoById(){
        $uid = TokenService::getCurrentUid();
        $res = (new UserMode())->getByOpenID($uid);
        if(!$res){
            throw new UserException([
                'msg'   => '该用户信息不存在'
            ]);
        }
        return $res;
    }

    /***
     * 专属二维码
     * @return array
     */
    public function getQrcode(){
        $uid = TokenService::getCurrentUid();
        $res = (new UserMode())->get($uid);
        if($res->qrcode ==''){
            $qr = $this->getWxaQrcode($uid);
            return show(1,'获取成功',config('wx.img_prefix').$qr);
        }else{
            return show(1,'已经生成',config('wx.img_prefix').$res->qrcode);
        }

    }
    /***
     * 生成小程序参数二维码
     */
    public function getWxaQrcode($uid)
    {

        //初始化获取access_token
        $accessToken = new AccessToken();
        $token = $accessToken->get();
        //$url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token='.$token;
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$token;
        $path = '/pages/user/qrcode?id=';
        $data=[
            'scene'   => $uid,
            'path'    => $path.$uid,
            'width'   => '430',
            'auto_color'  => false,
            'line_color'  => ["r"=>"54","g"=>"136","b"=>"255"]
        ];
        $return = curl_post($url,$data);

        //创建文件夹
        $dir = iconv("UTF-8", "GBK", 'uploads/'.$uid.'/');
        if (!file_exists($dir)){
            mkdir ($dir,0777,true);
        }
        //将生成的小程序码存入相应文件夹下
        $Qrurl =  $dir.time().'_'.$uid.'.jpg';
        file_put_contents($Qrurl,$return);
        //存入数据库
        $u = new \app\api\model\User();
        $u->where('id',$uid)->update(['qrcode'=> $Qrurl]);
        return $Qrurl;

    }

    /**
     * 实名认证
     * 1.传入基本信息
     * 2.上传证件照
     * 3.提交审核
     * 4.审核结果
     */
    public function identity(){
        $iv = db('identity');
        $uid = TokenService::getCurrentUid();
        //插入用户信息
        $data['uid'] = $uid;
        $data['real_name']          = input('post.name');
        $data['student_num']        = input('post.number');
        $data['Student_id_card']    = input('post.img');
        $data['add_time']           = time();
        $iv->insert($data);

        //调整认证状态 1为认证中，2为认证失败，3为认证成功，0为未认证
        (new UserMode())->where('id',$uid)->update(['identity'=> 1]);

        return show(1,'认证提交成功','');

    }

    /**
     * //修改个人信息
     */
    public function editUser()
    {
        $u = new UserMode();
        //修改用户信息
        $json = file_get_contents('php://input');
        $data = json_decode($json,true);

        $UserData = $data['userInfo'];
        //获取全局用户ID
        $uid = TokenService::getCurrentUid();
        $u->where('id',$uid)->update($UserData);
        //信息获取
        $user = $u->getByOpenID($uid);
        $result['status'] = 1;
        $result['msg'] = '修改成功';
        $result['user'] = $user;
        exit(json_encode($result));
    }


    /**
     * 解密用户信息
     * @param $sessionKey
     * @param $encryptedData
     * @param $iv
     * @return bool
     */
    private function getUserInfo($sessionKey,$encryptedData, $iv)
    {
        vendor('wxBizDataCrypt.wxBizDataCrypt');
        $pc = new \WXBizDataCrypt(config('wx.app_id'), $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        if ($errCode == 0) {
            return $data;
        } else {
            return false;
        }
    }

    /**
     * 读取/dev/urandom获取随机数
     * @param $len
     * @return mixed|string
     */
    private function randomFromDev($len) {
        $fp = @fopen('/dev/urandom','rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        }
        else
        {
            trigger_error('Can not open /dev/urandom.');
            return substr(time().MD5(time().rand()), 0, $len);
        }
        // convert from binary to string
        $result = base64_encode($result);
        // remove none url chars
        $result = strtr($result, '+/', '-_');

        return substr($result, 0, $len);
    }
}