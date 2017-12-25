<?php
namespace app\index\controller;

use app\model\OrderBid;
use app\model\RenwuTask;
use app\model\UserRw;
use think\Db;
use app\model\Renwu;
class Index
{
    /**
     * @return \think\response\Json
     * 首页的一级分类
     */

    public function first(){
        $data = Db::name('Cat')->where(['level'=>1])->field('cat_name,level,id c_id,cat_img img')->select();
        $msg  = empty($data) ?  "数据为空": "获取数据成功";

        $j = [
            'data'  =>$data,
            'status'=>200,
            'msg'   =>$msg
        ];

        return json($j);
    }


    /**
     * @return \think\response\Json
     * 二级分类
     */

    public function sec(){

        $id     = input('c_id');

        $data = Db::name('Cat')->where(['p_id'=>$id])->field('id,level,cat_name')->select();

        if(empty($data)){
            $msg = "该分类下无二级分类";
        }else{
            $msg = "获取数据成功";
        }

        $j = [
            'data'      =>empty($data)? array() :$data,
            'status'    =>200,
            'msg'       =>$msg
        ];

        return json($j);
    }

    /**
     * @return \think\response\Json
     * 二级分类下的任务
     */
    public function sec_rw(){
        $id = input('id');
        $area = input('area');
        if(!empty($area)){
            $data   = Db::name('Renwu')->where(['rw_cat'=>$id])->where("rw_status = 2")->where("is_show = 1")->where("rw_area","like","%$area%")->field('rw_title,start_time,id,rw_yj,rw_cover img')->select();
        }else{
            $data   = Db::name('Renwu')->where(['rw_cat'=>$id])->where("rw_status = 2")->where("is_show = 1")->field('rw_title,start_time,id,rw_yj,rw_cover img')->select();
        }

        foreach($data as $k=>$v){
            $data[$k]['start_time'] = date('Y/m/d');
        }
        $j = $this->return_data($data);

        return json($j);
    }

    public function banner()
    {
        $Img    = Db::name('Banner')->field('banner_url url')->where(['type'=>0])->select();

        if(empty($Img)){
            $status = 200;
            $msg    = "数据为空";
        }else{
            $status = 200;
            $msg    = "获取数据成功";
        }
        $j = [
            'data'  =>$Img,
            'status'=>$status,
            'msg'   =>$msg
        ];
        return json($j);
    }


    /**
     * 热门任务
     */

