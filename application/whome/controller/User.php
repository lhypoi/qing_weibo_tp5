<?php
namespace app\whome\controller;

use think\Controller;

class User extends Controller
{
//    http://localhost/20170718/qing_weibo_tp5/public/whome/user/home?id=14
//    用户主页
    public function home()
    {
//        return input('id');
        $weibo_list = model('weibo')
            ->field('*')
            ->where('user_id='.input('id'))
            ->select();

        foreach ($weibo_list as $key => $item)
        {
            $weibo_list[$key]['tag_data'] = model('tag')->alias('t')
                ->join('tag_relationship r', 't.id=r.tag_id')
                ->field('t.id,t.name')
                ->where('r.weibo_id='.$item['id'])
                ->select();
        }

        return json([
            'weibo_list'=> $weibo_list
        ]);
    }

}