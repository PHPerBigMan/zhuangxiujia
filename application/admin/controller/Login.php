<?php
namespace app\admin\controller;

use app\common\model\Admin;
use think\Controller;
use think\Db;
use think\Request;

class Login extends Controller
{
    public function Index()
    {
       return view();
    }

    public function app()
    {
        return view();
    }

    /**
     * 后台管理员登录
     */
    public function login()
    {
        $admin_name     = input('admin_name');
        $admin_pwd      = input('admin_pwd');
        $admin_check    = Db::name('admin')->field('admin_pwd,id')->where('admin_name',$admin_name)->find();

        if(empty($admin_check)){
            $code = 100;
            $msg  = "管理员不存在，请检查登录名";
        }else{
           if(sha1($admin_pwd) != $admin_check['admin_pwd']){
               $code = 300;
               $msg  =  "密码不正确";

           } else{
               $code = 200;
               $msg  = "登录成功";

               if(input('type') != 1){
                   session('app_admin',$admin_check['id']);
               }
           }
        }

        $j = [
            "code"  =>$code,
            'msg'   =>$msg
        ];

        return json($j);
    }



    /**
     * 退出登录
     */
    public function logout()
    {
        session('admin',null);
        session('app_admin',null);
        $msg = "退出成功";
        return json($msg);
    }
}
