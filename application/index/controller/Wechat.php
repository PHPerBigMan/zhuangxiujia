<?php
namespace app\index\controller;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use think\Db;

class Wechat
{
    public function set_order(){
        $options = [
            // 前面的appid什么的也得保留哦
            'app_id' => 'wx2932f269a786744a',
            // ...
            // payment
            'payment' => [
                'merchant_id'        => '1445154202',
                'key'                => '37585bfed1f578089c96d2a7a8552ccb',
                'notify_url'         => '/index/wechat/notify_url',       // 你也可以在下单时单独设置来想覆盖它
            ],
        ];

        $app = new Application($options);
        $payment = $app->payment;


        $attributes = [
            'trade_type'       => 'APP', // JSAPI，NATIVE，APP...
            'body'             => 'iPad mini 16G 白色',
            'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => '1217752501201407033233368018',
            'total_fee'        => 5388, // 单位：分
            // ...
        ];
        $order = new Order($attributes);

        $result = $payment->prepare($order);


        dump($result);die;
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
        }
    }

}
