<?php
namespace app\admin\controller;

use app\admin\model\Product;
use app\common\model\BookLose;
use think\Db;

class Shop extends Base
{
    public function index()
    {
        $count = Db::name('Product')->count();
        $j = [
            'title'     =>"商品管理",
            'count'=>$count
        ];
        return view("shop/index",$j);
    }



    /**
     * @return \think\response\Json
     * 商品列表
     */

    public function pro_ajax(){
        $data = Db::name('Product')->select();
        foreach($data as $k=>$v){

            $font = "style=\"font-size:14px\"";


            $id = $v['id'];
//            $data[$k]['create_time']    = date('Y-m-d H:i:s',$v['create_time']);

            $name                       = $v['is_show'] == 0 ? "上架" : "下架";
            $data[$k]['caozuo']         = "<a onclick='is_show(this,$id)'>$name</a> | <a onclick=\"pro_edit(this,$id)\" title=\"编辑\"><i class=\"Hui-iconfont\">&#xe6df;</i></a> | 
 <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"pro_del(this,$id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";

            $pro_img                    = $v['pro_img'];
            $data[$k]['pro_img']        = "<img src='$pro_img' style='width: 50px;height: 50px'>";
            $data[$k]['pro_cat']        = Db::name('MallCat')->where(['id'=>$v['pro_cat']])->value('cat_name');
            $data[$k]['pro_url']        = substr($data[$k]['pro_url'],0,80);

            $data[$k]['is_show']        = $v['is_show'] == 0 ? "<span class=\"label label-defaunt radius\" $font>已下架</span>" : "<span class=\"label label-success radius\" $font>已上架</span>";
        }


        return json(['data'=>$data]);
    }


    public function edit(){
        $data   = Db::name('MallCat')->field('id,cat_name')->where(['level'=>2])->select();
        $pro    = Db::name('Product')->where(['id'=>input('id')])->find();
        $j = [
            'title'=>'商品修改',
            'data'=>$data,
            'pro'=>$pro,
            'id'=>input('id')
        ];

        return view('shop/edit',$j);
    }

    /**
     * @return \think\response\Json
     * 保存商品数据
     */

    public function save(){
        $data                 = input('post.');
//        $data['pro_name']   = input('pro_name');
//        $data['pro_price']  = input('pro_price');
//        $data['pro_cat']    = input('pro_cat');
//        $data['pro_url']    = input('pro_url');
//        $pro_img = "";
//        $suolve = "";
//        $longimg = "";
//        $img_get    = input('product_save_img');
        //处理前端获取的图片
//        if(!empty($img_get)){
//            $img = explode(',',$img_get);
//            $pro_img    = $img[0];
//            $suolve     = $img[1];
//            $longimg   = $img[2];
//        }
        return Product::saveProduct($data);
    }

    /**
     * @return \think\response\Json
     * layer图片预处理
     */

    public function img()
    {

        if ((request()->file('file')) != NULL) {
            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            $img = '/uploads/' . $info->getSaveName();

        }

        if(input('type')==1){
            session('pro_img',$img);
        }else if(input('type') == 2){
            session('cat_img',$img);
        }else if(input('type') == 3){
            session('sq_img',$img);
        }else if(input('type') == 4){
            session('banner',$img);
        }else if(input('type') == 5){
            session('cat_img',$img);
        }


        return json($img);
    }


    /**
     * @return \think\response\Json
     * 删除商品
     */

    public function del(){
        $id = input('id');
        $s = Db::name('Product')->where(['id'=>$id])->delete();
        if($s){
            $code = 200;
        }else{
            $code = 404;
        }

        return json($code);
    }

    /**
     * @return \think\response\Json
     * 商品上下架
     */

    public function is_show(){
        $id = input('id');
        $is_show = Db::name('Product')->where(['id'=>$id])->value('is_show') == 0 ? 1 : 0;

        $s = Db::name('Product')->where(['id'=>$id])->update(['is_show'=>$is_show]);
        if($s){
            $code = 200;
        }else{
            $code = 404;
        }

        return json($code);
    }

    public function cat(){
        $j = [
            'title'=>'产品分类'
        ];

        return view('shop/cat',$j);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 分类数据
     */

    public function cat_ajax(){
        $data = Db::name('mall_cat')->order('level','asc')->select();
        foreach($data as $k=>$v){
            $id = $v['id'];
            $data[$k]['caozuo'] = "<a onclick=\"edit('分类编辑','cat_edit?id=$id',480,480)\" title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"del(this,$id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";

            $data[$k]['level']  = $v['level'] == 1 ? "一级分类" : "二级分类";
        }

        return json(['data'=>$data]);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 删除分类
     */

    public function cat_del(){
        $id = input('id');
        $is_first = Db::name('mall_cat')->where(['p_id'=>$id])->find();
        if($is_first){
            $retJson['code'] = 400;
            $retJson['msg']  = "该分类下有二级分类,删除失败";
        }else{
            Db::name('mall_cat')->where(['id'=>$id])->delete();
            $retJson['code'] = 200;
            $retJson['msg']  = "删除失败";
        }

        return json($retJson);
    }

    /**
     * @param $id
     * @return \think\response\View
     * author hongwenyang
     * method description : 分类编辑
     */

    public function cat_edit($id){
        $data = Db::name('mall_cat')->where(['id'=>$id])->find();
        $first = Db::name('mall_cat')->where(['level'=>1])->select();
        $j = [
            'title'=>'分类详情',
            'data'=>$data,
            'first'=>$first
        ];

        return view('shop/cat_edit',$j);
    }

    public function cat_save(){
        $data = input('post.');
        if(session('cat_img') != ""){
            $data['img'] = session('cat_img');
        }
        if($data['id'] == ""){
            unset($data['id']);
            //新增
            if($data['level'] == 1){
                //一级分类
                $data['p_id'] = 0;
            }
//            $data['create_time'] = time();
            $s = Db::name('mall_cat')->insert($data);
        }else{
            //编辑
            if($data['level'] == 1){
                $data['p_id'] = 0;
            }
            $map['id'] = $data['id'];
            unset($data['id']);
            $s = Db::name('mall_cat')->where($map)->update($data);
        }
        if($s){
            $retJson['code'] = 200;
            $retJson['msg']  = "操作成功";
        }else{
            $retJson['code'] = 403;
            $retJson['msg']  = "操作失败";
        }

        return json($retJson);
    }
    /**
     * author hongwenyang
     * method description : 删除 session中存储的图片数据
     */

    public function session_del(){

        if(!empty(session('pro_img'))){
            session('pro_img'," ");
        }

        if(!empty(session('cat_img'))){
            session('cat_img'," ");
        }

        if(!empty(session('user_pic'))){
            session('user_pic'," ");
        }

        if(!empty(session('banner'))){
            session('banner'," ");
        }

        if(!empty(session('cat_img'))){
            session('cat_img');
        }
    }
}
