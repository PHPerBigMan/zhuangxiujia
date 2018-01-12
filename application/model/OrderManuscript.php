<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\model;
use think\Model;

class OrderManuscript extends Model{
    protected $table = "zjx_order_manuscript";
    public function getProgrammeAttr($value){
        return json_decode($value,true);
    }
    /**
     * @param $files 图片文件
     * @param $data  订单号
     * @return \think\response\Json
     * author hongwenyang
     * method description : 保存设计师的设计稿
     */
    public static function addManuscript($files,$data){
        $manuscript = array();
        foreach($files as $k=>$file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $manuscript[$k] = DS.'uploads'.DS.$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }

        $manuscript = json_encode($manuscript);
        // 保存数据

        // 查看是已经上传过设计稿
        $isSend = OrderManuscript::where('orderId',$data['orderId'])->select();
        if($isSend){
            // 更新
            $s = OrderManuscript::where('orderId',$data['orderId'])->update([
                'manuscript'=>$manuscript
            ]);
        }else{
            $s = OrderManuscript::insert([
                'orderId'=>$data['orderId'],
                'manuscript'=>$manuscript
            ]);
        }
        // 修改订单状态 待确认
        UserRw::where('orderId',$data['orderId'])->update([
            'order_status'=>10
        ]);
        return returnStatus($s);
    }

    public function getManuscriptAttr($value){
        return json_decode($value,true);
    }

    public static function confirm($post){
        // 修改订单状态 改为已中标
        UserRw::where($post)->update([
            'order_status'=>8
        ]);

        // 修改任务的状态
        $s = Renwu::where([
            'id'=>UserRw::where($post)->value('rw_id')
        ])->update([
            'status'=>1
        ]);

        $code = 200;
        $msg = "方案确认";

        return ['code'=>$code,'msg'=>$msg];
    }
}