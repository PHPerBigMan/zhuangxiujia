<?php
namespace app\admin\controller;

use think\Db;

class Anli extends Base
{
    /**
     * 调整审核信息
     */

    public function shenhe(){
        $id = input('id');
        $is_pass = input('is_pass');
        $s = Db::name('UserAnli')->where(['id'=>$id])->update([
            'is_pass'=>$is_pass
        ]);

        if($is_pass == 1){
            if($s){
                $msg['code'] = 200;
                $msg['msg']  = "审核通过";
            }
        }else{
            if($s){
                $msg['code'] = 200;
                $msg['msg']  = "审核不通过";
            }
        }

        return json($msg);
    }


    /**
     * @return \think\response\Json
     * 推荐为经典案例
     */

    public function tuijian(){
        $id         = input('id');
        $jidian     = input('jidian');
        $re=Db::name('UserAnli')->where("id=$id")->find();
//        dump($re);
//        die;
        if ($re['is_pass']==1) {
            $s = Db::name('UserAnli')->where(['id' => $id])->update([
                'jidian' => $jidian
            ]);
                if ($jidian == 1) {
                    if ($s) {
                        $msg['code'] = 200;
                        $msg['msg'] = "操作成功";
                    }
                } else {
                    if ($s) {
                        $msg['code'] = 200;
                        $msg['msg'] = "操作成功";
                    }
                }
        }else {
            $msg['code'] = 10;
            $msg['msg'] = "审核未通过不能推荐为经典案例";
        }
        return json($msg);

    }

    /**
     * @return \think\response\View
     * 案例详情
     */

    public function read(){
        $id = input('id');
        $data = Db::name('UserAnli')->where(['id'=>$id])->find();
        $data['pic']       = json_decode($data['pic'],true);
        $data['user_name'] = Db::name('User')->where(['id'=>$data['user_id']])->value('user_name');


        //分类
        $cat = Db::name('AnliCat')->where(['level'=>1])->select();
        foreach($cat as $k=>$v){
            $cat[$k]['sec'] = Db::name('AnliCat')->where(['p_id'=>$v['id']])->select();
            foreach($cat[$k]['sec'] as $k1=>$v1){
                if(($data['houseTypes'] == $v1['id']) || (($data['acreageTypes'] == $v1['id'])) || (($data['decorativeStyles'] == $v1['id'])) || (($data['priceRanges'] == $v1['id']))){
                    $cat[$k]['sec'][$k1]['select'] = 1;
                }else{
                    $cat[$k]['sec'][$k1]['select'] = 0;
                }
            }
            switch ($v['id']){
                case 1:
                    $name = 'houseTypes';
                    break;
                case 2:
                    $name = 'acreageTypes';
                    break;
                case 3:
                    $name = 'decorativeStyles';
                    break;
                default:
                    $name = 'priceRanges';
            }
            $cat[$k]['name'] = $name;
        }
        $j = [
            'title'=>"用户案例详情",
            'data'=>$data,
            'cat'=>$cat
        ];

        return view('anli/anli_read',$j);
    }

    /**
     * @return \think\response\Json
     * @throws \think\Exception
     */

    public function add(){
        $id = input('id');
        $data['title']          = input('title');
        $data['user_name']      = input('user_name');
        $data['is_pass']        = input('is_pass');
        $data['pass_content']   = input('pass_content');
        $data['houseTypes']     = input('houseTypes');
        $data['acreageTypes']   = input('acreageTypes');
        $data['decorativeStyles']   = input('decorativeStyles');
        $data['priceRanges']        = input('priceRanges');
        $data['create_time']        = time();

        if(empty($id)){
            $s = Db::name('UserAnli')->insert($data);
        }else{
            $s = Db::name('UserAnli')->where(['id'=>$id])->update([
                'title'             =>$data['title'],
                'is_pass'           =>$data['is_pass'],
                'pass_content'      =>$data['pass_content'],
                'houseTypes'        =>$data['houseTypes'],
                'acreageTypes'      =>$data['acreageTypes'],
                'decorativeStyles'  =>$data['decorativeStyles'],
                'priceRanges'       =>$data['priceRanges'],
            ]);
        }

        if($s){
            $msg['code'] = 200;
        }else{
            $msg['code'] = 404;
        }

        return json($msg);
    }
}
