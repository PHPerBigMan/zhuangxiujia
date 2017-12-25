<?php
namespace app\index\controller;

use think\Db;

class Renwu
{
    /**
     * @return \think\response\Json
     * 任务详情
     */
    public function read(){

        $id = input('id');
        $data =\app\model\Renwu::with('task')->where(['id'=>$id])->field('id,rw_yj,rw_title,rw_img,rw_main,rw_ding,create_time,start_time,type,bid_time')->find();

//        dd($data);die;
        $data['task']           = count($data['task']);
        $data['rw_img']         = json_decode($data['rw_img'],true);
        $data['start_time']     = strtotime($data['start_time']);

        $j = $this->return_data($data);
        return json($j);
    }

    /**
     *
     * @return \think\response\Json
     * 接单
     */

    public function create(){
        $id = input('id');
        $insert['user_id']      = input('user_id');
        $insert['rw_id']        = $id;
        $insert['orderId']      = date("Ymd",time()).time();
        $insert['order_status'] = 0;
        $insert['create_time']  = time();
        $s = Db::name('UserRw')->insert($insert);

        Db::name('Renwu')->where(['id'=>$id])->update(['rw_status'=>0]);
        if($s){
            $msg = '任务接单成功';
            $status = 200;
        }else{
            $msg = '任务接单失败';
            $status = 404;
        }

        $j = [
            'msg'=>$msg,
            'status'=>$status
        ];

        return json($j);
    }

    /**
     * @return \think\response\Json
     * 任务搜索
     */

    public function search(){
        $key    = input('keyword');
        $area = input('area');
        if(empty($key)){
            $data = "";
            $j = $this->return_data($data);
        }else{
//            $sql = "SELECT id,rw_yj,rw_cover img,start_time,rw_title FROM zjx_renwu WHERE rw_status = 2 OR rw_title like '%$key%' OR rw_yj like '%$key%' OR rw_main like '%$key%'";
            if(!empty($area)){
                $data = Db::name('Renwu')->where(['rw_status'=>2,'is_show'=>1])->where('rw_title','like',"%$key%")->where('rw_area','like',"%$area%")
                    ->field('id,rw_yj,rw_cover img,start_time,rw_title')->select();
            }else{
                $data = Db::name('Renwu')->where(['rw_status'=>2,'is_show'=>1])->where('rw_title','like',"%$key%")->field('id,rw_yj,rw_cover img,start_time,rw_title')->select();

            }

            $j = $this->return_data($data);
        }

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
}
