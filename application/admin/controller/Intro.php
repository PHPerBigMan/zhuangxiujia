<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Intro extends Base
{
    public function index(){
        $j = [
            'title'=>'平台介绍'
        ];

        return view('intro/index',$j);
    }


    public function intro_ajax(){
        $data = Db::name('Intro')->where(['type'=>0])->select();

        foreach($data as $k=>$v){
            $data[$k]['create_time'] = date('Y-m-d H:i:s',$data[$k]['create_time']);

            $id = $v['id'];
            $data[$k]['caozuo'] = "<a href=\"/admin/Intro/read?id=$id\" title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | 
 <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"del(this,$id,0)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";

        }


        return json(['data'=>$data]);
    }


    public function read(){

        $id         = input('id');
        $data       = Db::name('Intro')->where(['id'=>$id])->find();
        $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);

        if($id == 0){
            $data['title'] = '';
            $data['content'] = '';
            $data['type'] = 0;
            $data['img'] = "";
            $data['id'] = 0;
            $data['create_time'] = date('Y-m-d H:i:s',time());
        }
        $j = [
            'title'=>'平台介绍详情',
            'data'=>$data
        ];
        return view('intro/read',$j);
    }


    /**
     * @return \think\response\Json
     * 保存平台介绍数据
     */

    public function add(){
        $data = input('post.');
        if($data['id'] == 0){
            unset($data['create_time']);
            $data['create_time'] = time();
            $data['type']           = 0;
            $s = Db::name('Intro')->insert($data);
        }else{

            $s = Db::name('Intro')->where(['id'=>$data['id']])->update([
                'title'     =>$data['title'],
                'content'   =>$data['content']
            ]);
        }

        if($s){
            $code = 200;
        }

        return json($code);
    }

    /**
     * @return \think\response\Json
     * 删除平台
     */

    public function del(){
        $id = input('id');
        $type = input('type');
        switch ($type){
            case 0:
                $table = 'Intro';
                break;
            case 1:
                $table = 'WebNotice';
                break;
            case 2:
                $table = 'Youhui';
                break;
            case 3:
                $table = 'Hot';
                break;
            default:
                $table = 'new';
        }
        $s =Db::name($table)->where(['id'=>$id])->delete();
        if($s){
            $code = 200;
        }

        return json($code);
    }

    public function banner(){
        $j = [
            'title'=>'轮播图管理'
        ];

        return view('intro/banner',$j);
    }

    public function banner_ajax(){
        $data = Db::name('Banner')->select();
        foreach($data as $k=>$v){
            $data[$k]['banner_url'] = "<img src='".$v['banner_url']."' style='width:150px;height:100px'>";

            $data[$k]['caozuo'] = "<a onclick='banner_edit(".$v['id'].")' title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | 
 <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"banner_del(".$v['id'].")\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";


            switch ($v['type']){
                case 0:
                    $data[$k]['type'] = "APP";
                    break;
                default:
                    $data[$k]['type'] = "官网";

            }
        }
        return json(['data'=>$data]);
    }

    /**
     * @return \think\response\View
     * author hongwenyang
     * method description : 查询
     */

    public function banner_edit(){
        $data = Db::name('Banner')->where(['id'=>input('id')])->find();

        $j = [
            'title'=>'轮播图编辑',
            'data'=>$data
        ];

        return view('/intro/banner_edit',$j);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 保存banner图片
     */

    public function banner_save(){
        $id = input('post.');
        if($id['id'] == 0){
            unset($id['id']);
            $id['banner_url'] = session('banner');
            $id['create_time'] = time();

            Db::name('Banner')->insert($id);
        }else{
            Db::name('Banner')->where(['id'=>$id['id']])->update([
                'banner_url'=> empty(session('banner')) ? Db::name('Banner')->where(['id'=>$id['id']])->value('banner_url'):session('banner'),
                'type'=>$id['type'],
                'herf'=>$id['href']
            ]);
        }

        return json(200);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : banner删除
     */

    public function banner_del(){
        $id = input('id');
        Db::name('Banner')->where(['id'=>$id])->delete();
        return json(200);
    }


    public function notice(){
        $j = [
            'title'=>'网站公告管理'
        ];

        return view('intro/notice',$j);
    }

    public function notice_ajax(){
        $data = Db::name('WebNotice')->select();
        foreach($data as $k=>$v){
            $data[$k]['caozuo'] = "<a title='编辑' onclick='edit(".$v['id'].",1)'><i class='Hui-iconfont' >&#xe6df;</i></a>  | 
 <a style='text-decoration:none' class='ml-5' onClick='del(".$v['id'].",1)' href='javascript:;' title='删除'><i class='Hui-iconfont'>&#xe6e2;</i></a>";

        }

        return json(['data'=>$data]);
    }


    public function yh(){
        $j = [
            'title'=>'最新优惠'
        ];
        return view('intro/yh',$j);
    }

    public function yh_ajax(){
        $data = Db::name('Youhui')->select();
        foreach($data as $k=>$v){
            $data[$k]['caozuo'] = "<a title='编辑' onclick='edit(".$v['id'].",2)'><i class='Hui-iconfont' >&#xe6df;</i></a>  | 
 <a style='text-decoration:none' class='ml-5' onClick='del(".$v['id'].",2)' href='javascript:;' title='删除'><i class='Hui-iconfont'>&#xe6e2;</i></a>";

        }

        return json(['data'=>$data]);
    }

    public function rd(){
        $j = [
            'title'=>'热点'
        ];
        return view('intro/rd',$j);
    }

    public function rd_ajax(){
        $data = Db::name('Hot')->select();
        foreach($data as $k=>$v){
            $data[$k]['caozuo'] = "<a title='编辑' onclick='edit(".$v['id'].",3)'><i class='Hui-iconfont' >&#xe6df;</i></a>  | 
 <a style='text-decoration:none' class='ml-5' onClick='del(".$v['id'].",3)' href='javascript:;' title='删除'><i class='Hui-iconfont'>&#xe6e2;</i></a>";

        }

        return json(['data'=>$data]);
    }

    public function edit(){
        $get = input('get.');
        switch ($get['type']){
            case 0:
                $table = "Intro";
                break;
            case 1:
                $table ="WebNotice";
                break;
            case 2:
                $table ="Youhui";
                break;
            case 3:
                $table = "Hot";
                break;
            case 4:
                $table = "new";
                break;
            default:
                $table = "Intro";
        }

        $data =  Db::name($table)->where(['id'=>$get['id']])->find();

        $j = [
            'title'=>'编辑',
            'data'=>$data,
            'type'=>$get['type']
        ];
        return view('intro/edit',$j);
    }



    public function save(){
        $data = input('post.');
        $data['is_show'] = 1;
        switch ($data['type']){
            case 0:
                $table = "Intro";
                break;
            case 1:
                $table = "WebNotice";
                break;
            case 2:
                $table = "Youhui";
                break;
            case 3:
                $table = "Hot";
                break;
            case 4:
                $table = "new";
                break;
            default:
                $table = "Intro";
        }
        if(empty($data['id'])){
            unset($data['id']);

            if($data['type'] == "0"){
                $data['type'] = 0;
            }else if($data['type'] == "6"){
                $data['type'] = 5;
            }else{
                unset($data['type']);
            }

            $data['create_time'] = time();
            $s = Db::name($table)->insert($data);
        }else {
            $s = Db::name($table)->where(['id' => $data['id']])->update([
                'title' => $data['title'],
                'content' => $data['content']
            ]);
        }
        if($s){
            $msg['code'] = 200;
        }

        return json($msg);
    }
        public function img()
        {
            $re=Db::name('ad')->select();
            $j = [
                're'=>$re
            ];
            return view('intro/img',$j);

        }

      public function move()
      {
          $id=input('param.id');
          //dump($id);
          $re=Db::name('ad')->where("id=$id")->delete();
          if ($re) {
              $this->success('删除成功');
          }else
          {
              $this->error('删除失败');
          }
      }
    public function insert()
    {
        return $this->fetch();
    }
    public function savainsert()
    {
        $file = request()->post("img");
        if (request()->file("img")) {
            $path=ROOT_PATH . 'public' . DS . 'uploads';
            $file['url']="/public/uploads/".$this->upload($path);
        }

        $result=Db::name('ad')->insert($file);

        if ($result) {
            $this->success('添加成功');
        }else
        {
            $this->error('添加失败');
        }
    }
     public function update()
     {
         $id=input('param.id');
         $re=Db::name('ad')->where("id=$id")->find();
         $this->assign("re",$re);
//         dump($re);
//         die;
         return $this->fetch();
     }
    public function savaupdate()
    {
        $id=input('param.id');
        $ar=request()->post();
//        dump($ar);die;
       // $href=input('param.url');
        $file = request()->post("img");
        if (request()->file("img")) {
            $path=ROOT_PATH . 'public' . DS . 'uploads';
            $file['url']="/uploads/".$this->upload($path);
     }
        $result=Db::name('ad')->where("id=$id")->update(['url'=>$file['url'],'href'=>$ar['url']]);

        if ($result) {
            $this->success('修改成功');
        }else
        {
            $this->error('修改失败');
        }

    }
    public function upload($path=null,$filename=null)
    {
        //获取表单上传文件
        $file=request()->file("img");
        //移动文件到指定路径
        if ($path==null) {
            $path=ROOT_PATH.'public'.DS.'uploads';
        }
        $info=$file->move($path);
        if ($info) {
            return  $info->getSaveName();
        }else
        {
            return $file->getError();
        }
    }
    /**
     * @return \think\response\View
     * author hongwenyang
     * method description : 新闻
     */

    public function news(){
        $j = [
            'title'=>'新闻'
        ];
        return view('intro/new',$j);
    }

    public function news_ajax(){
        $data = Db::name('New')->select();
        foreach($data as $k=>$v){
            $data[$k]['caozuo'] = "<a title='编辑' onclick='edit(".$v['id'].",4)'><i class='Hui-iconfont' >&#xe6df;</i></a>  | 
 <a style='text-decoration:none' class='ml-5' onClick='del(".$v['id'].",4)' href='javascript:;' title='删除'><i class='Hui-iconfont'>&#xe6e2;</i></a>";

        }

        return json(['data'=>$data]);
    }

    public function fuwu(){
        $j = [
            'title'=>'服务'
        ];
        return view('intro/fuwu',$j);
    }

    public function fuwu_ajax(){
        $data = Db::name('Intro')->where(['type'=>5])->select();
        foreach($data as $k=>$v){
            $data[$k]['caozuo'] = "<a title='编辑' onclick='edit(".$v['id'].",0)'><i class='Hui-iconfont' >&#xe6df;</i></a>  | 
 <a style='text-decoration:none' class='ml-5' onClick='del(".$v['id'].",0)' href='javascript:;' title='删除'><i class='Hui-iconfont'>&#xe6e2;</i></a>";

        }

        return json(['data'=>$data]);
    }

}
