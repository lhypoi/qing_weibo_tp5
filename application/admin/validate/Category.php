<?php
namespace app\admin\validate;
use think\Validate;

class Category extends Validate{
    protected $rule = [
        'cate_name' => 'require|length:2,10|unique:star', //验证星星不能为空
    ];

    protected  $message = [
        'cate_name.require' => '名称不能为空！',
        'cate_name.length' => '名称必须在2到10个字之间！',
        'cate_name.unique' => '名称不能重复！',
    ];

//    针对编辑的时候不需要做唯一验证，那么就重新第一指定的规则
    protected $scene = [
        'edit' =>  ['cate_name'=>'require|length:2,10']
    ];
}