<?php
namespace app\admin\controller;

use app\common\model\Kefu;
use think\Db;
//use app\model\total;
use app\admin\model\Total as P;
use think\Request;


class Total extends Base
{
    protected $total;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->total = new P();
    }

    public function index()
    {

        $title = "施工统计";
        return view("PcWeb/total", compact('title'));
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取数据列表
     */
    public function GetList(){

        $data = [];
        $data = $this->total->select();

        foreach ($data as $v){
            $v->caozuo = "<a onclick =edit($v->id,$v->total) title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a>";
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

        $s = $this->total->where('id',$Get['id'])->update([
            'is_hot'=>$Get['is_hot']
        ]);

        return returnStatus($s);
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
            $s = $this->total->where($map)->update($data);
        }else{
            // 新增
            $s = $this->total->insert($data);
        }
        return returnStatus($s);
    }

    public function del(){
        $id = input('id');
        $s = $this->total->where('id',$id)->delete();
        return returnStatus($s);
    }
}
