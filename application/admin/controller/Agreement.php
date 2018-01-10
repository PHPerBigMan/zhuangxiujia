<?php
namespace app\admin\controller;


class Agreement extends Base
{
    protected $model;

    public function index(){
        $title = "用户协议";

        $data = \app\model\Agreement::find();

        $data->agreement = mb_substr($data->agreement,0,40);
        return view('agreement/index',compact('title','data'));
    }

    public function Update(){
        $id = input();

        $data = \app\model\Agreement::findOrFail($id);

        $title = '编辑用户协议';

        return view('agreement/read',compact('data','title'));
    }

    public function save(){
        $post = input();

        $s = \app\model\Agreement::where('id',$post['id'])->update([
            'agreement'=>$post['agreement']
        ]);

        return returnStatus($s);
    }
}
