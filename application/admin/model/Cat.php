<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\admin\model;
use think\Model;

class Cat extends Model{
    protected $table = "zjx_cat";

    protected $field = true;

    /**
     * @param $data
     * @return \think\response\Json
     * author hongwenyang
     * method description :保存分类数据
     */
    public static function saveCat($data){
        $f= $data['f'];
        if(!empty($data['fcat_name']) || !empty($data['scat_name'])){
            if($data['type'] == 0){
                //二级分类
                $data['level'] = 2;
                $data['cat_name'] = $data['scat_name'];
            }else{
                //一级分类
                $data['level'] = 1;
                $data['p_id'] = 0;
                $data['cat_name'] = $data['fcat_name'];
                $data['cat_img']   = $data['cat_img_save'];
            }
        }
        if(!$f){
            $data['create_time'] = time();
            $s = Cat::create($data);
            if($s){
                $code = 200;
            }
        }else{
            $data['create_time'] = time();
            unset($data['f']);
            unset($data['type']);
            unset($data['fcat_name']);
            unset($data['scat_name']);
            unset($data['cat_img_save']);
            $s = Cat::where(['id'=>$data['id']])->update($data);
            if($s){
                $code = 200;
            }
        }


        return json($code);
    }
}