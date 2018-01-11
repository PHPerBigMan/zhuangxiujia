<?php
namespace app\index\controller;

use app\admin\model\Cat;
use think\Db;

class Anli
{
    /**
     * @return \think\response\Json
     * 推荐案例列表
     */

    public function more(){
        $data = Db::name('UserAnli')->where(['jidian'=>1])->field('id,pic,title')->select();

        $data = $this->img($data);
        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 分类下的案例列表
     */

    public function anli_list(){
        $type = input('type');
        $case_type = [input('case_type')];
        if(empty(input('case_type'))){
            $case_type = [1,2];
        }

        if($type == 1){
            $data = Db::name('UserAnli')->where(['jidian'=>1])->whereIn('case_type',$case_type)->field('id,pic,title,decorativeStyles,priceRanges,houseTypes,acreageTypes')->select();

        }else if($type == 2){
            $data = Db::name('UserAnli')->field('id,pic,title,decorativeStyles,houseTypes,acreageTypes,priceRanges')->whereIn('case_type',$case_type)->order('create_time desc')->select();
        }else if($type == 3){
            $sql = "SELECT anli_id,count(anli_id) as num FROM zjx_anli_read group by anli_id order by num DESC ";
            $anli_id = Db::query($sql);

            foreach ($anli_id as $k=>$v) {
                $data[$k] = Db::name('UserAnli')->whereIn('case_type',$case_type)->field('id,pic,title,decorativeStyles,priceRanges,houseTypes,acreageTypes')->where(['id'=>$v['anli_id']])->find();
            }

        }
        $data = $this->img($data);

        $data = $this->Type($data);
        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 最新发布
     */

    public function new_list(){
        //这里的代码是否要修改
        $data = Db::name('UserAnli')->field('id,pic,title')->order('create_time desc')->select();
        $data = $this->img($data);
        $j = $this->return_data($data);
        return json($j);
    }


    /**
     * @return \think\response\Json
     * 人气最高
     */

    public function hot(){
        $sql = "SELECT anli_id,count(anli_id) as num FROM zjx_anli_read group by anli_id order by num DESC ";
        $anli_id = Db::query($sql);

        foreach ($anli_id as $k=>$v) {
            $data[$k] = Db::name('UserAnli')->field('id,pic,title')->where(['id'=>$v['anli_id']])->find();
        }

        $data = $this->img($data);
        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 案例详情  (好像没用)
     */

    public function read(){
        $id = input('id');
        $data = Db::name('UserAnli')->where(['id'=>$id])->field('title,create_time,pass_content')->find();

        //增加一次查看详情的记录
        $insert['user_id'] = 1;
        $insert['anli_id'] = $id;
        $insert['create_time'] = time();
        Db::name('AnliRead')->insert($insert);

        $j = $this->return_data($data);

        return json($j);
    }


    /**
     * @return \think\response\Json
     * 案例详情
     */

    public function anli_read(){
        $id = input('id');
        $data = Db::name('UserAnli')->where(['id'=>$id])->field('id,title,pass_content content,create_time')->find();
        $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);

        //增加一次查看详情的记录
        $insert['user_id'] = 1;
        $insert['anli_id'] = $id;
        $insert['create_time'] = time();
        Db::name('AnliRead')->insert($insert);

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


    public function img($data){
        foreach($data as $k=>$v){
            if(!empty($v['pic'])){
                $img = json_decode($v['pic'],true);
                $data[$k]['pic'] = $img[0];
            }else{
                $data[$k]['pic'] = "";
            }
        }
        return $data;
    }

    public function Type($data){

        foreach($data as $k=>$v){

            $data[$k]['quarters']   = !isset($v['priceRanges']) ? "" : $v['priceRanges'];
            $data[$k]['style']      = !isset($v['decorativeStyles']) ? "" : $v['decorativeStyles'];
            $data[$k]['house']      = !isset($v['houseTypes']) ? "" : $v['houseTypes'];
            $data[$k]['acreage']    = !isset($v['acreageTypes']) ? "" : $v['acreageTypes'];
        }

        return $data;
    }
}
