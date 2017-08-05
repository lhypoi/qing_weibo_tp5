<?php
namespace app\whome\controller;

use think\Controller;

class User extends Controller
{
//    http://localhost:86/public/whome/user/home?id=14
//    用户主页
    public function home()
    {

        $user = model('user')
            ->field('*')
            ->where('id='.input('id'))
            ->find();

        $weibo_list = model('weibo')
            ->field("weibo_detail.*, user_nickname, user.user_pic")
            ->join("user", "user.id = weibo_detail.user_id")
            ->where('user_id='.input('id'))
            ->order("weibo_detail.id DESC")
            ->withCount("commont")
            ->paginate(10);

        foreach ($weibo_list as $key => $item)
        {
            $item['tag'] = model('tag')->alias('t')
                ->join('tag_relationship r', 't.id=r.tag_id')
                ->field('t.id,t.name')
                ->where('r.weibo_id='.$item['id'])
                ->select();
        }

        $this->assign("user", $user);
        $this->assign("weibo_data", $weibo_list);

        return $this->fetch('weibo\personal');
    }

}