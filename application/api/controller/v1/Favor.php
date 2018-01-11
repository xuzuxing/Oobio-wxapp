<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/29
 * Time: 16:13
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\model\User as UserModel;
use app\api\model\Demand;
use app\api\service\Token;

class Favor extends Base {

    /***
     * 收藏
     * @url /fav/add
     * @return array
     */
    public function addFav(){

        $uid = Token::getCurrentUid();
        $data['uid'] = $uid;
        $data['iid'] = input('iid');
        $data['create_time'] = time();
        $favObj = db('favor');
        if($favObj->insert($data)){
            $result['status'] = 1;
            $result['msg'] = '收藏成功';
        }else{
            $result['status'] = 0;
            $result['msg'] = '收藏失败';
        }
        exit(json_encode($result));
    }


    public function delFav(){
        $uid = Token::getCurrentUid();
        $data['uid'] = $uid;
        $data['iid'] = input('iid');
        $favObj = db('favor');
        if($favObj->where($data)->delete()){
            $result['status'] = 1;
            $result['msg'] = '取消收藏成功';
        }else{
            $result['status'] = 0;
            $result['msg'] = '取消收藏失败';
        }
        exit(json_encode($result));
    }

    public function isFav(){
        $uid = Token::getCurrentUid();
        $data['uid'] = $uid;
        $data['iid'] = input('iid');
        $favObj = db('favor');
        $data = $favObj->where($data)->find();
        if(!empty($data)){
            $result['status'] = 1;
            $result['msg'] = '已收藏';
        }else{
            $result['status'] = 0;
            $result['msg'] = '未收藏';
        }
        exit(json_encode($result));
    }


    public function myFav(){
        $uid = Token::getCurrentUid();
        $where['f.uid'] = $uid;

        $i = new Demand();
        $page = input('page','1');
        $page_count = 20;
        $limit = ($page-1)*$page_count;
        $list = db('favor')
            ->table('__FAV__ f')
            ->field('i.*,f.id as fad')
            ->join('__DEMAND__ i ','i.id = f.iid','LEFT')
            ->where($where)
            ->limit($limit,$page_count)
            ->order('f.time asc')
            ->select();

        $result['status'] = 1;
        $result['msg'] = '获取成功';
        $result['data'] = $list;
        exit(json_encode($result));
    }
}