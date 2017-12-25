<?php
namespace app\admin\controller;

use think\Db;
use think\Request;

class Banner extends Base
{
    protected  $model;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model = new \app\admin\model\Banner();
    }

    /**
     * author hongwenyang
     * method description : 广告位
     */

      public function add(){
          $listadd = $this->model->where('type','listadd')->select();
          $detailadd = $this->model->where('type','detailadd')->select();
          $title = "广告位编辑";
          return view('banner/index',compact('listadd','detailadd','title'));
      }


    /**
     * @return \think\response\View
     * author hongwenyang
     * method description : 新增/修改广告位页面
     */

      public function addupdate(){
          $title = '修改广告位';
          $map['id'] = input('id');
          if($map['id']){
              $bannerUrl = $this->model->where($map)->find();
          }else{
              $bannerUrl = json_decode("{}");
              $bannerUrl->id = "";
              $bannerUrl->banner_url = "";
              $bannerUrl->type = "listadd";
          }
          return view('banner/addupdate',compact('bannerUrl','title'));
      }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 保存广告位图片
     */

      public function save(){
          $data = input('post.');
          if($data['id']){
              // 修改
              $s = $this->model->where([
                  'type'=>$data['type'],
                  'id'=>$data['id']
              ])->update([
                  'banner_url'=>$data['bannerUrl'],
                  'type'=>$data['type']
              ]);
          }else{
              // 新增
              $s = $this->model->insert([
                  'banner_url'=>$data['bannerUrl'],
                  'type'=>$data['type']
              ]);
          }
          return returnStatus($s);
      }

      public function del(){
          $id = input('id');
          $s = $this->model->where('id',$id)->delete();
          return returnStatus($s);
      }
}
