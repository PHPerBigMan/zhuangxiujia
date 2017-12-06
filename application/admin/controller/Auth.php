<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Auth extends Base
{

    public function Jlist(){
        $data = Db::name('AdminGroup')->select();
        $j = [
            'title'=>'账号列表',
            'data'=>$data
        ];
        return view('auth/index',$j);
    }


    /**
     * @return \think\response\View
     * 角色新增
     */

    public function admin_add(){
        $gid    = input('gid');
        if($gid != 0) {
            $role   = Db::name('AdminGroup')->where(['gid'=>$gid])->find();
            $role['rule'] = explode(',',$role['rule']);
        }else{
            $role['title']      = "";
            $role['miaoshu']    = "";
            $role['gid']        = 0;
            $role['rule']       = array();
        }

        //权限类型
        $data = Db::name('AdminRuleCat')->select();
        foreach($data  as $k=>$v){
            $data[$k]['desc'] = Db::name('AdminRule')->where(['classify'=>$v['cid']])->select();
        }

        $j = [
            'title'=>'添加角色',
            'data'=>$data,
            'role'=>$role
        ];
        return view('auth/add',$j);
    }

    /**
     * @return \think\response\Json
     * 保存角色信息
     */
    public function role_save(){
        $data['rule']  = input('rule/a');
        $gid  = input('gid');
        $data['title']  = input('title');
        $data['miaoshu']  = input('miaoshu');
        if (strlen(trim($data['title']))>0 && strlen(trim($data['miaoshu']))>0)
        {
            if($gid == 0){
                $data['rule']  = empty(input('rule/a')) ? "":implode(',',input('rule/a'));
                $s = Db::name('AdminGroup')->insert($data);
            }else{
                $s = Db::name('AdminGroup')->where(['gid'=>$gid])->update([
                    'title'=> $data['title'],
                    'rule'=>implode(',',$data['rule']),
                    'miaoshu'=>$data['miaoshu']
                ]);
            }
            if($s){
                $code = 200;
            }
        }else{
            $code=10;
        }





//        dump($code);die;
        return json($code);
    }

    /**
     * @return \think\response\Json
     * 删除角色
     */

    public function del(){
        $gid = input('gid');
        $s   = Db::name('AdminGroup')->where(['gid'=>$gid])->delete();
        if($s){
            $code = 200;
        }

        return json($code);
    }


    public function Rlist(){
        $data = Db::name('Admin')->select();
        foreach($data as $k=>$v){
            $data[$k]['admin_juese'] = Db::name('AdminGroup')->where(['gid'=>$data[$k]['admin_juese']])->value('title');
        }

        $j = [
            'title'=>'账号管理',
            'data'=>$data
        ];

        return view('auth/list',$j);
    }


    /**
     * @return \think\response\View
     * 新增管理员
     */

    public function role_add(){
        $id     = input('id');
        if($id != 0){
            $data   = Db::name('Admin')->where(['id'=>$id])->find();
        }else{
            $data['admin_name'] ="";
            $data['admin_pwd'] = "";
            $data['admin_juese'] = 3;
            $data['id'] = 0;
        }
        $group = Db::name('AdminGroup')->select();
        $j = [
            'title'=>'账号管理',
            'data'=>$data,
            'group'=>$group
        ];

        return view('auth/role_add',$j);
    }


    public function role_edit(){
        $id = input('id');
        $data['admin_name'] = input('admin_name');
        $data['admin_pwd']  = input('admin_pwd');
        $data['admin_juese'] = input('admin_juese');
//        echo $admin_juese;die;
        if($id == 0){
            $data['admin_pwd'] = sha1($data['admin_pwd']);
            $s = Db::name('Admin')->insert($data);
        }else{
            $s = Db::name('Admin')->where(['id'=>$id])->update([
                'admin_name'=> $data['admin_name'],
                'admin_pwd'=>Db::name("Admin")->where(['id'=>$id])->value('admin_pwd') == $data['admin_pwd'] ? $data['admin_pwd'] :sha1($data['admin_pwd']) ,
                'admin_juese'=> $data['admin_juese']
            ]);
        }
        if($s){
            $code = 200;
        }

        return json($code);
    }


    /**
     * @return \think\response\Json
     * 删除账号
     */

    public function role_del(){
        $id = input('id');
        $s = Db::name('Admin')->where(['id'=>$id])->delete();
        if($s){
            $code = 200;
        }

        return json($code);
    }
}
