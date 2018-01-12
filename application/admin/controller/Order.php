<?php
namespace app\admin\controller;

use app\admin\model\Percentage;
use app\common\model\BookLose;
use app\model\MoneyList;
use app\model\UserRw;
use think\Db;

class Order extends Base
{
    public function index()
    {
        $count = Db::name('UserRw')->count();
        $j = [
            'title'     =>"订单管理",
            'count'=>$count
        ];
        return view("order/index",$j);
    }

    /**
     * @return \think\response\Json
     * 订单列表
     */

    public function index_data(){
        $data = Db::name('UserRw')->order('id','desc')->select();
        foreach($data as $k=>$v){
            $data[$k]['user_name'] =Db::name('User')->where(['id'=>$v['user_id']])->value('user_name') ;
            $data[$k]['rw_title'] =Db::name('Renwu')->where(['id'=>$v['rw_id']])->value('rw_title') ;
            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $msg = $this->msg($v['order_status']);
            $data[$k]['order_status'] = $msg;

            $id                 = $v['id'];
            $data[$k]['caozuo'] = "<a href=\"/admin/Order/read?id=$id\">详情</a>";
        }

        return json(['data'=>$data]);
    }


    /**
     *
     * @return \think\response\View
     * 订单详情
     */

    public function read(){
        $userData = "";
        $GetTaskUser = "";
        $id         = input('id');
        $data       = UserRw::where(['id'=>$id])->find();
        $msg        = $this->msg($data['order_status']);
        $money      = \app\model\Renwu::where(['id'=>$data['rw_id']])->find();
        $data['status']         = $msg;
        $data['rw_title']       = $money['rw_title'];
        $data['rw_ding']        = $money['rw_ding'];
        $data['rw_yj']          = $money['rw_yj'];
        $data['user_name']      = Db::name('User')->where(['id'=>$data['user_id']])->value('user_name');


        $user_id = UserRw::where('rw_id',$data['rw_id'])->column('user_id');
        if($data['order_type'] == 2){
            // 获取所有投标的用户id

            // 查找 投标用户对应的用户信息
            if(!empty($user_id)){
                $userData = \app\model\User::whereIn('id',$user_id)->column('user_name');

                if(!empty($userData)){
                    $userData = implode(',',$userData);
                    if(empty($userData)){
                        $userData = implode(',',\app\model\User::whereIn('id',$user_id)->column('user_phone'));
                    }
                }
            }

            // 查找中标用户的id
            $GetTaskUserId = UserRw::whereIn('order_status',[1,2,3,4,5,6,8,10])->where('rw_id',$data['rw_id'])->value('user_id');
            // 查找中标用户信息
            $GetTaskUser = \app\model\User::where('id',$GetTaskUserId)->value('user_name');

            if($GetTaskUser == null){
                $GetTaskUser = \app\model\User::where('id',$GetTaskUserId)->value('user_phone');
            }

        }
        $data['phone'] = array();
        if(!empty($user_id)){
            $data['phone'] = \app\model\User::whereIn('id',$user_id)->column('user_phone');
        }

        $title= "订单详情";
        return view('order/read',compact('data','title','userData','GetTaskUser'));
    }



    /**
     * @return \think\response\Json
     * 修改订单状态
     */

    public function order_edit(){
        $id = input('id');
        $order_remark = input('order_remark');
        $order_status = input('order_status_pass');
        $TaskId= UserRw::where('id',$id)->value('rw_id');
        if($order_status == 0){
            // 任务不通过
            $s = Db::name('UserRw')->where(['id'=>$id])->update([
                'order_remark'=>$order_remark,
                'order_status'=>$order_status
            ]);
            $TaskPass = 1;
        }else if($order_status == 4){
            $s = Db::name('UserRw')->where(['id'=>$id])->update([
                'order_status'=>$order_status
            ]);

            //退换定金的操作  微信退款+记录于数据库中

            //将佣金充值进用户账户 需要重新计算佣金
            $percent = Percentage::where('id',1)->value('percentage');

            $TaskUserGetMoney = round(input('rw_yj') * (1-$percent),2);
            // 佣金
            Db::name("User")->where(['id'=>Db::name('UserRw')->where(['id'=>$id])->value('user_id')])->setInc('user_money',$TaskUserGetMoney);
            //定金
            Db::name("User")->where(['id'=>Db::name('UserRw')->where(['id'=>$id])->value('user_id')])->setInc('user_money',input('rw_ding'));

            // 将定金返还至账户
            // 将数据记录正在金额明细中
            // 将记录存储
            $type = [
                0=>$TaskUserGetMoney,
                1=>input('rw_ding')
            ];
            foreach ($type as $k=>$value){
                MoneyList::insert([
                    'user_id'=>Db::name('UserRw')->where(['id'=>$id])->value('user_id'),
                    'money'=>$value,
                    'type'=>$k,
                    'status'=>2,
                    'create_time'=>time()
                ]);
            }
            // 将任务的状态改为已完成

            \app\model\Renwu::where('id',$TaskId)->update(['status'=>2,'rw_status'=>1]);

            // 任务通过
            $TaskPass = 2;
        }else{
            $s = Db::name('UserRw')->where(['id'=>$id])->update([
                'order_status'=>$order_status
            ]);
            $TaskPass = 0;
        }

        // 修改任务的状态
        \app\model\Renwu::where('id',$TaskId)->update([
            'rw_pass'=>$TaskPass
        ]);
        if($s){
            $code = 200;
        }else{
            $code = 404;
        }

        return json($code);
    }





    protected function msg($status){
        $font = "style=\"font-size:14px\"";
        switch($status){
            case 0:
                $msg = "<span class=\"label label-defaunt radius\" $font>待付款</span>";
                break;
            case 1:
                $msg = "<span class=\"label label-success radius\" $font>进行中</span>";
                break;
            case 2:
                $msg = "<span class=\"label label-success radius\" $font>进行中(需审核)</span>";
                break;
            case 3:
                $msg = "<span class=\"label label-success radius\" $font>进行中(审核未通过)</span>";
                break;
            case 4:
                $msg = "<span class=\"label label-success radius\" $font>已完成</span>";
                break;
            case 5:
                $msg = "<span class=\"label label-defaunt radius\" $font>已取消</span>";
                break;
            case 6:
                $msg = "<span class=\"label label-defaunt radius\" $font>用户已取消</span>";
                break;
            case 7:
                $msg = "<span class=\"label label-success radius\" $font>投标中</span>";
                break;
            case 8:
                $msg = "<span class=\"label label-success radius\" $font>已中标</span>";
                break;
            case 9:
                $msg = "<span class=\"label label-defaunt radius\" $font>未中标</span>";
                break;
            default :
                $msg = "<span class=\"label label-success radius\" $font>设计稿确认中</span>";
        }

        return $msg;
    }
}
