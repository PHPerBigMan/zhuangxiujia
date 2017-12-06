<?php
namespace app\index\controller;

use think\Db;

class Jiancai
{
    /**
     * @return \think\response\Json
     * 建材列表
     */

      public function index(){
         $data = Db::name('MallCat')->where(['level'=>1])->field('id,cat_name')->select();
         foreach ($data as $k=>$v){
             $data[$k]['child'] = Db::name('MallCat')->where(['p_id'=>$v['id']])->field('id sec_id,cat_name,img')->select();
         }
         $j = $this->return_data($data);

          return json($j);
      }


    /**
     * @param $id
     * @return array
     * 处理数据
     */

      protected function mall_cat($id){
          if($id == ""){
              $data     = Db::name('MallCat')->where(['level'=>1])->field('id,cat_name,level')->select();
              $first    = Db::name('MallCat')->where(['level'=>1])->value('id');
              $sec_data = Db::name('MallCat')->where(['p_id'=>$first])->field('id sec_id,cat_name,level,img')->select();
          }else{
              $data     = Db::name('MallCat')->where(['level'=>1])->field('id,cat_name,level')->select();
              $sec_data = Db::name('MallCat')->where(['p_id'=>$id])->field('id sec_id,cat_name,level,img')->select();
          }
          if(empty($data)){
              $msg = '数据为空';
          }else{
              $msg = '获取数据成功';
          }

          $j = [
              'status'=>200,
              'msg'   =>$msg,
              'data'  =>$data,
              'sec'   =>$sec_data
          ];

          return $j;
      }


    /**
     * @return \think\response\Json
     * 商品
     */

      public function product(){
          $id = input('sec_id');
          $data = Db::name('Product')->where(['pro_cat'=>$id,'is_show'=>1])->field('id pro_id,pro_name,pro_price,pro_img,pro_url')->select();
          $j = $this->return_data($data);
          return json($j);
      }


    public function return_data($data){
        if(empty($data)){
            $data = array();
            $msg = '数据为空';
        }else{
            $msg = '获取数据成功';
        }
        $j = [
            'status'=>200,
            'msg'   =>$msg,
            'data'=>$data
        ];
        return $j;
    }


}