    public function rewu(){
        $area = input('area');
        // 装修热门任务
        $renovation     = array();
        $design         = array();
        // TODO:: 这里还有问题
        if(!empty($area)){

            $renovation = Renwu::where(['is_show'=>1,'rw_status'=>2,'type'=>1])->where('rw_area','like',"%$area%")->field('id,rw_title,rw_yj,start_time,rw_cover img,type')->select();

            $design = Renwu::where(['is_show'=>1,'rw_status'=>2,'type'=>2])->where('rw_area','like',"%$area%")->field('id,rw_title,rw_yj,start_time,rw_cover img,type')->select();
        }else{

            $renovation = Renwu::where(['is_show'=>1,'rw_status'=>2,'type'=>1])->field('id,rw_title,rw_yj,start_time,rw_cover img,type')->select();

            $design = Renwu::where(['is_show'=>1,'rw_status'=>2,'type'=>2])->field('id,rw_title,rw_yj,start_time,rw_cover img,type')->select();

        }

        $design = Renwu::IndexTask($design);

        $renovation =  Renwu::IndexTask($renovation);

        $j = Task($renovation,$design);

        return json($j);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 装修任务投标
     */
    public function bid(){
        $data = input('');
        return UserRw::add($data);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 上传设计师方案
     */
    public function programme(){
        $data = input('');
        $file = request()->file('img');
        return OrderBid::addProgramme($file,$data);
    }


    /**
     * @return array
     * author hongwenyang
     * method description : 任务详情
     */
    public function TaskMore(){
        $orderId = input();

        $data = UserRw::with(['task'=>function($query){$query->field('id,rw_main');}])->where($orderId)->find()->toArray();

        if(!empty($data)){
            // 获取任务的状态
            $TaskStatus = Renwu::where('id',$data['rw_id'])->value('status');
            if($TaskStatus == 0){
                // 投标中
                $orderStatus = [7];
            }else{
                // 中标
                $orderStatus = [8];
            }
            // 查找对应任务的 投标或中标用户
            $user_id = UserRw::where('rw_id',$data['rw_id'])->whereIn('order_status',$orderStatus)->column('user_id');
            // 获取用户信息
            $userInfo = \app\model\User::whereIn('id',$user_id)->field('user_pic,id,user_name')->select();

            $data['user'] = $userInfo;

            $Task = Renwu::where('id',$data['rw_id'])->find();
            $data['rw_title'] = $Task['rw_title'];
            $data['rw_yj']    = $Task['rw_yj'];
            $data['bid_time'] = $Task['bid_time'];
            $data['create_time'] = $Task['create_time'];
            $data['rw_main'] = $Task['rw_main'];
        }
        $data['task'] = count($data['task']);

        return json($this->return_data($data));
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 业主端 任务详情
     */
    public function OwnerTaskMore(){
        $id = input();
        $data = Renwu::where('id',$id['id'])->field('id,rw_title,rw_yj,create_time,bid_time,rw_main,status as taskStatus')->find()->toArray();
        // 获取已投标用户
        $data['design'] = UserRw::where(['rw_id'=>$data['id']])->with(['user'=>function($query){$query->field('id,user_name,user_pic');}])->select();

        if(!empty($data['design'])){
            $data['task'] = count($data['design']);
            foreach ($data['design'] as $k=>$v){
                $data['user'][$k]['orderId'] = $v['orderId'];
                $data['user'][$k]['id'] = $v['user']['id'];
                $data['user'][$k]['user_name'] = $v['user']['user_name'];
                $data['user'][$k]['user_pic'] = $v['user']['user_pic'];
            }
            unset($data['design']);
        }else{
            $data['task'] = 0;
        }
        return json($this->return_data($data));
    }
    /**
     * @return \think\response\Json
     * 经典案例
     */
    public function anli(){
        $data = Db::name('UserAnli')->where(['jidian'=>1])->field('id,pic,title')->limit(4)->select();
        foreach($data as $k=>$v){
            if(!empty($v['pic'])){
                $img = json_decode($v['pic'],true);
                $data[$k]['pic'] = $img[0];
            }else{
                $data[$k]['pic'] = "";
            }
        }

        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 获取市级信息
     */

    public function province(){
        $id     = input('id');
        $provinceId = Db::name('HatProvince')->where(['id'=>$id])->value('provinceID');
        $data   = Db::name('HatCity')->where(['father'=>$provinceId])->field('city')->select();
        foreach($data as $k=>$v){
            $data[$k] = $v['city'];
        }
        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 获取市级信息
     */

    public function pro(){
        $data = Db::name('HatProvince')->field('id,province p_name')->select();
        $j = $this->return_data($data);
        return json($j);
    }


    /**
     * 平台介绍
     */

    public function intro(){
        $data = Db::name('Intro')->find();
        $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);
        $version = Db::name('Config')->field('version,download')->find();
        $version['version'] = (int)$version['version'];

        if(empty($data)){
            $data = array();
            $msg = '数据为空';
        }else{
            $msg = '获取数据成功';
        }

        $j = [
            'data'=>$data,
            'version'=>$version,
            'status'=>200,
            'msg'=>$msg
        ];
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


    public function index()
    {
        return redirect('http://zxzj.wf.sunday.so');
    }

    /**
     * @return \think\response\Json 设计方案详情
     */
    public function BidRead(){
        $orderId = input('orderId');
        $data = OrderBid::where('orderId',$orderId)->find();
        return json($this->return_data($data));
    }

    /**
     * @return \think\response\Json 方案确认
     */

    public function ConfirmBid(){
        $orderId = input('orderId');
        $data = UserRw::where('orderId',$orderId)->with('userName')->with('task')->with('orderbid')->field('user_id,rw_id,orderId')->find()->toArray();
        $data = UserRw::ConfirmBid($data);
//        dd($data);
        return json($this->return_data($data));
    }
}