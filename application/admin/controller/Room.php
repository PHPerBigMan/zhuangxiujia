<?php
namespace app\admin\controller;

use think\Db;

class Room extends Base
{
    public function index()
    {
        $j = [
            'title'     =>"装修列表",
        ];
        return view("room/index",$j);
    }




    public function room_ajax(){
        $data = Db::name('Zx')->select();
        foreach ($data as $k=>$v){
            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $data[$k]['user_name'] = Db::name('User')->where(['id'=>$v['user_id']])->value('user_name');
            $id = $v['id'];
            $data[$k]['caozuo'] = "<a href=\"/admin/Room/read?id=$id\" title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a>  | 
 <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"room_del(this,$id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";
        }

        return json(['data'=>$data]);
    }



    public function read(){
        $id = input('id');
        $data = Db::name('Zx')->where(['id'=>$id])->find();

        $data['user_name'] = Db::name('User')->where(['id'=>$data['user_id']])->value('user_name');
        $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);
        $j = [
            'title'=>'装修详情',
            'data'=>$data
        ];

        return view('room/read',$j);
    }


    public function del(){
        $id = input('id');
        $s = Db::name('Zx')->where(['id'=>$id])->delete();

        if($s){
            $code = 200;
        }

        return json($code);
    }
}
