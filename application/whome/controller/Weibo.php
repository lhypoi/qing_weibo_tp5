<?php
namespace app\whome\controller;

use think\Controller;
use think\Request;
use think\Session;

class Weibo extends Controller
{
//    首页加载
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
        }

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

//    发布微博
    public function sendWeibo ()
    {
//        通常微博的数据
        $weibo_data = input();
        $weibo_data['create_time'] = time();
        $weibo_data['user_id'] = Session::get('info')['id'];
//        其他类型的微博数据
        if (input('type') == 'pic_text') {
            $info = Request()->file('pic_file')->validate(["ext"=>"jpg,png,gif"])->move("uploads");
            $weibo_data['pic'] = '/public/uploads/'.$info->getSaveName();
        } else if (input('type') == 'music') {
            $weibo_data['music'] = input('music_file');
        } else if (input('type') == 'video') {
            $info = Request()->file('video_file')->validate(["ext"=>"mp4"])->move("uploads");
            $weibo_data['video'] = '/public/uploads/'.$info->getSaveName();
        }
//微博数据插入数据库
        $weibo_model = model("weibo");
        $weibo_model->allowField(true)->save($weibo_data);
        $weibo_id = $weibo_model->getLastInsID();
//        标签的处理
        $tagname_arr = input('tagname_arr/a');
        $tagid_arr = [];
//        标签已存在和不存在的处理
        $tag_model = model('tag');
        if ($tagname_arr) {
            foreach ($tagname_arr as $key => $value) {
                $tagid = $tag_model->field('id')->where('name=\''.$value.'\'')->find()['id'];
                if ($tagid) {
                    $tagid_arr[] = $tagid;
                } else {
                    $tag_model->save(['name'=>$value]);
                    $tagid_arr[] = $tag_model->getLastInsID();
                }
            }
        }
//        批量更新标签微博关联表
        $tag_weibo_data = [];
        foreach ($tagid_arr as $key => $value) {
            $tag_weibo_data[] = [
                'tag_id'=>$value,
                'weibo_id'=>$weibo_id
            ];
        }
        model('tagRelationship')->saveAll($tag_weibo_data);
//        回复微博html
        $weibo_list = $weibo_model
            ->field("weibo_detail.*, user_nickname, user.user_pic")
            ->join("user", "user.id = weibo_detail.user_id")
            ->where('weibo_detail.id='.$weibo_id)
            ->withCount("commont")
            ->select();

        foreach ($weibo_list as $key => $item)
        {
            $item['tag'] =$tag_model->alias('t')
                ->join('tag_relationship r', 't.id=r.tag_id')
                ->field('*')
                ->where('r.weibo_id='.$item['id'])
                ->select();
        }
        $this->assign("weibo_data", $weibo_list);

        $html =  $this->fetch('public/weibo_li');

        return [
            "status" => 1,
            "msg" => "微博发送成功",
            "html" => $html
        ];
    }
}
