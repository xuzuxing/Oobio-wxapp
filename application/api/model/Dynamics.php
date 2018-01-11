<?php
namespace app\api\model;

class Dynamics extends BaseModel
{

    public function addDynamic($data){
        $rules = array(
            array('content','require','内容不能为空')
        );
        $data['time'] = strtotime($data['date'].' '.$data['time']);
        return $this->validate($rules)->insert($data);
    }

    public function getDynamicById($uid){
        $page = input('page', '1');
        $page_count = 20;
        $limit = ($page - 1) * $page_count;
        $list = $this->alias('d')
            ->field('d.*,u.avatarUrl,u.nickName')
            ->join('__USER__ u', ' u.id = d.uid', 'LEFT')
            ->where('uid',$uid)
            ->limit($limit, $page_count)
            ->order('d.create_time desc')
            ->select();
        if (empty($list)){
            return false;
        }else{
            $arr = array();
            foreach ($list as $v) {
                $arr[] = $v['id'];
            }
            $str = implode(',', $arr);
            $where = 'iid in (' . $str . ') and type = "dynamic"';

            $comObj = new Comment();
            $comment = $comObj->alias('c')
                ->field('c.id,c.iid,c.reply,c.content,u.nickName')
                ->join('__USER__ u ', ' u.id = c.uid', 'LEFT')
                ->where($where)
                ->order('c.create_time desc')
                ->select();
            $arr = array();
            foreach ($comment as $k => $v) {
                $arr[$v['iid']][] = $v;
            }
            foreach ($list as $k => $v) {
                $list[$k]['comment'] = $arr[$v['id']];
            }
            return $list;
        }
    }
}