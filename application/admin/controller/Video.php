<?php
namespace app\admin\controller;

use app\common\model\Kefu;
use think\Db;
use think\Request;

class Video extends Base
{
    protected $model;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model = new \app\admin\model\Banner();
    }

    public function index(){
        $data = $this->model->where('type','video')->find();

        $title = "视频管理";

        return view('video/index',compact('title','data'));
    }

    public function Update(){
        $data = $this->model->where('id',input('id'))->find();
        $id = input('id');
        $title = '视频编辑';
        return view('video/read',compact('data','title','id'));
    }


    /**
     * author hongwenyang
     * method description : 保存视频文件
     */

    public function save(){
        $file = request()->file('file');

        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        $url = DS . 'uploads'.DS.$info->getSaveName();
        $this->model->where('id',input('id'))->update([
            'banner_url'=>$url
        ]);
        return $this->success('保存成功','/admin/video/index');
    }
}
