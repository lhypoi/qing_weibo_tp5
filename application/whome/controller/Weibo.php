<?php
namespace app\whome\controller;

use think\Controller;

class Weibo extends Controller
{
    public function index() {
        $weibo_list = model("weibo")
                    ->field("weibo_detail.*, user.user_nickname, user.user_pic")
                    ->join("user", "user.id = weibo_detail.user_id")
                    ->order("weibo_detail.id DESC")
                    ->withCount("commont")
                    ->paginate(10);
        foreach ($weibo_list as $item) {
            $item['tag'] = model("tag")
                        ->field("*")
                        ->join("tag_relationship", "tag_relationship.tag_id=weibo_tag.id")
                        ->where("tag_relationship.weibo_id=".$item['id'])
                        ->select();
        }
        $this->assign("weibo_data", $weibo_list);
        //print_r($weibo_list);
        return $this->fetch();
    }
    
    //异步加载获取
    public function get() {
        $page_mark = input('page_mark');
        $weibo_model = model("weibo");
        $weibo_data = array();
        $weibo_id = array();
        if($page_mark == 'index') {
            $weibo_data = $weibo_model
                        ->field("weibo_detail.*, user.user_nickname, user.user_pic")
                        ->join("user", "user.id = weibo_detail.user_id")
                        ->order("weibo_detail.id DESC")
                        ->withCount("commont")
                        ->paginate(10);
            foreach ($weibo_data as $item) {
                $item['tag'] = model("tag")
                            ->field("*")
                            ->join("tag_relationship", "tag_relationship.tag_id=weibo_tag.id")
                            ->where("tag_relationship.weibo_id=".$item['id'])
                            ->select();
            }
        } //elseif ($page_mark == 'tag') {
//             $weibo_id = $weibo_model->getWeiboListById($id, $page);
//             foreach ($weibo_id as $key => $value) {
//                 $result = $weibo_model->getWeiboListByTag($value['weibo_id']);
//                 foreach ($result as $k => $v) {
//                     $weibo_data.array_push($weibo_data, $v);
//                 }
//             }
//         } elseif ($page_mark == 'home') {
//             $weibo_id = $weibo_model->getWeiboidByUser($id, $page);
//             foreach ($weibo_id as $key => $value) {
//                 $result = $weibo_model->getWeiboListByTag($value['id']);
//                 foreach ($result as $k => $v) {
//                     $weibo_data.array_push($weibo_data, $v);
//                 }
//             }
//         }

        if(empty($weibo_data)) {
            return [
                "status" => 0,
                "msg" => "无更多页面"
            ];
        }else{
            $this->assign("weibo_data", $weibo_data);
            return [
                "status" => 1,
                "html" => $this->fetch("/public/weibo_li"),
            ];
        }
    }
}