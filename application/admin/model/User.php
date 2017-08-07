<?php
/**
 * Created by PhpStorm.
 * User: graphic
 * Date: 2017/8/3
 * Time: 15:28
 */

namespace app\admin\model;


use think\Model;

class User extends Model
{
    public function weibo()
    {
       return $this->hasMany("detailed","uid");
    }
}