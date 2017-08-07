<?php
/**
 * Created by PhpStorm.
 * User: graphic
 * Date: 2017/8/3
 * Time: 11:36
 */

namespace app\admin\model;

//管理员
use think\Model;

class Admin extends Model
{
    protected $insert = ["create_time","admin_pwd"];//用来定义只在添加操作自动完成的字段
    protected $updateTime = '';

//    自动完成，需要我们自己写字段要生成的格式
   public function setAdminPwdAttr($val)
   {
       return md5($val);
   }
}