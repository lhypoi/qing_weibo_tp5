<?php
namespace app\admin\controller;
use think\Controller;

/**
* 登陆控制器
*/
class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function check_verify(){
        return input('code');
    }
}
 ?>