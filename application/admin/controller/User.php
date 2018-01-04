<?php
namespace app\admin\controller;

use think\Db;

class User extends Base
{
    public function index()
    {

        $count = Db::name('User')->count();
        $j = [
            'title' => "会员列表",
            'count'=>$count,
        ];
        return view("user/index", $j);
    }

    /**
     * @return \think\response\Json
     * 用户列表
     */

    public function user_info(){
        $data = Db::name('User')->order('id','desc')->select();
        foreach($data as $k=>$v){
            $id                         = $v['id'];
            $data[$k]['caozuo']         = "<td class=\"td-manage\"> <a title=\"查看\" href=\"/admin/User/read?id=$id\"  class=\"ml-5\" style=\"text-decoration:none\"><i class=\"Hui-iconfont\">&#xe6df;</i></a> </td>";

            $head_img                   = $v['user_pic'];
            $data[$k]['user_pic']       = "<img src='$head_img' style='width: 45px;height: 45px'>";
            $data[$k]['user_sex']       = $v['user_sex'] == 0 ? "女" : "男";
        }

        return json(['data'=>$data]);
    }
    /**
     * @return \think\response\View
     * 用户详情
     */

    public function read(){
        $id = input('id');

        $data = Db::name('User')->where(['id'=>$id])->field('id,user_name,user_phone,user_area,user_pic,user_sex,user_money,create_time')->find();

        $data['user_sex']       = $data['user_sex'] == 0 ? "女" : "男";
        if(empty($data)){
            $data['user_name'] = "";
            $data['user_phone'] = "";
            $data['user_area'] = "";
            $data['user_pic'] = "";
            $data['user_sex'] = "";
            $data['user_money'] = "";
            $data['create_time'] = "";
        }
        $j = [
            'title'=>"会员详情",
            'data'=>$data
        ];
        return view('user/read',$j);
    }

    /**
     * @return \think\response\View
     * 用户案例列表
     */

    public function anli_list()
    {

        $count = Db::name('UserAnli')->count();
        $j = [
            'title' => "用户案例列表",
            'count'=>$count,
        ];
        return view("user/user_anli", $j);
    }

    /**
     * @return \think\response\Json
     * 用户案例列表 Datatables数据
     */

    public function anli_ajax(){
        $data = Db::name('UserAnli')->field('id,user_id,title,jidian,is_pass,pic,create_time')->order('id','desc')->select();
        foreach($data as $k=>$v){
            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $data[$k]['user']        = Db::name('User')->where(['id'=>$v['user_id']])->value('user_name');
            $data[$k]['is_pass']     = $v['is_pass'] == 0 ? "<span class=\"label label-defaunt radius\">审核未通过</span>" :"<span class=\"label label-success radius\">审核已通过</span>";
            $data[$k]['jidian']      = $v['jidian'] == 0 ?"<span class=\"label label-defaunt radius\">暂未推荐为经典案例</span>" :"<span class=\"label label-success radius\">已推荐为经典案例</span>";
            $pic                     = json_decode($v['pic'],true);
            $data[$k]['pic']         = "<img src='$pic[0]' style='width: 45px;height: 45px'>";
            $id                      = $v['id'];
            $data[$k]['caozuo']      = "<a style=\"text-decoration:none\" onClick=\"anli_shenhe(this,$id)\" href=\"javascript:;\" title=\"审核\">审核</a> | <a style=\"text-decoration:none\" onclick=\"anli_tuijian(this,$id)\">推荐</a>
 | <a href=\"/admin/Anli/read?id=$id\"><i class=\"Hui-iconfont\">&#xe6df;</i></a>";
        }

        return json(['data'=>$data]);
    }


    public function cat(){
        $j = [
            'title'=>'案例分类'
        ];

        return view('user/cat',$j);
    }

    public function cat_ajax(){
        $data = Db::name('AnliCat')->select();
        foreach($data as $k=>$v){
            if($v['level'] == 1){
                $data[$k]['p_name'] = '顶级分类';
            }else{
                $data[$k]['p_name'] = Db::name('AnliCat')->where(['id'=>$v['p_id']])->value('cat_name');
            }

            $cat_name = $v['cat_name'];
            $data[$k]['caozuo'] = "<a title='编辑' onclick=\"anli_cat_edit(".$v['id'].",'$cat_name')\"><i class='Hui-iconfont' >&#xe6df;</i></a> | 
  <a style='text-decoration:none' class='ml-5' onclick='cat_del(".$v['id'].")'><i class='Hui-iconfont'>&#xe6e2;</i></a>";

        }

        return json(['data'=>$data]);
    }

    /**
     * author hongwenyang
     * method description : 修改分类
     */

    public function save(){
        $data = input('post.');

        if($data['id'] == 0){

            $insert = $data['data'];
            $insert['create_time'] = time();
            $insert['is_show'] = 1;
            $insert['p_id'] = empty($data['data']['p_id']) ? 0 : $data['data']['level'] == 1 ? 0 :  $data['data']['p_id'];
            Db::name('AnliCat')->insert($insert);
        }else{
            Db::name('AnliCat')->where(['id'=>$data['id']])->update([
                'cat_name'=>$data['cat_name']
            ]);
        }

        return json(['code'=>200]);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 删除分类
     */

    public function del(){
        $id = input('id');
        $is_have = Db::name('AnliCat')->where(['p_id'=>$id])->find();
        if(!empty($is_have)){
            $msg['code'] = 404;
            $msg['msg']  = '改分类下有二级分类,不能删除!!!!';
        }else{
            Db::name('AnliCat')->where(['id'=>$id])->delete();
            $msg['code']    = 200;
            $msg['msg']     = '删除成功';
        }

        return json($msg);
    }

    public function add(){
        $data = Db::name('AnliCat')->where(['level'=>1])->select();
        $j = [
            'title'=>'新增分类',
            'data'=>$data
        ];

        return view('user/add',$j);
    }
}
