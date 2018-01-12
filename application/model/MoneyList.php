<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\model;
use think\Db;
use think\Model;

class MoneyList extends Model{
    protected $table = "zjx_money_list";

    public function order(){
        return $this->hasOne(UserRw::class,'orderId','order_id');
    }
    public static function add($data){
        // 先查询是否金额大于余额
        $hasMoney = User::where('id',$data['user_id'])->value('user_money');
        if($hasMoney >= $data['money']){
            $s = MoneyList::insert([
                'user_id'=>$data['user_id'],
                'money'=>$data['money'],
                'type'=>3,
                'status'=>1,
                'create_time'=>time()
            ]);

            if($s){
                // 将数据加入提现申请数据表中
                Db::name('tixian')->insert([
                    'user_id'=>$data['user_id'],
                    'money'=>$data['money']
                ]);
                $code = 200;
            }
        }else{
            $code = 0;
        }


        return $code;
    }


    /**
     * @param $data
     * @return array
     * author hongwenyang
     * method description : 处理数据
     */

    public static function getReturnData($data){
        $returnData = array();
        if(!empty($data)){
            foreach ($data as $k=>$v) {
                $returnData[$k]['rw_title']         = 1;
                $returnData[$k]['money']            = $v['money'];
                $returnData[$k]['create_time']      = $v['create_time'];
                $returnData[$k]['status']           = $v['status'];
                $returnData[$k]['type']             = $v['type'];
                $returnData[$k]['rw_title']         = Renwu::where('id',$v['order']['rw_id'])->value('rw_title');
            }
        }

        return $returnData;
    }

    /**
     * @param $attch
     * @param $notify
     * author hongwenyang
     * method description : 微信支付成功回调后增加数据
     */
    public static function WxPayAddMoneyList($attch,$notify){
        MoneyList::insert([
            'user_id'=>$attch[2],
            'order_id'=>UserRw::where('out_trade_no',$attch[0])->value('order_id'),
            'money'=>$notify / 100,
            'create_time'=>time(),
            'type'=>2
        ]);
    }
}