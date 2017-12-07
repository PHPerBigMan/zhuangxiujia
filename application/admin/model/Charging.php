<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\admin\model;
use think\Model;

class Charging extends Model{
    protected $table = "zjx_charging";



    public function getIsHotAttr($value){

        if($value){
            return '<span class="label label-success radius">已推荐</span>';
        }else{
            return '<span class="label label-defaunt radius">未推荐</span>';
        }
    }

    public function getLunboAttr($value){
        return json_decode($value,true);
    }
}