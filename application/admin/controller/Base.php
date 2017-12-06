<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Base extends Controller
{
    public function _initialize()
    {
//        session('admin',1);
        mb_internal_encoding("UTF-8");
        if (\think\Request::instance()->controller() != 'Login') {
            if (! session('admin')) {
                $this->redirect('login/index');
            }else{
                //当前路径
                $url = '/'.\think\Request::instance()->module().'/'.\think\Request::instance()->controller().'/'.\think\Request::instance()->action();

                $other_action = array('index','welcome','anli_list','zlist','save','img','session_del');
                //用户所在组
                $admin_juese = Db::name('Admin')->where(['id'=>session('admin')])->value('admin_juese');
                $rule = Db::name('AdminGroup')->where(['gid'=>$admin_juese])->value('rule');

                $temp_arr = explode(',',$rule);


                $map['identifying'] = $url;
                $rule_id = Db::name('AdminRule')->where(['identifying'=>$url])->value('id');

                if($admin_juese != "3"){
                    if(is_null($rule_id) || !in_array($rule_id,$temp_arr)){
                        if(!in_array(\think\Request::instance()->action(),$other_action)){
                           $this->error("您没有权限","zxj.hwy.sunday.so/admin/index/index.html",3);
//                            echo \think\Request::instance()->action();
//                            echo $other_action[0];
//                            var_dump($other_action);die;
                        }
                    }
                }
            }
        }
    }

}
