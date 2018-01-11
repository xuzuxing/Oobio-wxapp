<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 17:12
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\service\Token;
use app\api\model\Demand as DemandModel;
use app\api\model\User as UserModel;
use app\api\validate\IDMustBePositiveInt;

/**
 * 获取最新列表
 * 不需要登录验证
 * 因此不继承Base验证
 * @package app\api\controller\v1
 */
class Demand extends Base
{
    /**
     * 获取最新列表
     * @url /demand/latest
     */
    public function getLatest($address,$page = '1'){

        $info = (new DemandModel())->getLists($address,$page);
        if (!$info ) {
            return show(0, '没有数据',  $info);
        }
        return show(1, '获取成功',  $info);
    }

    /**
     * 获取需求列表
     * @url /demand/mylist
     * @return array
     */
    public function getMylist($page = '1'){

        $uid = Token::getCurrentUid();
        $data = (new DemandModel())->getMyLists($uid,$page);
        if(empty($data)){
            return show(0, '请求数据不存在',  $data);
        }

        return show(1, '获取成功',  $data);
    }

    /**
     * My需求数目
     * @url /demand/mycount
     * @return array
     */
    public function getMycount(){
        $uid = Token::getCurrentUid();
        $data = (new DemandModel())
            ->where('uid',$uid)
            ->count();
        if(!$data){
            return show(0, '您还没有发布任何需求^_^',  $data);
        }

        return show(1, '获取成功',  $data);
    }


    /**
     * 发布需求
     * @url /demand/add
     */
    public function addDemand()
    {
        //      1.一般形式：date('Y-m-d H:i:s', 1156219870);
        //      2.一般形式：strtotime('2010-03-24 08:15:42');
        $uid = Token::getCurrentUid();
        $data = [
            'name'          => input('post.name'),
            'uid'           => $uid,
            'gender'        => input('post.gender'),
            'phone'         => input('post.phone'),
            'price'         => input('post.price'),
            'cate'          => input('post.cate'),
            'initial_time'  => strtotime(input('post.time1')),
            'end_time'      => strtotime(input('post.time2')),
            'remark'        => input('post.remark'),
            'address'       => input('post.address')
        ];
        $i = new DemandModel();
        if (!($id = ($i->save($data)))) {
            $result['status'] = 0;
            $result['msg'] = $i->getError();
        } else {
            $result['status'] = 1;
            $result['msg'] = '发布成功';
            $result['iid'] = $id;
        }
        exit(json_encode($result));
    }

    /***
     * @url /demand/info
     * @return array
     */
    public function getDemandDetails($id){
        (new IDMustBePositiveInt())->goCheck();
        $i = new DemandModel();
        $data = $i->getDetail($id);
        if(!$data){
            return show(0, '没有此需求^_^',  $data);
        }

        return show(1, '获取成功',  $data);
    }
}