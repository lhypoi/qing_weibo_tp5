<?php
namespace app\whome\controller;

use think\Controller;
use think\Session;
class User extends Controller
{
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
    
    public function logout() {
        Session::delete("info");
        return ['status'=>1];
    }
}