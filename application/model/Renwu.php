<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\model;
use app\admin\model\Cat;
use think\Model;

class Renwu extends Model{
    protected $table = "zjx_renwu";


//    public function getStartTimeAttr($value){
//        return date('m'.'月'.'d'.'日',$value);
//    }

    public function task(){
        return $this->hasMany(UserRw::class,'rw_id','id');
    }


    public function user(){
        return $this->hasMany(User::class,'user_id','id');
    }


//    public function getTaskAttr($value){
//        return count($value);
//    }
    protected function scopeCount($query,$id){
        $query->where('id',$id)->count();
    }
//    public function getBidTimeAttr($value){
//        return date("s",($value - time()));
//    }

    public function getCreateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    public function getRwMainAttr($value){
        $data = explode(';',$value);
        foreach ($data as $k=>$v){
            $data[$k] = ($k+1) .'.'. $v;
        }
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * 处理首页任务数据
     */

    public  static function IndexTask($data){

        if(!empty($data)){
            foreach ($data as $k=>$v){
                $data[$k]['task'] = \app\model\UserRw::where('rw_id',$v['id'])->count();

                if($v['type'] == 1){
                    // 装修任务
//                    $data[$k]['status'] =  $v['rw_status'] == 1 ? 2 : $v['rw_status'] == 2 ? 1 : 1;

                    switch ($v['rw_status']){
                        // 进行中
                        case 0:
//                            $data[$k]['status'] = 0;
                            $data[$k]['status'] = 1;
                            break;
                            // 已结束
                        case 1:
//                            $data[$k]['status'] = 1;
                            $data[$k]['status'] = 2;
                            break;
                            // 可接单（未接单）
                        default:
                            $data[$k]['status'] = 3;
                            break;
                    }
                }

                $data[$k]['start_time'] = date("m-d",$v['start_time']);
            }
        }
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * author hongwenyang
     * method description : 获取后台数据列表
     */
    public static function getBackAll($data){
        foreach($data as $k=>$v){
            $data[$k]['bidUser'] = "";
            if($v['type'] == 1){
                $bidUser = UserRw::with('user')->where('rw_id',$v['id'])->whereIn('order_status',[0,1,2,3,4,6,8,7,10])->find();
                $data[$k]['bidUser'] = empty($bidUser) ? "" : $bidUser['user']['user_name'];
            }else{
                $bidUser = UserRw::where('rw_id',$v['id'])->whereIn('order_status',[0,1,2,3,4,6,8,7,10])->column('user_id');

                $data[$k]['bidUser'] = empty($bidUser) ? "" : User::whereIn('id',$bidUser)->column('user_name');
            }
        }
        return $data;
    }
}