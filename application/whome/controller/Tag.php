<?php
namespace app\whome\controller;

use think\Controller;

class Tag extends Controller
{
    public function show(){
        $id=input('id');
        $weibo_id=db("tagRelationship")
            ->field("weibo_id")
            ->where('tag_id='.$id)
            ->order("id DESC")
            ->select();
//        var_dump($weibo_id);exit();
        $weibo_data = array();
        foreach ($weibo_id as $key=>$item)
        {
            $result=model('weibo')->where('id='.$item['weibo_id'])->select();
            foreach ($result as $k => $v) {
                $weibo_data[]=$v;
            }
        }
//        var_dump($weibo_data);
        foreach ($weibo_data as $key => $value) {
//            $weibo_data[$key]['user_data'] = $model->getWeiboUser($value['user_id']);
//            SELECT * FROM weibo_user WHERE id=$user_id
            $weibo_data[$key]['user_data'] = model('user')->where('id='.$value['user_id'])->select();
//            var_dump($weibo_data[$key]['user_data']);
//            $weibo_data[$key]['commet_data'] = $model->getWeiboCommentTotal($value['id']);
//            SELECT count(*) FROM weibo_commet WHERE weibo_id=$weibo_id;
            $weibo_data[$key]['commet_data']=model("commet")
                ->where("weibo_id=".$value['id'])
                ->count('id');
//                ->select();
//            $weibo_data[$key]['tag_data'] = $this->model('tag')->getTagbyWeiboid($value['id']);
//            select t.id,t.name from tag t inner join tag_relationship r on r.weibo_id = ".$weibo_id." and r.tag_id = t.id
            $weibo_data[$key]['tag_data']=model("tag")->alias('t')
                ->join('tag_relationship r', 't.id=r.tag_id')
                ->field('t.id,t.name')
                ->where('r.weibo_id='.$value['id'])
                ->select();

        }

        $this->assign("weibo_data", $weibo_data);
        return $this->fetch('weibo\tag');
    }
    public function tagSelect(){
        $tid=input('tag_id');
//        SELECT weibo_id FROM tag_relationship WHERE tag_id = $id ORDER BY id DESC LIMIT $page"
        $weibo_id=db('tag_relationship')->field('weibo_id')->where('tag_id='.$tid)->order("id DESC")->limit(3)->select();
        $result = array();
        foreach ($weibo_id as $key => $value) {
//            SELECT * FROM weibo_detail WHERE id = $weibo_id
            $value = model("weibo")->where("id=".$value['weibo_id'])->select();
//            var_dump($value);
            $result[]= $value[0];
        }
        return $result;
    }

}