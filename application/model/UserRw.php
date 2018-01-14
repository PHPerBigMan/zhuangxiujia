<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\model;
use app\admin\model\Percentage;
use think\Db;
use think\Model;

class UserRw extends Model{
    protected $table = "zjx_user_rw";

    public function orderbid(){
        return $this->hasOne(OrderBid::class,'orderId','orderId');
    }


    public function task(){
        return $this->hasOne(Renwu::class,'id','rw_id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function userName(){
        return $this->hasOne(User::class,'id','user_id')->field('id,user_name');
    }
    /**
     * @param $data
     * @return \think\response\Json
     * author hongwenyang
     * method description : 任务投标
     */
    public static  function add($data){
        // 判断用户是否已经投标
        $isBid = UserRw::where(['user_id'=>$data['user_id'],'rw_id'=>$data['id']])->find();

        if(empty($isBid)){
            // 确认竞标
            // 查询该任务已被投标的次数
            $bidCount = UserRw::where('rw_id',$data['id'])->count();

            // 查询任务是否已经结束
            $type = Renwu::where('id',$data['id'])->value('type');

            if($type == 1){
                // 装修任务
                $status =Renwu::where('id',$data['id'])->value('rw_status');
            }else{
                // 设计任务
                $status = Renwu::where('id',$data['id'])->value('status');

            }
            if($bidCount >= 3){
                // 该任务投标次数已满
                return json(['code'=>404,'msg'=>"该任务投标次数已达上限"]);
            }else{
                Db::name('log')->insert([
                    'msg'=>'创建新订单',
                    'data'=> json_encode($data)
                ]);
                if($type == 1){
                    if($status == 1){
                        // 已经完成
                        return json(['code'=>404,'msg'=>"该任务已完成"]);
                    }else{
                        $s = UserRw::insert([
                            'user_id'=>$data['user_id'],
                            'rw_id'=>$data['id'],
                            'orderId'=>date('Ymd').time(),
                            'order_type'=>2,
                            'order_status'=>7,
                            'create_time'=>time()
                        ]);
                        return returnStatus($s);
                    }
                }else{
                    if($status == 2){
                        // 已经完成
                        return json(['code'=>404,'msg'=>"该任务已完成"]);
                    }else{
                        $s = UserRw::insert([
                            'user_id'=>$data['user_id'],
                            'rw_id'=>$data['id'],
                            'orderId'=>date('Ymd').time(),
                            'order_type'=>2,
                            'order_status'=>7,
                            'create_time'=>time(),
                            'out_trade_no'=>$data['out_trade_no']
                        ]);
                        return returnStatus($s);
                    }
                }

            }

        }else{
            // 已经竞标
            return json(['code'=>404,'msg'=>"该任务已投标"]);
        }
    }

    /**
     * 处理确认方案数据
     * @param $data 数据
     * @return mixed
     */

    public static function ConfirmBid($data){
        $returnData['rw_title'] = !isset($data['task']['rw_title']) ? "任务不存在" : $data['task']['rw_title'];
        $returnData['designer'] = !isset($data['user_name']['user_name']) ? "设计师" :$data['user_name']['user_name'];
        $returnData['bid']      = !isset($data['orderbid']['programme'][0]) ? "" :$data['orderbid']['programme'][0];
        $returnData['rw_yj']    = !isset($data['task']['rw_yj']) ? 0 :$data['task']['rw_yj'];
        $returnData['orderId']    = !isset($data['orderId']) ? 0 :$data['orderId'];
        return $returnData;
    }

    /**
     * @param $option
     * @return mixed
     * 我的任务数据处理 备用
     */

    public static  function MyTask($option){
        if($option['taskType'] == 1){
            // 投标中
            $type = [7,8,9];
        }else if($option['taskType'] == 2){
            // 进行中
            $type = [0,1,2,3,5,6];
        }else{
            // 已完成
            $type = [4];
        }
        $Getdata = UserRw::where(['user_id'=>$option['user_id']])->whereIn('order_status',$type)->select();

        foreach ($Getdata as $k => $v) {
            $data[$k]['create_time'] = date('Y/m/d', strtotime($v['create_time']));
            $rewu = Renwu::where(['id' => $v['rw_id']])->find();
//            if (($type == 0) || ($type == 5 && $v['order_status'] == 0)) {
            if ($v['order_status'] == 0) {
                if ((time() - $v['create_time']) > $rewu['pay_limit_time']) {
                    //如果时间超时更高状态为超时
                    UserRw::where(['orderId' => $v['orderId']])->update(['order_status' => 5]);
                    Renwu::where(['id' => $v['id']])->update(['rw_status' => 2]);
                } else {
//                    $data[$k]['pay_limit_time']     = ($rewu['pay_limit_time']-(time()-$rewu['pay_limit_time'])) / 60;

                    $time = ($rewu['pay_limit_time'] - (time() - $v['create_time']));

                    $data[$k]['pay_limit_time'] = (int)substr($time, 0, 4);
                }
            }

            $data[$k]['img']                = $rewu['rw_cover'];
            $data[$k]['rw_title']           = $rewu['rw_title'];
            $data[$k]['rw_yj']              = $rewu['rw_yj'];
            $data[$k]['rw_ding']            = $rewu['rw_ding'];
            $data[$k]['order_status']       = UserRw::where(['orderId'=>$v['orderId']])->value('order_status');


            switch ($data[$k]['order_status']){
                case 0:
                    $msg = "待付款";
                    break;
                case 1:
                    $msg = "进行中";
                    break;
                case 2:
                    $msg = "进行中";
                    break;
                case 3:
                    $msg = "进行中";
                    break;
                case 4:
                    $msg = "审核通过任务完成";
                    break;
                case 5:
                    $msg = "已取消";
                    break;
                case 6:
                    $msg ="已取消" ;
                    break;
                case 7:
                    $msg = "投标中";
                    break;
                case 8:
                    $msg = "已中标";
                    break;
                case 9:
                    $msg = "未中标";
                    break;

                default :
                    $msg = "已完成";

            }
            $data[$k]['status_msg'] = $msg;
        }
        return $data;
    }


    /**
     * @param $Getdata
     * @return mixed
     * author hongwenyang
     * method description :返回处理后的数据列表
     */
    public static  function ReturnOrderList($Getdata){
        $data = array();

        foreach($Getdata as $k=>$v){
            // 接单日期
            $data[$k]['create_time'] = $v['create_time'];
            // 订单状态
            $data[$k]['order_status'] = $v['order_status'];
            // 订单编号
            $data[$k]['orderId'] = $v['orderId'];
            // 任务标题
            $data[$k]['rw_title'] = $v['rw_title'];
            // 任务定金
            $data[$k]['rw_ding'] = $v['rw_ding'];
            // 任务封面
            $data[$k]['rw_cover'] = $v['rw_cover'];
            // 任务状态
            if(in_array($v['order_status'],[0,7])){
                $data[$k]['status_msg'] = "投标中";
            }else if(in_array($v['order_status'],[8])){
                $data[$k]['status_msg'] = "已中标";
            }else if(in_array($v['order_status'],[9])){
                $data[$k]['status_msg'] = "未中标";
            }else if(in_array($v['order_status'],[4])){
                $data[$k]['status_msg'] = "已完成";
            }else if(in_array($v['order_status'],[5])){
                $data[$k]['status_msg'] = "确认中";
            }
        }
//        dump($data);die;
        return $data;
    }


    /**
     * @param $Getdata
     * @return array
     * author hongwenyang
     * method description : 我的任务 业主端 处理数据
     */
    public static function OwnerTask($Getdata){
        $returnData  = array();
        switch ($Getdata['type']){
            // 投标中
            case 0:
                $status = [0];
                break;
            // 进行中
            case 1:
                $status = [1];
                break;
            // 已完成
            case 2:
                $status = [2];
                break;

        }
        // 任务数据
        $data = Renwu::whereIn('status',$status)->where('user_id',$Getdata['user_id'])->select();

        if(!empty($data)){
           foreach ($data as $k=>$v){
               $returnData[$k]['rw_title'] = $v['rw_title'];
               $returnData[$k]['task']     = UserRw::where('rw_id',$v['id'])->count();
               $returnData[$k]['id']       = $v['id'];
               $returnData[$k]['rw_cover'] = $v['rw_cover'];
           }
        }

        return $returnData;
    }

    /**
     * @param $post
     * @return \think\response\Json
     * author hongwenyang
     * method description : 确认设计稿
     */
    public static function Confirmation($post){
        // 修改订单的状态
        $s = UserRw::where('orderId',$post)->update(['order_status'=>4]);
        // 退换用户的定金和佣金到个人账户
        $TaskInfo = Renwu::where('id',UserRw::where('orderId',$post)->value('rw_id'))->find();

        //开始退换账户
        User::where('id',UserRw::where('orderId',$post)->value('user_id'))->setInc('user_money',$TaskInfo['rw_ding']);

        // 重新计算佣金  乘以抽成部分
        $percent = Percentage::where('id',1)->value('percentage');

        $TaskUserGetMoney = round($TaskInfo['rw_yj'] * (1-$percent),2);

        User::where('id',UserRw::where('orderId',$post)->value('user_id'))->setInc('user_money',$TaskUserGetMoney);

        // 退还定金
        User::where('id',UserRw::where('orderId',$post)->value('user_id'))->setInc('user_money',$TaskInfo['rw_ding']);

        // 将记录存储(佣金)
        $type = [
            0=>$TaskUserGetMoney,
            1=>$TaskInfo['rw_ding']
        ];
        foreach ($type as $k=>$value){
            MoneyList::insert([
                'user_id'=>UserRw::where('orderId',$post)->value('user_id'),
                'money'=>$value,
                'type'=>$k,
                'status'=>2,
                'create_time'=>time()
            ]);
        }

        // 修改任务的状态为已完成
        Renwu::where('id',$TaskInfo['id'])->update(['status'=>2,'bid_time'=>0]);
        if($s){
            return returnStatus($s);
        }
    }
}