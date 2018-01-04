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

class OrderBid extends Model{
    protected $table = "zjx_order_bid";
    public function getProgrammeAttr($value){
        return json_decode($value,true);
    }
    /**
     * @param $files 图片文件
     * @param $data  订单号
     * @return \think\response\Json
     * author hongwenyang
     * method description : 保存设计师的方案
     */
    public static function addProgramme($files,$data){
        Db::name('log')->insert([
            'msg'=>'提交方案',
            'data'=> json_encode($data)
        ]);
        $programme = array();
        foreach($files as $k=>$file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $programme[$k] = DS.'uploads'.DS.$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        $programme = json_encode($programme);

        $isHave = OrderBid::where('orderId',$data['orderId'])->find();
        if($isHave){
            // 保存数据
            $s = OrderBid::where('orderId',$data['orderId'])->update([
                'programme'=>$programme,
                'updated_at'=>date('Y-m-d H:i:s',time())
            ]);
        }else{
            // 保存数据
            $s = OrderBid::insert([
                'orderId'=>$data['orderId'],
                'programme'=>$programme
            ]);
        }


        return returnStatus($s);
    }
}