<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\admin\model;
use think\Model;

class Total extends Model{
    protected $table = "zjx_total";

    public function getTypeAttr($value){
        switch ($value){
            case 0:
                $title = "正在施工";
                break;
            case 1:
                $title=  "已完成";
                break;
            default:
                $title =  "客户满意";
                break;
        }

        return $title;
    }
}