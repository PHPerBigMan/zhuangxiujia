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
            $v->caozuo = "<a style=\"text-decoration:none\" onclick=\"hot(this,$v->id)\">推荐</a>
 | <a href=\"read?id=$v->id\" title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | 
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
        $title = '热装小区操作';
        if(isset($id)){
            $data = $this->charging->find($id);
            $data->lunbocount = count($data->lunbo);
            if(!empty($data->lunbo)){
                $data->lunbojson = implode(',',$data->lunbo);
            }else{
                $data->lunbojson = "";
            }
        }else{
            $data = json_decode("{}");
            $data->suolve = "";
            $data->lunbo = "";
            $data->lunbojson = "";
            $data->id = "";
            $data->lunbocount = 0;
        }
        // 区域数据
        $area = Db::name('hat_area')->where('father',330100)->select();
//        dd($area);
        return view('PcWeb/chargingRead',compact('data','title','area'));
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
            if($lunbo[0] == ""){
                unset($lunbo[0]);
            }
            sort($lunbo);
            $data['suolve'] = $lunbo[0];
            $data['lunbo'] = json_encode($lunbo);
        }
//        dd($data);
        if(!empty($data['id'])){
            // 修改
            $map['id'] = $data['id'];
            unset($data['id']);
            $s = $this->charging->where($map)->update($data);
        }else{
            // 新增
            $s = $this->charging->insert($data);
        }
        return returnStatus($s);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 删除数据
     */
    public function del(){
        $id = input('id');
        $s = $this->charging->where('id',$id)->delete();
        return returnStatus($s);
    }
}
