<?php
namespace app\admin\controller;

use app\common\model\Kefu;
use think\Db;
use think\Request;

class Content extends Base
{
    protected $model;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model = new \app\admin\model\Content();
    }

    public function index(){
        $data = $this->model->find();
        $title = 'content';
        return view('content/index',compact('data','title'));
    }


    public function update(){
        $id = input('id');
        $data = $this->model->where('id',$id)->find();
        $title = '编辑';
        return view('content/read',compact('data','title'));
    }

    public function saveImg(){
        $file = request()->file('file');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $filename = '/uploads/'.$info->getSaveName();
            }
        }
        return json_encode($filename);
    }

    public function save(){
        $data = input();
//        dump($data);
        $s = $this->model->where('id',$data['id'])->update([
            'phone'=>$data['phone'],
            'qrcode'=>$data['qrcode'],
            'title'=>$data['title']
        ]);

        return returnStatus($s);
    }
}
