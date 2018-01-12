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


        $s = Db::name('Tixian')->where(['id'=>$id])->update(['shenghe'=>1]);
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
        // 修改为提现成功
        $s = Db::name('tixian')->where('id',$id)->update(['status'=>1]);
        if($s){
            return 200;
        }
    }



}
