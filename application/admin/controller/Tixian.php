<?php
namespace app\admin\controller;

use app\admin\model\Withdrawals;
use app\common\model\BookLose;
use app\model\Bank;
use think\Db;
use think\View;

class Tixian extends Base
{
    public function index()
    {
//        echo time();die;
        $count = Db::name('Tixian')->count();
        $j = [
            'title'     =>"提现管理",
            'count'=>$count
        ];
        return view("tixian/index",$j);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取提现列表数据
     */
    public function tixian_ajax(){

        return json(['data'=>Withdrawals::getAll()]);
    }


    /**
     * @return \think\response\Json
     * 审核
     */

    public function shenghe(){
        $id = input('id');

        $shenghe  = Db::name('Tixian')->where(['id'=>$id])->value('shenghe') == 0 ? 1 : 0;

        $s = Db::name('Tixian')->where(['id'=>$id])->update(['shenghe'=>$shenghe]);
        if($s){
            $code = 200;
        }else{
            $code = 404;
        }

        return json($code);
    }

    /**
     * 打款
     */

    public function dakuan(){
        $id = input('id');
        //打款
        $money = input('money');

        $status = Db::name('Tixian')->where(['id'=>$id])->value('status');
        if($status == 1){
            //用户已提现成功
            $code = 100;
        }else{
            //打款成功后减去对应用户的账户余额
            Db::name("User")->where(['id'=>Db::name('Tixian')->where(['id'=>$id])->value('user_id')])->setDec('user_money',$money);
        }
    }



}
