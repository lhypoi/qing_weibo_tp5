<?php
function hasPhoto($val){
    return !empty($val)?$val:'__PUBLIC__/img/default.png';
}