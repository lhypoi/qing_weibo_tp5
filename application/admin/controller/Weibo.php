<?php
namespace app\admin\controller;

use think\Controller;

class Weibo extends Controller{

    public function index()
    {
      $weibo_list =  db("detail")->paginate(10);

      $this->assign("weibo_list",$weibo_list);
       return $this->fetch();

    }

    // 保存无页面刷新更改的数据
    public function ajaxTextEdit()
    {
        // 要修改的id
        // 要修改的字段field
       
        db("detailed")
            ->where("id=".input('id'))
             ->update([
                          input("field")=>input('newV')
                       ]);

        return ['status'=>1];
    }
    
}



?>