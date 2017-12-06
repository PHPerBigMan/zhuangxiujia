<?php
namespace app\admin\controller;

use app\common\model\Kefu;
use think\Db;
//use app\model\ProductHouse;
use app\admin\model\Charging as C;
use think\Request;


class Charging extends Base
{
    protected $charging;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->charging = new C();
    }

    public function index()
    {
        $title = "热装小区";
        return view("PcWeb/charging", compact('title'));
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取数据列表
     */
    public function GetList(){
        $data = [];

        $data = $this->charging->select();


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

        $s = $this->charging->where('id',$Get['id'])->update([
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
        if(isset($id)){
            $data = $this->charging->find($id);
        }

    }
}
