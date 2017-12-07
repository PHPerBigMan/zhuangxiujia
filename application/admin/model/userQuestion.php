<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\admin\model;
use think\Model;

class userQuestion extends Model{
    protected $table = "zjx_user_question";

    public function getIsHotAttr($value){
        if($value){
            return "是";
        }
        return "否";
    }
}