<?php
namespace app\admin\controller;

use app\common\model\Kefu;
use think\Db;
use think\Request;

class About extends Base
{
    protected $model;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model = new \app\admin\model\About();
    }

    /**
     * @return \think\response\View
     * author hongwenyang
     * method description : 文章
     */
    public function index(){
        $data = $this->model->select();
        $title= 'Go了解我管理';
        return view('about/index',compact('data','title'));
    }


    public function Update(){
        $id = input('id');
        $data = $this->model->where('id',$id)->find();
        $title = '文章管理';
        return view('about/read',compact('data','title'));
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 保存数据
     */
    public function save(){
        $data = input('post.');
        $s = $this->model->where('id',$data['id'])->update([
            'content'=>$data['content'],
            'title'=>$data['title']
        ]);

        return returnStatus($s);
    }
}
