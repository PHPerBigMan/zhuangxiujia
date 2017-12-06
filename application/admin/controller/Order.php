<?php
namespace app\admin\controller;

use app\common\model\BookLose;
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
        $data = Db::name('UserRw')->select();
        foreach($data as $k=>$v){
            $data[$k]['user_name'] =Db::name('User')->where(['id'=>$v['user_id']])->value('user_name') ;
            $data[$k]['rw_title'] =Db::name('Renwu')->where(['id'=>$v['rw_id']])->value('rw_title') ;
            $data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $msg = $this->msg($v['order_status']);
            $data[$k]['order_status'] = $msg;

            $id                 = $v['id'];
            $data[$k]['caozuo'] = "<a href=\"/admin/Order/read?id=$id\"><i class=\"Hui-iconfont\">&#xe6df;</i></a>";
        }

        return json(['data'=>$data]);
    }


    /**
     *
     * @return \think\response\View
     * 订单详情
     */

    public function read(){
        $id         = input('id');
        $data       = Db::name('UserRw')->where(['id'=>$id])->find();
        $msg        = $this->msg($data['order_status']);
        $money      = Db::name('Renwu')->where(['id'=>$data['rw_id']])->field('rw_ding,rw_yj')->find();
        $data['status']         = $msg;
        $data['create_time']    = date('Y-m-d H:i:s',$data['create_time']);
        $data['rw_title']       = Db::name('Renwu')->where(['id'=>$data['rw_id']])->value('rw_title');
        $data['rw_ding']        = $money['rw_ding'];
        $data['rw_yj']          = $money['rw_yj'];
        $data['user_name']      = Db::name('User')->where(['id'=>$data['user_id']])->value('user_name');
        $j = [
            'title'=>'订单详情',
            'data'=>$data
        ];
        return view('order/read',$j);
    }



    /**
     * @return \think\response\Json
     * 修改订单状态
     */

    public function order_edit(){
        $id = input('id');
        $order_remark = input('order_remark');
        $order_status = input('order_status_pass');

        if($order_status == 0){
            $s = Db::name('UserRw')->where(['id'=>$id])->update([
                'order_remark'=>$order_remark,
                'order_status'=>$order_status
            ]);
        }else if($order_status == 4){
            $s = Db::name('UserRw')->where(['id'=>$id])->update([
                'order_status'=>$order_status
            ]);

            //退换定金的操作  微信退款+记录于数据库中

            //将佣金充值进用户账户
            Db::name("User")->where(['id'=>Db::name('UserRw')->where(['id'=>$id])->value('user_id')])->setInc('user_money',input('rw_yj'));
        }else{
            $s = Db::name('UserRw')->where(['id'=>$id])->update([
                'order_status'=>$order_status
            ]);
        }

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
                $msg = "<span class=\"label label-success radius\" $font>进行中</span>";
                break;
            case 3:
                $msg = "<span class=\"label label-success radius\" $font>进行中</span>";
                break;
            case 4:
                $msg = "<span class=\"label label-success radius\" $font>已完成</span>";
                break;
            case 5:
                $msg = "<span class=\"label label-defaunt radius\" $font>已取消</span>";
                break;
            default :
                $msg = "<span class=\"label label-defaunt radius\" $font>已取消</span>";
        }

        return $msg;
    }
}
