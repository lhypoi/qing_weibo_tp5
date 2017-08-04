<?php
namespace app\whome\model;

use think\Model;

class Weibo extends Model
{
    protected $table = "weibo_detail";
    
    public function commont() {
        return $this->hasMany("commet", "weibo_id");
    }
}