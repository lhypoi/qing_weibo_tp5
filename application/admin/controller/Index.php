<?php
namespace app\admin\controller;

use think\Controller;
class Index extends Controller
{
    public function admin()
    {
        return $this->fetch();
    }
}

?>