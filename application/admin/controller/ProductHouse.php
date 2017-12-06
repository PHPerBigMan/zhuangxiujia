<?php
namespace app\admin\controller;

use app\common\model\Kefu;
use think\Db;
//use app\model\ProductHouse;
use app\admin\model\ProductHouse as P;
use think\Request;


class ProductHouse extends Base
{
    protected $productHouse;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->productHouse = new P();
    }

    public function index()
    {

        $title = "产品展示";
        return view("PcWeb/productHouse", compact('title'));
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取数据列表
     */
    public function GetList(){

        $data = [];
        $data = $this->productHouse->select();

        foreach ($data as $v){
            $v->suolve = "<img class='layui-upload-img product_suolve' id='demo1' src='$v->suolve'>";
            $v->caozuo      = "<a style=\"text-decoration:none\" onclick=\"hot(this,$v->id)\">推荐</a>
                | <a href=\"/admin/product_house/read?id=$v->id\"><i class=\"Hui-iconfont\">&#xe6df;</i></a>";
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

        $s = $this->productHouse->where('id',$Get['id'])->update([
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
            $data = $this->productHouse->find($id);
            $data->lunbojson = implode(',',$data->lunbo);
        }else{
            $data = json_decode("{}");
            $data->suolve = "";
            $data->lunbo = "";
            $data->lunbojson = "";
            $data->id = "";
        }
        return view('PcWeb/product',compact('data','title'));
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
        if(!empty($data['lunbo'])){
            $lunbo = explode(',',$data['lunbo']);
            if(empty($data['id'])){
                unset($lunbo[0]);
                sort($lunbo);
            }
            $data['lunbo'] = json_encode($lunbo);
        }
        if(!empty($data['id'])){
            // 修改
            $map['id'] = $data['id'];
            unset($data['id']);
            $s = $this->productHouse->where($map)->update($data);
        }else{
            // 新增
            $s = $this->productHouse->insert($data);
        }
        return returnStatus($s);
    }
}
