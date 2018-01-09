<?php
namespace app\index\controller;

use think\Db;

class Faxian
{

    /**
     * @return \think\response\Json
     * 分类名称
     */

    public function index(){
        $data = Db::name('ZlCat')->field('id,cat_name')->select();
        $j = $this->return_data($data);

        return json($j);
    }

    /**
     * @return \think\response\Json
     * 列表
     * 0:左图右文
     * 1:上题下图
     */

    public function read(){
        $id     = input('id');
        $data   = Db::name('Zl')->where(['zl_cat'=>$id])->field('id zl_id,zl_title,zl_pic,zl_type,created_at,zl_content')->select();
        foreach($data as $k=>$v){
            $zl_pic = json_decode($v['zl_pic'],true);
            $data[$k]['zl_pic'] = $zl_pic[0];
            $data[$k]['content'] = mb_substr($v['zl_content'],0,20);
        }
//        echo "<pre>";
//        var_dump($data);die;
        $j = $this->return_data($data);
        return json($j);
    }


    /**
     * @return \think\response\Json
     * 查看更多
     */

    public function more(){
        $id                     = input('zl_id');
        $data                   = Db::name('Zl')->where(['id'=>$id])->field('zl_title,content zl_content,created_at')->find();
        $j = $this->return_data($data);
        return json($j);
    }



    public function return_data($data){
        if(empty($data)){
            $data = array();
            $msg = '数据为空';
        }else{
            $msg = '获取数据成功';
        }
        $j = [
            'status'=>200,
            'msg'   =>$msg,
            'data'=>$data
        ];
        return $j;
    }


}
