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

    public function headSelect(){
        $uid=input('user_id');
        $weibo_data = model("weibo")->where("user_id".$uid)->order("id desc")->limit(3)->select("*");
        foreach ($weibo_data as $key => $value) {
            $weibo_data[$key]['time']=date('Y-m-d H:i:s',$weibo_data[$key]['create_time']);
        }
        return $weibo_data;
    }

    //发布微博
    public function sendWeibo()
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

    public function delete(){
        // 获取微博id
// 删除这条微博信息，删除它的评论。文件 unlink
//    $weibo_id = $_POST['id'];
        $weibo_id = input('id');
//    $table='weibo_detail';
//    $type=$_POST['type'];
        $type=input('type');
        $weibo_data=array();

//    $result=$this->model("weibo")->getWeiboListByTag($weibo_id);
//    "SELECT * FROM weibo_detail WHERE id = $weibo_id";
        $result=model('weibo')->where('id='.$weibo_id)->select('*');
        if($type=='pic_text'){
            // print_r($result);
            $file_path=$result[0]['pic'];
            // echo $file_path;exit();
            unlink($file_path);
        }elseif ($type=='video') {
            $file_path=$result[0]['video'];
            unlink($file_path);
        }elseif($type=='long_content'){
            $str=$result[0]['weibo_content'];
            preg_match_all('/<img.+src=\"\/?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/iU',$str,$match);
            foreach ($match[1] as $key => $value) {
                $file_path=$_SERVER['DOCUMENT_ROOT'].$match[1][$key];
                unlink($file_path);
            }

        }


// 查询评论表该微博是否有评论信息

//        $commot_list = $this->model("comment")->getCommontByWid($weibo_id);
//        select("select * from weibo_commet where weibo_id = $weibo_id")
        $commot_list=db('weibo_commet')->where('weibo_id='.$weibo_id)->select('*');
        if (!empty( $commot_list)) {
            foreach ($commot_list as $key=>$value) {
                $this->model("weibo_commet")->where("id".$value['id'])->delete();
            }
        }

//查询是否有标签，有则删除   还没改！！！
        $tag_data=$this->model("tag")->getIdInRelation($weibo_id);
        if(!empty($tag_data)){
            foreach ($tag_data as $key => $value){
                $this->model("tag")->delTag($value['id']);
            }
        }

        $this->model("weibo")->delInfo($table,$weibo_id);

        return [
            "status" => 1,
            "msg" => "微博删除成功"
        ];
    }
}

