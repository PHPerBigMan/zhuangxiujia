<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Apply extends Base
{
   protected $model;
   public function __construct(Request $request = null)
   {
       parent::__construct($request);
       $this->model = new \app\admin\model\Apply();
   }


   public function index(){
       $title = '免费申请';
       return view('apply/icat_list',compact('title'));
   }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 返回数据
     */
   public function data(){
       $data = $this->model->select();
       foreach ($data as $v){
           $v->caozuo = "<a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"del($v->id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";

       }
       return json(['data'=>$data]);
   }

    /**
     * author hongwenyang
     * method description : 删除数据
     */

   public function del(){
       $id = input('id');
       $this->model->where('id',$id)->delete();
   }
}
