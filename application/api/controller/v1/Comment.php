<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 18:42
 */

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\service\Token;
use app\api\model\Comment as CommentModel;
use app\api\model\User as UserModel;
use app\api\model\Demand as DemandModel;
use app\api\validate\IDMustBePositiveInt;

/***
 * 评论
 * @package app\api\controller\v1
 */
class Comment extends Base
{


    public function get($page = '1')
    {
        $C = new CommentModel();
        $data = $C->getCommentLists($page);
        if(empty($data)){
            return show(0, '没有最新评论:(',  $data);
        }
        return show(1, '获取成功',  $data);

    }

    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $C = new CommentModel();
        $data = $C->getCommentDetail($id);
        if(empty($data)){
            return show(0, '没有最新评论:(',  $data);
        }
        return show(1, '获取成功',  $data);
    }

    public function get_count(){
        $C = new CommentModel();
        $where['iid'] = input('id');
        $where['type'] = input('type');
        $data = $C->where($where)->count();
        $result['status'] = 1;
        $result['msg'] = '评论总数加载成功';
        $result['data'] = $data;
        exit(json_encode($result));
    }


    /***
     * 添加评论
     */
    public function add()
    {
        $uid = Token::getCurrentUid();
        $data['uid']        = $uid;
        $data['iid']        = input('iid');
        $data['content']    = input('content','');
        $data['type']       = input('type','info');
        $data['reply']      = input('reply','');
        $data['img']        = htmlspecialchars_decode(input('img',''));
        $data['create_time'] = time();
        $res = (new CommentModel())->addComment($data);
        if($id = $res){
            $result['status'] = 1;
            $result['msg'] = '评论成功';
            $result['id'] = $id;
            //发送站内消息
            msg('comment',$data['iid'],$uid,$uid,'回复了您的信息 :'.$data['content']);
        }else{
            $result['status'] = 0;
            $result['msg'] = '评论失败';
        }
        exit(json_encode($result));
    }


    /**
     * 点赞
     */
    public function zan()
    {
        $uid = Token::getCurrentUid();
        $data['uid'] = $uid;
        $data['cid'] = input('cid');
        $zanObj = db('praise');
        $zanData = $zanObj->where($data)->find();
        if(empty($zanData)){
            $data['create_time'] = time();
            $zanObj->insert($data);  //写入赞的数据表
            $C = new CommentModel();
            $com['id'] = $data['cid'];
            $C->where($com)->setInc('zan');  //评论赞+1
            $data = $C->where($com)->find();
            //点赞提醒
            msg('zan','',$data['uid'],$uid,'赞了你的评论:'.$data['content']);

            $result['status'] = 1;
            $result['msg'] = '点赞成功';
            $result['zan'] = $data['zan'];
        }else{
            $result['status'] = 0;
            $result['msg'] = '你已经赞过了';
        }

        exit(json_encode($result));
    }

}
