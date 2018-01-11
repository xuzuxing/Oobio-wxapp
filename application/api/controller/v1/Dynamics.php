<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/10/25
 * Time: 22:05
 */
namespace app\api\controller\v1;
use app\api\controller\Base;
use app\api\service\Token;
use app\api\model\Dynamics as DynamicModel;
use app\api\model\User as UserModel;
use app\api\model\Comment as CommentModel;

/**
 * 动态类
 * Class Dynamics
 * @package app\api\controller\v1
 */
class Dynamics extends Base {

    public function add()
    {
        $u = new UserModel();
        $uid = Token::getCurrentUid();
        $user = $u->getByOpenID($uid);
        $data['uid'] = $user['id'];
        $data['type'] = 'dynamic';
        $data['content'] = input('content','');
        $data['img'] = htmlspecialchars_decode(input('img',''));
        $C = new CommentModel();
        if($C->save($data)){
            $result['status'] = 1;
            $result['msg'] = '获取成功';
        }else{
            $result['status'] = 0;
            $result['msg'] = '获取失败';
        }
        exit(json_encode($result));
    }
    public function getList()
    {
        $data = (new CommentModel())->getDynamic();
        if (empty($data)) {
            return show(0, '没有更多动态了~', $data);
        }
        return show(1, '消息加载成功', $data);

    }

}