<?php
function hasPhoto($val){
    return !empty($val)?$val:'static/img/default.png';
}

function returnjson ($status, $msg, $html='',$err='', $other='') {
    echo json_encode(
        array(
            "status"=>$status,
            "msg"=>$msg,
            "html"=>$html,
            "err"=>$err,
            "other"=>$other
        )
    );
}