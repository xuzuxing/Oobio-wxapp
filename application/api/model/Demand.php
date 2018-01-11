<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/11/25
 * Time: 17:13
 */

namespace app\api\model;


class Demand extends BaseModel
{
    protected $autoWriteTimestamp = true;

    public function getLists($address,$page){

        //默认查询大于当前时间的数据
        $where = 'd.status = 1 and d.end_time <= "'.time().'"';
        if($address != ''){
            //指定位置起始
            $where .= ' and d.address like "%'.$address.'%"';
        }
        //页数
        $page_count = 20;
        $limit = ($page-1)*$page_count;
        $lists = $this->alias('d')
            ->field('d.*,u.avatarUrl')
            ->join('__USER__ u ',' u.id = d.uid','LEFT')
            ->where($where)
            ->limit($limit,$page_count)
            ->order('create_time asc')
            ->select();
        $data = TimeToDate($lists);
        return $data;

    }
    public function getMyLists($uid,$page){
        $page_count = 10;
        $limit = ($page-1)*$page_count;
        $lists = $this->where('uid' ,$uid)
            ->limit($limit,$page_count)
            ->order('create_time desc')
            ->select();
        $data = TimeToDate($lists);
        return $data;
    }

    public function getDetail($id){
        $this->where('id = "'.$id.'"')->setInc('see');
        $data =  $this->table('__DEMAND__ i,__USER__ u')
            ->field('i.*,u.nickName,u.avatarUrl')
            ->where('i.uid = u.id and i.id = "'.$id.'"')
            ->find();
        $data['end_time']      = date('H:i',$data['end_time']);
        $data['initial_time']  = date('H:i',$data['initial_time']);
        return $data;
    }
}