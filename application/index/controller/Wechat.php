<?php
namespace app\index\controller;

use app\model\UserRw;
use app\model\Renwu;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use think\Db;

class Wechat
{
    // 用于业主 支付佣金
    public function OwnerPay(){
        $orderData = input();
        $options = config('options');
        $app = new Application($options);
        $payment = $app->payment;
        $out_trade_no  = 'wx'.$orderData['orderId'].rand(1000,9999);
        $taskId     = UserRw::where('orderId',$orderData['orderId'])->value('rw_id');
        $detail     = Renwu::where('id',$taskId)->value('rw_title');

        $attributes = [
            'trade_type'       => 'APP', // JSAPI，NATIVE，APP...
            'body'             => '支付押金',
            'detail'           => $detail,
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $orderData['rw_yj']*100, // 单位：分
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            UserRw::where('orderId',$orderData['orderId'])->update([
                'out_trade_no'=>$out_trade_no
            ]);
            $prepayId = $result->prepay_id;
            $config = $payment->configForAppPayment($prepayId);
        }
    }


    /**
     * 支付成功回调
     */
    public function paySuccess(){
        $options = config('options');
        $app = new Application($options);
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = UserRw::where('out_trade_no',$notify->out_trade_no)->find();
            if (count($order) == 0) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order['pay_time']) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $order['pay_time'] = date("Y-m-d H:i:s",time()); // 更新支付时间为当前时间
                $order['order_status'] = 1; //支付成功,
            } else { // 用户支付失败
                $order['order_status'] = 0; //待付款
            }
            UserRw::where('out_trade_no',$notify->out_trade_no)->update($order); // 保存订单
            return true; // 返回处理完成
        });
    }
}
