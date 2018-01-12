<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\admin\model;
use app\model\Bank;
use app\model\User;
use think\Model;

class Withdrawals extends Model{
    protected $table = "zjx_Tixian";

    public static function getAll(){
        $data = Withdrawals::select();

        foreach($data as $k=>$v){
            $font = "style=\"font-size:14px\"";
            $user = User::where(['id'=>$v['user_id']])->find();
            $data[$k]['user_name']      =  $user['user_name'];
            if(empty($data[$k]['user_name'])){
                $data[$k]['user_name']      =  $user['user_phone'];
            }
//            $data[$k]['create_time']    = date('Y-m-d H:i:s',$v['create_time']);
            if(!empty($v['end_time'])){
                $data[$k]['end_time']       = date('Y-m-d H:i:s',$v['end_time']);
            }
//

            switch($v['status']){
                case 0:
                    $msg = "<span class=\"label label-defaunt radius\" $font>提现中</span>";
                    break;
                case 1:
                    $msg = "<span class=\"label label-success radius\" $font>提现成功</span>";
                    break;
                default:
                    $msg = "<span class=\"label label-defaunt radius\" $font>提现失败</span>";
            }

            $data[$k]['status'] = $msg;

            switch($v['shenghe']){
                case 0:
                    $sh = "<span class=\"label label-defaunt radius\" $font>不通过</span>";
                    break;
                case 1:
                    $sh = "<span class=\"label label-success radius\" $font>通过</span>";
                    break;
                case 2:
                    $sh = "<span class=\"label label-success radius\" $font>审核中</span>";
                    break;
            }

            $data[$k]['shenghe'] = $sh;

            $id = $v['id'];

            $money = $v['money'];
            $data[$k]['caozuo'] = "<a style=\"text-decoration:none\" onclick=\"shenghe(this,$id)\">审核</a> | <a onclick='dakuan(this,$id,$money)'>打款</a>";

            $bank = Bank::where('user_id',$v['user_id'])->find();

            if(!empty($bank)){
                $data[$k]['bankNo']         = $bank['bankNo'];
                $data[$k]['bankCustomer']   = $bank['bankCustomer'];
                $data[$k]['bankName']       = $bank['bankName'];
            }else{
                $data[$k]['bankNo']         = "未填写";
                $data[$k]['bankCustomer']   = "未填写";
                $data[$k]['bankName']       = "未填写";
            }
        }

        return $data;
    }
}