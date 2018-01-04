<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\model;
use think\Model;

class Decorate extends Model{
    protected $table = "zjx_zx";

    public function task(){
        return $this->hasOne(Renwu::class,'apply_id','id');
    }

//    public function getImgsAttr($value){
//        if(!empty($value)){
//            $img = json_decode($value,true);
//            return $img[0];
//        }
//        return "";
//    }
    /**
     * @param $post
     * @return array
     * author hongwenyang
     * method description : 业主端申请记录
     */
    public static function getAllData($post){
        $data = Decorate::where('user_id',$post['user_id'])->select();

        $returnData = array();
        if(!empty($data)){
            foreach ($data as $k=>$v){
                $returnData[$k]['houstType']    = $v['room_type'];
                $returnData[$k]['measure']      = $v['room_mj'];
                $returnData[$k]['created_at']   = $v['created_at'];
                $returnData[$k]['id']           = $v['id'];
                $status = Renwu::where('apply_id',$v['id'])->find();
                if(!$status){
                    $returnData[$k]['status_msg'] = "审核中";
                }else{
                    $returnData[$k]['status_msg'] = "已发布";
                }
                $img = array();
                if(!empty($v['imgs'])){
                    $img = json_decode($v['imgs'],true);
                }
                $returnData[$k]['img'] = $img;

            }
        }

        return $returnData;
    }


    /**
     * @param $post
     * @return mixed
     * author hongwenyang
     * method description : 查询一条数据详情
     */

    public static function getOneData($post){
        $data = Decorate::where('id',$post['id'])->find();
        $returnData = array();
        if(!empty($data)){
            $returnData['houstType']    = $data['room_type'];
            $returnData['measure']      = $data['room_mj'];
            $returnData['created_at']   = $data['created_at'];
            $returnData['id']           = $data['id'];
            $returnData['money']           = $data['room_ys'];
            $returnData['remark']           = $data['remark'];
            $returnData['time']           = $data['room_time'];
            $status = Renwu::where('apply_id',$data['id'])->find();
            if(!$status){
                $returnData['status_msg'] = "审核中";
            }else{
                $returnData['status_msg'] = "已发布";
            }
            $returnData['img'] = array();
            if(!empty($data['imgs'])){
                $returnData['img'] = json_decode($data['imgs'],true);
            }
        }
        return $returnData;
    }
}