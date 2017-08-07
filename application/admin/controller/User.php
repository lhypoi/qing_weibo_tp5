<?php
namespace app\admin\controller;

use think\Controller;

class User extends Controller{

    public function index()
    {
//        withCount : 统计user用户表下面每个用户有多少条微博
       $list=  model("user")->withCount("weibo")->select();

       $this->assign("list",$list);
       return $this->fetch();

    }
    public function add()
    {
        return $this->fetch();
    }

    public function save()
    {
//        添加操作
//        自动完成：创建时间都可以无需手动定义
        $valiAdmin = validate("admin");
        if ($valiAdmin->check($_POST)){
            // $_POST['admin_pwd'] = md5($_POST['admin_pwd']);
            model("admin")->admin_pwd = $_POST['admin_pwd'];
            model("admin")->save($_POST);
        }else{
            print_r($valiAdmin->getError());
        }

//        自动校验：管理员用户名到底存不存在
    }
}



?>