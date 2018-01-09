<?php
namespace app\admin\controller;

use think\Db;

class Index extends Base
{
    public function index()
    {
        $admin_name = Db::name('admin')->where('id',session('app_admin'))->value('admin_name');

        $j = [
            'title'     =>"后台首页",
            'admin_name'=>$admin_name
        ];
        return view("index/web",$j);
    }

    public function app()
    {
        $admin_name = Db::name('admin')->where('id',session('app_admin'))->value('admin_name');

        $j = [
            'title'     =>"后台首页",
            'admin_name'=>$admin_name
        ];
        return view("index/app",$j);
    }


    /**
     * @return \think\response\View
     * 我的桌面统计数据
     */

    public function welcome(){
        //用户总量
        $user_count = Db::name('User')->count();
        //任务总量
        $rw_count = Db::name('Renwu')->count();
        //订单总量
        $order_count = Db::name('UserRw')->count();
        $j = [
            'title'         =>'我的桌面',
            'order_count'   =>$order_count,
            'user_count'    =>$user_count,
            'rw_count'      =>$rw_count
        ];
        return view('index/welcome',$j);
    }
}
