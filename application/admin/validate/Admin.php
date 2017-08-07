<?php
/**
 * Created by PhpStorm.
 * User: graphic
 * Date: 2017/8/3
 * Time: 11:48
 */

namespace app\admin\validate;


use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        "admin_name" => 'require|length:5,12|unique:admin',
        "admin_pwd" => 'require|length:6,12',
    ];

    protected $message= [
        "admin_name.require" => "管理员用户名不能为空！",
        "admin_name.length" => "用户名长度必须在5到12位",
        "admin_name.unique" => "用户名已经存在",
        "admin_pwd.require" => "密码不能为空！",
        "admin_pwd.length" => "密码长度必须在6到12位",
    ];
}