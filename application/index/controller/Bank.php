<?php
namespace app\index\controller;

use think\Db;

class Bank
{
    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 新增银行卡信息
     */
    public function saveBankInfo(){
        $data = input('post.');

        $s = \app\model\Bank::create($data);

        return returnStatus($s);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 修改银行卡信息
     */
    public function editBankInfo(){
        $data = input('post.');

        $map['id'] = $data['id'];

        unset($data['id']);

        $s = \app\model\Bank::where($map)->update($data);

        return returnStatus($s);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 删除数据
     */
    public function delBankInfo(){
        $post = input('post.');

        $s = \app\model\Bank::where($post)->delete();

        return returnStatus($s);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取数据
     */
    public function getBankInfo(){
        $post = input('post.');

        $data= \app\model\Bank::where($post)->select();

        return json(return_data_bank($data));
    }
}
