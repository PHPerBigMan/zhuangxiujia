<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\admin\model;
use think\Model;

class Construction extends Model{
    protected $table = "zjx_construction";

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

    public function getStatusAttr($value){
       switch ($value){
           case 0:
               $title = "开工";
               break;
           case 1:
               $title = "水电";
               break;
           case 2:
               $title = "泥木";
               break;
           case 3:
               $title = "油工";
               break;
           default:
               $title = "竣工";
               break;
       }

       return $title;
    }
}