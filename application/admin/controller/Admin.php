<?php
namespace app\admin\controller;

use app\common\model\Kefu;
use think\Db;

class Admin extends Base
{
    public function index()
    {
        $data = Db::name('Admin')->find();
        $j = [
            'data'=>$data,
            'title' => "登录管理",
        ];
        return view("admin/index", $j);
    }


    public function edit(){
        $id = input('id');
        $data = Db::name('Admin')->where(['id'=>$id])->find();
        $j = [
            'title'=>"后台管理----修改密码",
            'data'=>$data,
            'id'   =>$id
        ];
        return view('admin/edit',$j);
    }

    /**
     * @return \think\response\Json
     * 执行修改操作
     */

    public function admin_edit(){
        $id = input('id');
        $admin_pwd = input('password');
        $a = Db::name('Admin')->where(['id'=>$id])->update([
            'admin_pwd'=>sha1($admin_pwd)
        ]);
        if($a){
            $msg['code']  = 200;
            $msg['msg'] ="修改成功! 请重新登录";
            session('admin',NULL);
        }else{
            $msg['code'] = 100;
            $msg['msg'] = "修改失败";
        }

        return json($msg);
    }

    /**
     * @return \think\response\View
     * 客服电话页面
     */

    public function kefu(){
        $data = Db::name('kefu')->find();
        if(empty($data)){
            $data = array();
        }
        $j = [
            'title'=>"客服电话",
            'data'=>$data
        ];

        return view('admin/kefu',$j);
    }

    /**
     * @return \think\response\View
     * 添加客服电话表单页面
     */

    public function add_tel(){
        $kefu_id = input('id');

        if(empty($kefu_id)){
            $data['tel'] = "";
            $id = '';
        }else{
            $data = Db::name('Kefu')->where(['id'=>$kefu_id])->find();
            $id = $data['id'];
        }

        $j = [
            'title'=>"添加客服电话",
            'data'=>$data['tel'],
            'id'=>$id
        ];

        return view('admin/add_kefu',$j);
    }

    public function kefu_edit(){
        $id = input('id');
        $tel = input('tel');
        if($id == ""){
            //添加客服电话
            $kefu = new Kefu();
            $kefu->tel = $tel;
            $kefu->create_time = time();
            $s = $kefu->save();
        }else{
            $s = Db::name('Kefu')->where(['id'=>$id])->update([
                'tel'=>$tel
            ]);
        }
        if($s){
            $msg['code'] = 200;
            $msg['msg'] = "操作成功";
        }else{
            $msg['code'] = 100;
            $msg['msg'] = "操作失败";
        }

        return json($msg);
    }
}
