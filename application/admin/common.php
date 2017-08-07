<?php
use think\Request;
function is_active($cur_ctr,$str)
{
    if (strstr($cur_ctr,lcfirst(Request::instance()->controller()))){
        return $str;
    }

}

	

?>