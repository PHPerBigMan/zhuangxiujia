<?php
namespace app\admin\controller;

use think\Db;

class Faxian extends Base
{
    public function index(){
        $j = [
            'title'=>'分类列表'
        ];

        return view('faxian/index',$j);
    }


    /*资料分类数据*/
    public function cat_ajax()
    {
    	$data = Db::name('ZlCat')->select();
    	foreach($data  as $k=>$v){
//    		$data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
    		$id = $v['id'];
            $data[$k]['caozuo'] = "<a onclick=\"fa_edit('分类编辑','cat_edit?id=$id',500,200)\" title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"fx_del(this,$id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";

    	}
    	
    	return json(['data'=>$data]);
    }


    public function cat_edit(){
        $id = input('id');
        $data = Db::name('ZlCat')->where(['id'=>$id])->value('cat_name');
        $j = [
            'title'=>'编辑分类',
            'data'=>$data,
            'id'=>$id
        ];

        return view('faxian/edit',$j);
    }

    /**
     * @return \think\response\Json
     * 保存分类的数据
     */

    public function cat_save(){
        $id = input('id');
        $data['cat_name'] = input('cat_name');
        if($id == 0){
            $s = Db::name('ZlCat')->insert($data);
        }else{
            $s = Db::name('ZlCat')->where(['id'=>$id])->update(['cat_name'=>$data['cat_name']]);
        }

        if($s){
            $code = 200;
        }

        return json($code);
    }

    public function cat_del(){
        $id = input('id');
        $s = Db::name('ZlCat')->where(['id'=>$id])->delete();
        if($s){
            $code = 200;
        }

        return json($code);
    }


    public function Zlist(){
        $j = [
            'title'=>'资料列表'
        ];

        return view('faxian/list',$j);
    }

    /**
     * @return \think\response\Json
     * 资料列表
     */

    public function data_ajax(){
        $data = Db::name('Zl')->select();
        foreach($data as $k=>$v){
//            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);

            $id = $v['id'];

            $data[$k]['caozuo'] = "<a href=\"/Admin/Faxian/zl_edit?id=$id\" title=\"资料编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"fx_del(this,$id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";


            switch ($v['zl_type']){
                case 0:
                    $type = "图文展示";
                    break;
                default:
                    $type = "图片展示";
            }
            $data[$k]['zl_type'] = $type;

            $data[$k]['zl_cat'] = Db::name('ZlCat')->where(['id'=>$v['zl_cat']])->value('cat_name');
        }

        return json(['data'=>$data]);
    }


    /**
     * @return \think\response\View
     * 资料编辑详情
     */

    public function zl_edit(){
        $data = Db::name('Zl')->where(['id'=>input('id')])->find();
        if(empty(input('id'))){
            $data['zl_title']   = "";
            $data['zl_cat']     = 0;
            $data['zl_type']    = 0;
            $data['zl_pic']     = "";
            $data['content']    = "";
            $data['id']         = 0;
        }else{
            $data['zl_pic'] = json_decode($data['zl_pic'],true);
        }
        $cat = Db::name('ZlCat')->select();
        $j = [
            'title'=>'编辑资料',
            'data'=>$data,
            'cat'=>$cat
        ];

        return view('faxian/add',$j);
    }


    /**
     * 保存修改
     */

    public function add(){
        $data = input('post.');

        if(request()->file('zl_pic') !=NULL){
            $files = request()->file('zl_pic');
            foreach($files as $file){
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $zl_pic[] = DS.'uploads'.DS.$info->getSaveName();
                }else{
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }
        }

        if($data['id'] == 0){
            unset($data['id']);
            if(empty($zl_pic)){
                $data['zl_pic'] = "";
            }else{
                $data['zl_pic'] = json_encode($zl_pic);
            }
            $s = Db::name('Zl')->insert($data);
        }else{
            if(empty($zl_pic)){
                $s = Db::name('Zl')->where(['id'=>$data['id']])->update([
                    'zl_title'      =>$data['zl_title'],
                    'zl_cat'        =>$data['zl_cat'],
                    'zl_type'        =>$data['zl_type'],
                    'content'       =>empty($data['content']) ? "" :$data['content'],
                ]);
            }else{
                $s = Db::name('Zl')->where(['id'=>$data['id']])->update([
                    'zl_title'      =>$data['zl_title'],
                    'zl_cat'        =>$data['zl_cat'],
                    'zl_type'        =>$data['zl_type'],
                    'zl_pic'        =>json_encode($zl_pic),
                    'content'       =>empty($data['content']) ? "" :$data['content'],
                ]);
            }
        }

        if($s){
            echo "<script>alert('操作成功');history.go(-2)</script>";
        }
    }


    public function del()
    {
        $id = input('id');
        $s = Db::name('Zl')->where(['id'=>$id])->delete();
        if($s){
            $code = 200;
        }

        return json($code);
    }
}
