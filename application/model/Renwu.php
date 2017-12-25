<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\model;
use think\Model;

class Renwu extends Model{
    protected $table = "zjx_renwu";


    public function getStartTimeAttr($value){
        return date('m'.'月'.'d'.'日',$value);
    }

    public function task(){
        return $this->hasMany(UserRw::class,'rw_id','id');
    }


//    public function getTaskAttr($value){
//        return count($value);
//    }

    protected function scopeCount($query,$id){
        $query->where('id',$id)->count();
    }

    public function getBidTimeAttr($value){
        $time = $value - time();
        if($time <= 0){
            $time = 0;
        }
        return $time;
    }

    public function getCreateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    public function getRwMainAttr($value){
        $data = array();
        $data = explode(';',$value);
        if(!empty($data)){
            foreach ($data as $k=>$v){
                $data[$k] = ($k+1) .'.'. $v;
            }
            sort($data);
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
                $v['task'] = \app\model\UserRw::where('rw_id',$v['id'])->count();
            }
        }
        return $data;
    }

}