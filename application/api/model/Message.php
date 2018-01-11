<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 20:30
 */

namespace app\api\model;
use think\Db;

class Message extends BaseModel
{

    /**
     * 获取分类消息详细
     * @param $uid
     * @param $type
     * @param $page
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMsgTypeById($uid,$type,$page){

        $page_count = 20;
        $limit = ($page-1)*$page_count;
        $where['uid'] = $uid;
        $where['type'] = $type;

        $data = DB::table('__MESSAGE__ m')
            ->field('m.*,u.avatarUrl,u.nickName')
            ->join('__USER__ u ',' m.uid=u.id','LEFT')
            ->where($where)
            ->limit($limit,$page_count)
            ->order('m.create_time desc')
            ->select();
        //这里要 返回数据为0，则直接return 0
        //意思就是没有消息
        if(empty($data)){
            return $data;
        }else{
            //修改数据库为已读状态
            $see['see'] = 1;
            $arr = array();
            foreach($data as $v){
                $arr[] = $v['id'];
            }
            $str = implode(',',$arr);
            $str = 'id in ('.$str.')';
            self::where($str)->update($see);
            return $data;
        }
    }

    /**
     * 通过用户ID获取未读消息
     * @param $uid
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getMsgAllById($uid){
        return self::field('type,count(*) as count')
            ->where([
                'uid' => $uid,
                'see' => 0
            ])
            ->group('type')
            ->select();
    }
}