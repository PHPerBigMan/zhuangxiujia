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


    public function getStartTimeAttr($value){
        return date('m'.'月'.'d'.'日',$value);
    }

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
                        case 1:
                            $data[$k]['status'] = 2;
                            break;
                        default:
                            $data[$k]['status'] = 1;
                            break;
                    }
                }
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
            $img                     = $v['rw_cover'];
            $data[$k]['rw_cover']    = "<img src='$img' style='width: 100px;height: 100px;'>";
            $data[$k]['rw_cat']      = Cat::where(['id'=>$v['rw_cat']])->value('cat_name');
            if($v['type'] == 1){
                switch ($v['rw_status']){
                    case 0:
                        $status = "<span class=\"label label-defaunt radius\">未支付佣金</span>";
                        break;
                    case 1:
                        $status = "<span class=\"label label-success radius\">已支付佣金</span>";
                        break;
                    case 2:
                        $status = "<span class=\"label label-defaunt radius\">未被接单</span>";
                        break;
                }
            }else{
                switch ($v['status']){
                    case 0:
                        $status = "<span class=\"label label-defaunt radius\">投标中</span>";
                        break;
                    case 1:
                        $status = "<span class=\"label label-success radius\">进行中</span>";
                        break;
                    case 2:
                        $status = "<span class=\"label label-success radius\">已完成</span>";
                        break;
                }
            }

            $data[$k]['rw_status'] = $status;

            switch ($v['is_show']){
                case 0:
                    $is_show ="<span class=\"label label-defaunt radius\">未发布</span>" ;
                    break;
                case 1:
                    $is_show = "<span class=\"label label-success radius\">已发布</span>";
                    break;

            }
            $data[$k]['is_show'] = $is_show;
            $id = $v['id'];
            $name = $v['is_show'] == 0 ? "发布" : "取消发布";
            $data[$k]['caozuo'] = "<a onclick=\"rw_fb(this,$id)\" id='fb'>$name</a> | <a style=\"text-decoration:none\" onclick=\"rw_rm(this,$id)\">推荐热门</a>
 | <a href=\"/admin/Renwu/read?id=$id\" title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | 
 <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"rw_del(this,$id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";

            $data[$k]['rw_hot'] = $v['rw_hot'] == 0 ? "<span class=\"label label-defaunt radius\">非热门任务</span>" : "<span class=\"label label-success radius\">热门任务</span>";

            if($v['type'] == 1){
                $bidUser = UserRw::with('user')->where('rw_id',$v['id'])->whereIn('order_status',[0,1,2,3,4,6,8,7,10])->find();
                $data[$k]['bidUser'] = empty($bidUser) ? "" : $bidUser['user']['user_name'];
            }else{
                $bidUser = UserRw::where('rw_id',$v['id'])->whereIn('order_status',[0,1,2,3,4,6,8,7,10])->column('user_id');

                $data[$k]['bidUser'] = empty($bidUser) ? "" : User::whereIn('id',$bidUser)->column('user_name');

                if(!empty($data[$k]['bidUser'])){

                    $data[$k]['bidUser'] = implode("<br>",$data[$k]['bidUser']);
                }
            }

            $p_id = Cat::where('id',$v['rw_cat'])->value('p_id');

            // 一级分类名称 用于搜索
            $data[$k]['p_name'] = Cat::where('id',$p_id)->value('cat_name');
        }

//        dump($data);die;
        return $data;
    }
}