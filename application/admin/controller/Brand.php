<?php
namespace app\admin\controller;

use app\admin\model\MallCat;
use app\common\model\Kefu;
use think\Db;
//use app\model\brand;
use app\admin\model\Brand as P;
use think\Request;


class Brand extends Base
{
    protected $brand;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->brand = new P();
    }

    public function index()
    {

        $title = "品牌展示";
        return view("PcWeb/brand", compact('title'));
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取数据列表
     */
    public function GetList(){

        $data = [];
        $data = $this->brand->select();

        foreach ($data as $v){
            $v->url = "<img class='layui-upload-img product_suolve' id='demo1' src='$v->url'>";
            $v->caozuo = "<a href=\"read?id=$v->id\" title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | 
 <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"del(this,$v->id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";
        }
        return json(['data'=>$data]);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 修改为是否推荐
     */

    public function handel(){
        $Get = input('post.');

        $s = $this->brand->where('id',$Get['id'])->update([
            'is_hot'=>$Get['is_hot']
        ]);

        return returnStatus($s);
    }


    /**
     * author hongwenyang
     * method description : 详情
     */

    public function read(){
        $id = input('id');
        $title = '产品展示操作';
        if(isset($id)){
            $data = $this->brand->find($id);

        }else{
            $data = json_decode("{}");
            $data->url = "";
            $data->id = "";
            $data->type = "";
            $data->content = "";
        }
        //商品分类
        $mall = MallCat::select();

        return view('PcWeb/brandRead',compact('data','title','mall'));
    }

    /**
     * author hongwenyang
     * method description : 保存返回图片路径
     * @return string
     */

    public function saveImg(){
        return json_encode(makeImg());
    }

    /**
     * * author hongwenyang
     * method description : 保存数据
     * @return mixed
     */

    public function save(){
        $data = input('post.');
        if(!empty($data['id'])){
            // 修改
            $map['id'] = $data['id'];
            unset($data['id']);
            $s = $this->brand->where($map)->update($data);
        }else{
            // 新增
            $s = $this->brand->insert($data);
        }
        return returnStatus($s);
    }

    public function del(){
        $id = input('id');
        $s = $this->brand->where('id',$id)->delete();
        return returnStatus($s);
    }
}
