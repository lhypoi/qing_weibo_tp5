<?php
namespace app\whome\controller;

use think\Controller;
use think\Session;
class Commont extends Controller
{
    //获取评论
    public function getComment() {
        $article_id = input('article_id');
        $commentStart = input('commentList');
        $result = model("commet")
                ->field("weibo_commet.id,
                        weibo_commet.commet_content,
                        weibo_commet.commet_time,
                        weibo_commet.weibo_id,
                        weibo_commet.user_id,
                        user.user_name,
                        user.user_nickname,
                        user.user_pic")
                ->join("user", "user.id=weibo_commet.user_id AND weibo_commet.weibo_id=$article_id")
                ->order("weibo_commet.id DESC")
                ->paginate(5);
        $this->assign("page", ((int)input('page'))+1);
        $num = model("commet")->where("weibo_id=$article_id")->count();
        if(count($result) < 5 || (int)$commentStart + 5 >= $num) {
            $this->assign("more", 0);
        } else {
            $this->assign("more", 1);
        }
        $this->assign("item", $result);
        $html = $this->fetch("/public/commet_li");
        return [
            "status" => 1,
            "msg" => "评论查询成功", 
            "html" => $html
        ];
    }
    
    //发送评论
    public function addComment() {
        $comment_data['commet_time'] = time();
        $comment_data['user_id']  = Session:: get('info')['id'];
        $comment_data['commet_content'] = input('commet_content');
        $comment_data['weibo_id'] = input('weibo_id');
        $comment_model = model('commet');
        $comment_model->save($comment_data);
        $lastID = $comment_model->getLastInsID();
        if ($lastID > 0) {
            $new_comment = model("commet")
                            ->field("weibo_commet.id,
                                    weibo_commet.commet_content,
                                    weibo_commet.commet_time,
                                    weibo_commet.weibo_id,
                                    weibo_commet.user_id,
                                    user.user_name,
                                    user.user_nickname,
                                    user.user_pic")
                            ->join("user", "weibo_commet.user_id=user.id AND weibo_commet.id=$lastID")
                            ->select();
            $this->assign("item", $new_comment);
            $this->assign("more", 0);
            $html = $this->fetch("/public/commet_li");
            return[
                "status" => 1,
                "msg" => "评论发送成功", 
                "html" => $html,
                "last" => $new_comment
            ];
        }
    }
}