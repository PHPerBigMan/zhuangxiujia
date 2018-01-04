<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\model;
use think\Model;

class User extends Model{
    protected $table = "zjx_user";

    public function getUserSexAttr($value){
        if(!$value){
            return 0;
        }
        return $value;
    }

    public function getUserPicAttr($value){
        if(empty($value)) {
            return "/upload/normal.png";
        }
        return $value;
    }

    public function getUserNameAttr($value){
        if(empty($value)){
            return "用户";
        }
        return $value;
    }
}