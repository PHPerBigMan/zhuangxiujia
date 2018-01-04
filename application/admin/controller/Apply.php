<?php

namespace app\admin\controller;

use app\admin\model\Demand;
use think\Controller;
use think\Db;
use think\Request;

class Apply extends Base
{
    public function Icat(){
        $j = [
            'title'=>'申请类型列表'
        ];

        return view('apply/icat',$j);
    }


    public function cat_ajax(){
        $type = input('type');
        switch ($type){
            case 1:
                $map['type'] = 2;
                break;
            case 2:
                $map['type'] = 3;
                break;
            default:
                $map['type'] = 4;
        }

        $data = Db::name('Intro')->where($map)->select();

        foreach($data as $k=>$v){
            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $data[$k]['caozuo'] = "<a title='编辑' onclick='edit(".$v['id'].")'><i class='Hui-iconfont' >&#xe6df;</i></a>  | 
 <a style='text-decoration:none' class='ml-5' onClick='del(this,".$v['id'].")' href='javascript:;' title='删除'><i class='Hui-iconfont'>&#xe6e2;</i></a>";

            $data[$k]['img'] = "<img src='".$v['img']."' style='width:50px;height:50px'>";

        }

        return json(['data'=>$data]);
    }


    /**
     * @return \think\response\View
     * author hongwenyang
     * method description : 首页申请类型
     */

    public function iedit(){
        $id = input('id');

        $data = Db::name('Intro')->where(['id'=>$id])->find();


        $j = [
            'title'=>'分类详情',
            'data'=>$data
        ];

        return view('apply/edit',$j);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 保存对首页申请类型的操作
     */

    public function save(){
        $data = input('post.');
        $map['id'] = $data['id'];

        if(!empty(session('sq_img'))){
            $data['img'] = session('sq_img');
        }
        if($map['id'] == ""){
            $data['create_time'] = time();
            $data['type']        = 2;
            $s = Db::name('intro')->insert($data);
        }else{
            unset($data['id']);
            $s = Db::name('intro')->where($map)->update($data);
        }

        if($s){
            $msg['code'] = 200;
            $msg['msg']  = '操作成功';
        }else{
            $msg['code'] = 403;
            $msg['msg']  = '数据未改动';
        }

        return json($msg);
    }



    public function del(){
        $data = input('post.');
        Db::name('Intro')->where($data)->delete();
        $code = 200;
        return json($code);
    }


    public function Mcat(){
        $j = [
            'title'=>'申请类型列表'
        ];

        return view('apply/mcat',$j);
    }


    /**
     * @return \think\response\View
     * author hongwenyang
     * method description : 免费申请列表
     */

    public function Icat_list(){
        $j = [
            'title'=>'免费申请列表'
        ];

        return view('apply/icat_list',$j);
    }

    public function sq_list_ajax(){
        $type = input('type');
        switch ($type){
            case 1:
                $data = Db::name('Shenqing')->order('created_at','asc')->select();
                break;
            default:
                $data = Db::name('Zx')->order('id','desc')->select();
        }


        foreach ($data as $k=>$v){
//            $data[$k]['create_time'] = date('Y/m/d H:i:s',$v['create_time']);
            if($type == 1){
                $data[$k]['type'] = Db::name('Intro')->where(['id'=>$v['type']])->value('title');
            }

            switch ($v['called']){
                case 0:
                    $data[$k]['called'] = "未发布";
                    break;
                default:
                    $data[$k]['called'] = "已发布";
            }
            if($type == 2){
                $data[$k]['user_name'] = Db::name('user')->where(['id'=>$v['user_id']])->value('user_name');
            }
            $data[$k]['caozuo']      = "<a href='/admin/apply/read?id=".$v['id']."'><i class='Hui-iconfont'>&#xe6df;</i></a>";
        }
//        dump($data);die;
        return json(['data'=>$data]);
    }


    public function called(){
        $data = input('post.');

        if($data['type'] == 1){
            Db::name('Shenqing')->where(['id'=>$data['id']])->update(['called'=>$data['called']]);
        }else{
            Db::name('Zx')->where(['id'=>$data['id']])->update(['called'=>$data['called']]);
        }

    }

    /**
     * @return \think\response\View
     * author hongwenyang
     * method description : 免费申请列表
     */

    public function Mcat_list(){
        $j = [
            'title'=>'我要装修申请列表'
        ];

        return view('apply/mcat_list',$j);
    }
    //免费申请次数
    public function degree()
    {

        $re=Db::name("user")->find();
        $this->assign('re',$re);
        return $this->fetch();
    }
    public function update()
    {
        $id=input('param.id');
        $this->assign('re',$id);
        return $this->fetch();
    }
    public function updatedoit()
    {
        $degree=request()->post();
//        dump($degree);
//        die;
        $re=Db::name('user')->where("1=1")->update(['degree'=>"$degree[ci]"]);
        if ($re) {

            $this->success('修改成功',url('apply/degree'));
        }else{
            $this->error('修改失败',url('apply/degree'));
        }
    }


    public function read(){
        $id = input();

        $data = Demand::findOrFail($id);

        if(!empty($data)){
            if(!empty($data->imgs)){
                $data->imgs = json_decode($data->imgs,true);
            }

            $data->userName = \app\model\User::where('id',$data->user_id)->value('user_name');
        }
        $title = "详情";
        return view('apply/read',compact('data','title'));
    }
}
