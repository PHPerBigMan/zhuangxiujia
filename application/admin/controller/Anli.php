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
            // 先随机撤下一个经典的案例
            Db::name('UserAnli')->where('jidian',1)->limit(1)->update(['jidian'=>0]);
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
        $id                 = input('id');
        if($id == 0){
            $data['pic']            = array();
            $data['user_name']      = "";
            $data['phone']          = "";
            $data['houseTypes']     = "";
            $data['acreageTypes']   = "";
            $data['decorativeStyles']   = "";
            $data['priceRanges']        = "";
            $data['title']          = "";
            $data['is_pass']        = 0;
            $data['pass_content']   = "";
            $data['content']        = "";
            $data['id']             = $id;
            $data['case_type']      = 1;
            $data['cover']      = "";
        }else{
            $data               = Db::name('UserAnli')->where(['id'=>$id])->find();
            $data['pic']        = json_decode($data['pic'],true);
            $data['user_name']  = \app\model\User::where(['id'=>$data['user_id']])->value('user_name');
            $data['phone']      = \app\model\User::where('id',$data['user_id'])->value('user_phone');

            if(!empty($data['cover'])){
                $cover = json_decode($data['cover'],true);
                $data['cover'] = $cover[0];
            }
        }
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

//        dump($data);die;
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
        $data = input();

        $data['create_time']        = time();
        if(!empty($data['phone'])){
            // 查找用户
            $data['user_id'] = \app\model\User::where('user_phone',$data['phone'])->value('id');
        }
        unset($data['user_name']);
        unset($data['phone']);


        $cover= array();
        $cover[0] = $data['cover'];
        $data['cover'] = json_encode($cover);

        if(!$id){
            $s = Db::name('UserAnli')->insert($data);
        }else{
            $s = Db::name('UserAnli')->where(['id'=>$id])->update($data);
        }

        if($s){
            $msg['code'] = 200;
        }else{
            $msg['code'] = 404;
        }

        return json($msg);
    }
}
