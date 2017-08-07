<?php
namespace app\admin\controller;
use think\captcha\Captcha;
use think\Controller;

// 缺点：
// 
// psr

class Login extends Controller{

	public function index()
	{
		// 是去到view里面对应的login文件夹下面的index页面
		return $this->fetch();

 
	}


	public function doLogin()
	{
	   $user_captach = input("captach");

	    $captcha = new Captcha();
//        if($captcha->check($user_captach)){
            $where_data = [
                "admin_name"=>input("admin_name"),
                "admin_pwd"=>md5(input("admin_pwd")),
            ];

//        where sql语句条件
            $info = db("admin")->field("id")->where($where_data)->find();
//       echo db("admin")->getLastSql();

            if ($info){
               $this->success("登录成功",url("user/index"));
            }else{
                $this->error("用户名或密码不正确");
            }

//        }else{
//            echo "验证码不正确";
//        }
	}



     
}

?>