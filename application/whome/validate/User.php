<?php
namespace app\whome\validate;

//用户注册自动验证类

use think\Validate;

class User extends Validate
{
    //用户名不能为空，必须不能重复
    protected $rule =[
        "user_name" =>"require|unique:user|min:3|max:20",
        "user_nickname" => "require|unique:user",
        "user_pwd" => "confirm:user_repwd",
    ];

    protected $message = [
        "user_name.require" => "用户名不能为空",
        "user_name.unique" => "用户名已经存在！",
        "user_nickname.require" => "用户名不能为空",
        "user_nickname.unique" => "用户名已经存在！",
        "user_name.min" => "用户名必须大于3位小于20位",
        "user_name.max" => "用户名必须大于3位小于20位",
        "user_pwd.confirm" => "密码和确认密码不一致！",
    ];
}