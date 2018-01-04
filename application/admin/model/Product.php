<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\admin\model;
use think\Model;

class Product extends Model{
    protected $table = "zjx_product";

    protected $field = true;

    public static function saveProduct($data){
        $id = $data['id'];
        // 获取分类 的一级分类
        $data['pro_first'] = MallCat::where('id',$data['pro_cat'])->value('p_id');
        $data['is_show'] = 1;
        if($data['id'] ==0){
            $s = Product::insert($data);
        }else{

            $s = Product::where(['id'=>$id])->update($data);

        }
        if($s){
            $code = 200;
        }else{
            $code = 404;
        }
        return json($code);
    }
}