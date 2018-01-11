<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 18:44
 */

namespace app\api\model;



class Comment extends BaseModel
{

    /**
     * 获取评论列表
     * @param $page
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCommentLists($page)
    {
        $where['iid'] = input('id');
        $where['type'] = input('type');
        $page_count = 20;
        $limit = ($page-1)*$page_count;
        if($limit == ''){
            $limit = 100000;
        }else{
            $limit = $limit.','.$page_count;
        }
        $data = $this->alias('c')
            ->field('c.*,u.nickName,u.avatarUrl')
            ->join('__USER__ u ',' u.id = c.uid','LEFT')
            ->where($where)
            ->order('c.create_time desc')
            ->limit($limit)
            ->select();
        return $data;
    }

    public function getCommentDetail($id){

        $data = $this->alias('c')
            ->field('c.*,u.nickName,u.avatarUrl')
            ->join('__USER__ u ',' u.id = c.uid','LEFT')
            ->where(array('c.id'=>$id))
            ->find();
        return $data;
    }
    /**
     * 获取动态
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDynamic(){
        //加载页数
        $page = input('page','1');
        $page_count = 10;
        $limit = ($page-1)*$page_count;
        $data = $this->alias('c')
            ->field('c.id,c.reply,c.type,c.content,c.img,c.create_time,u.nickName,u.avatarUrl')
            ->join('__USER__ u ',' u.id = c.uid','LEFT')
            ->limit($limit,$page_count)
            ->order('c.create_time desc')
            ->select();
        return $data;
    }
    /**
     * 添加评论
     * @param $data
     * @return mixed
     */
    public function addComment($data){
        $rules = array(
            array('content','require','内容不能为空')
        );
        return $this->validate($rules)->insert($data);
    }

}