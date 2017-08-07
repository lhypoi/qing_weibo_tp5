<?php
namespace app\whome\controller;
use think\Session;
use think\Controller;
use think\Request;

class User extends Controller
{
//    http://localhost:86/public/whome/user/home?id=14
//    用户主页
    public function home()
    {

        if (!input('id')) {
            return 'error';
        }

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

    //登陆
    public function log() {
        $username = input('user_name');
        $pwd = input('user_pwd');
        $result = model("user")->where("user_name='$username' AND user_pwd='$pwd'")->find();
        if($result) {
            Session::set("info", $result);
            return [
                "status" => 1, 
                "msg" => "登陆成功", 
                "info" => $result
            ];
        } else {
            return [
                "status" => 0, 
                "msg" => "登陆失败", 
            ];
        }
    }
    
    //注册
    public function reg() {
        $user_vali = validate("user");
        if($user_vali->check(input())) {
            $user_model = model("user");
            $user_model->allowField(true)->save(input());
            Session::set("info",[
                "user_name"=>input("user_name"),
                "user_nickname"=>input("user_nickname"),
                "user_pic"=>"",
                "id"=>$user_model->getLastInsID()
            ]);
            return [
                'status'=>1,
                "id"=>$user_model->getLastInsID()
            ];
        }else {
            return [
                'status'=>0,
                'msg'=>$user_vali->getError()
            ];
        }
    }
    
    //退出
    public function logout() {
        Session::delete("info");
        return ['status'=>1];
    }
    
    //编辑头像
    public function edit()
    {
        $create_time = time();
        $uid = input('uid');
        $user_pic = "/public/static/img/user/".$uid.'_'.$create_time.".jpg";
        $file = Request()->file("user_pic");
        $info = $file->validate(["ext"=>"jpg,png"])->move("static/img/user/", $uid.'_'.$create_time.".jpg");
        if($info) {
            $result = model("user")
                    ->where("id=$uid")
                    ->setField("user_pic", $user_pic);
            if ($result == 1) {
                Session::set("info.user_pic", $user_pic);
                return [
                    "status" => 1,
                    "msg" => "更换头像成功"
                ];
            }
        }else{
            return [
                "status"=>0,
                "msg"=>$file->getError()
            ];
        }
    }

    //获取相册
    public function getPhoto() {
        $pageStart = input('photoList');
        $uid = input('uid');
        $page = $pageStart.",9";
        $photo_list = model('weibo')
            ->field('id,pic')
            ->where('user_id='.$uid.' and pic IS NOT NULL and pic != \'\'')
            ->order('id desc')
            ->page($page)
            ->select();
        if(empty($photo_list)) {
            return [
                "status"=>0,
                "msg"=>"无更多页面"
            ];
        }else {
            $this->assign("photo_list", $photo_list);
            $html = $this->fetch("public/photo_li");
            return [
                "status"=>1,
                "msg"=>"获取相册成功",
                "html"=>$html
            ];

        }
    }

    //获取个人信息
    public function getUserInfo() {
        $result = model('user')
            ->field('*')
            ->where('id='.Session::get('info')['id'])
            ->find();
        $this->assign("item", $result);
        $html = $this->fetch("public/user_manage");
        return [
            "status"=>1,
            "msg"=>"获取信息成功",
            "html"=>$html
        ];

    }

    //修改个人信息
    public function changeInfo() {
        $result = model('user')
            ->where('id='.Session::get('info')['id'])
            ->setField([
                'user_nickname'=>input('nickname'),
                'user_pwd'=>input('pwd'),
                'brief'=>input('info')
            ]);
        $info = model('user')
            ->where('id='.Session::get('info')['id'])
            ->find();
        if ($result == 1) {
            Session::set("info", $info);
            return [
                "status" => 1,
                "msg" => "修改成功"
            ];
        }
    }
    
    //判断本地缓存是否存在
    public function check_local() {
        $id = input('id');
        $result = model("user")->where("id=".$id)->find();
        Session::set("info", $result);
        $this->assign("item", $result);
        $html = $this->fetch("/public/login_state");
        return [
            "status" => 1,
            "html" => $html,
        ];
    }

}