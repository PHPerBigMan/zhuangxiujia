<?php
namespace app\index\controller;

use app\model\MoneyList;
use app\model\UserRw;
use app\model\Renwu;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use think\Db;
use think\Exception;

class Wechat
{
    protected function options(){ //选项设置
        return [
            // 前面的appid什么的也得保留哦
            'app_id' => 'wx5d1c29da542f85de', //你的APPID
            'secret'  => 'a83afcea6f2146990b82ce67231911e5',     // AppSecret
            'payment' => [
                'merchant_id'        => '1493980822',
                'notify_url'         => 'http://zxj.hwy.sunday.so/index/Wechat/paySuccess',
                // 微信商户号中的 key
                'key'                => 'D0A3272AA1A26C14D418859B60C80B5C'
            ],
        ];
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 投标前判断是否满足投标条件
     */
    public function checkIsBid(){
        $post = input();
        // 查看用户是否可以投标
        $isBid = UserRw::where(['user_id'=>$post['user_id'],'rw_id'=>$post['id']])->find();
        // 查看任务是否已结束
        $status = Renwu::where('id',$post['id'])->value('status');
        if($isBid){
            return json(['code'=>404,'msg'=>'已投标']);
        }else{
            if(in_array($status,[1,2])){
                return json(['code'=>404,'msg'=>'投标已结束']);
            }
            return json(['code'=>200,'msg'=>'可投标']);
        }
    }

    //
    public function OwnerPay(){
        $orderData = input();
        $options = config('options');
        $app = new Application($options);
        $payment = $app->payment;

        Db::name('log')->insert([
            'msg'=>'获取数据',
            'data'=>json_encode($orderData)
        ]);

        if($orderData['payType'] == 1){
            // 对已生成的订单进行付费
            $taskId     = UserRw::where('orderId',$orderData['orderId'])->value('rw_id');
            $user_id    = UserRw::where('orderId',$orderData['orderId'])->value('user_id');

            $out_trade_no  = 'wx'.$orderData['orderId'].'-'.$orderData['payType'].'-'.$user_id.'-'.$taskId;
        }else{
            // 投标先付费再生成订单
            $taskId = $orderData['id'];
            $user_id = $orderData['user_id'];
            $out_trade_no  = 'wx'.time().'-'.$orderData['payType'].'-'.$user_id.'-'.$taskId;


        }
        $detail     = Renwu::where('id',$taskId)->value('rw_title');

        $attributes = [
            'trade_type'       => 'APP', // JSAPI，NATIVE，APP...
            'body'             => '支付押金',
            'detail'           => $detail,
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $orderData['rw_yj']*100, // 单位：分
            'attach'           => $orderData['payType'].'-'.$user_id.'-'.$taskId  //
        ];


        $order = new Order($attributes);
        $result = $payment->prepare($order);
//        dd($result);die;
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            if($orderData['payType'] == 1){
                $out_trade_no = explode('-',$out_trade_no);
                UserRw::where('orderId',$orderData['orderId'])->update([
                    'out_trade_no'=>$out_trade_no[0]
                ]);
            }
            $prepayId = $result->prepay_id;
            $config = $payment->configForAppPayment($prepayId);

            return json($this->return_data($config));
        }
    }

    public function return_data($data){
        if(empty($data)){
            $data = array();
            $msg = '数据为空';
        }else{
            $msg = '获取数据成功';
        }
        $j = [
            'status'=>200,
            'msg'   =>$msg,
            'data'=>$data
        ];
        return $j;
    }

    /**
     * 支付成功回调
     */
    public function paySuccess(){
        $options = config('options');
        $app = new Application($options);
        $response = $app->payment->handleNotify(function($notify, $successful){
            Db::name('log')->insert([
                'msg'=>'投标接单',
                'data'=>$notify
            ]);

            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $attach = explode('-',$notify->out_trade_no);
            $order = UserRw::where('out_trade_no',$attach[0])->find();
            if($attach[1] == 1){
                if (count($order) == 0) { // 如果订单不存在  装修
                    die('Order not exist.'); // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }
            }else{
                if (count($order) != 0) { // 如果订单存在  投标
                    die('Order not exist.'); // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }
            }
//            Db::name('log')->insert([
//                'msg'=>'支付是否成功',
//                'data'=>$successful
//            ]);
            Db::name('log')->insert([
                'msg'=>'支付是否成功1',
                'data'=>json_encode($attach)
            ]);
            // 用户是否支付成功
            if ($successful) {
                try{
//                    Db::name('log')->insert([
//                        'msg'=>'支付成功',
//                        'data'=> $attach[1]
//                    ]);
                    if($attach[1] == 1){
                        // 对已有的订单进行修改
                        // 不是已经支付状态则修改为已经支付状态

                        Db::name('log')->insert([
                            'msg'=>'进来了1--任务id',
                            'data'=> $attach[3]
                        ]);
                        $update['pay_time'] = date("Y-m-d H:i:s",time());

                        // 查找订单对应的任务类型
                        $type = Renwu::where('id',$attach[3])->value('type');
                        if($type == 1){
                            // 装修任务
                            $old = UserRw::where('out_trade_no',$attach[0])->value('order_status');
                            if($old == 0){
                                $update['order_status'] = 1;
                            }
                        }else{
                            // 设计任务
                            $update['order_status'] = 8;

                            // 设计师中标后,将未中标且已支付定金 的数据返回到对应的用户账号中  并且将订单修改为未中标
//                            $TaskId = UserRw::where('out_trade_no',$attach[0])->value('rw_id');

                            $AllUserId = UserRw::where(['rw_id'=>$attach[3]])->where('out_trade_no','neq',$attach[0])->column('user_id');


                            Db::name('log')->insert([
                                'msg'=>'进来了2--任务修改',
                                'data'=> $AllUserId
                            ]);

                            if(!empty($AllUserId)){
                                // 开始退换定金
                                \app\model\User::whereIn('id',$AllUserId)->setDec('user_money',Renwu::where('id',$attach[3])->value('rw_ding'));

                                // 获取未中标的订单id
                                $NoGetOrderId = UserRw::where(['rw_id'=>$attach[3]])->where('out_trade_no','neq',$attach[0])->column('id');

                                // 将这些订单修改为未中标
                                UserRw::wehreIn('id',$NoGetOrderId)->update(['order_status'=>9]);
                            }

                        }

                        UserRw::where('out_trade_no',$attach[0])->update($update);
                    }else{

                        // payType == 2 的时候表示是投标的服务商进行付款  只有在付完款之后才能创建新的订单
                        Db::name('log')->insert([
                            'msg'=>'进来了',
                            'data'=> 12
                        ]);
                        // 创建投标新订单
                        $create['id'] = $attach[3];
                        $create['user_id']  = $attach[2];
                        $create['out_trade_no'] = $attach[0];
                        UserRw::add($create);
                    }

                    // 支付成功 增加支出列表
                    MoneyList::WxPayAddMoneyList($attach,$notify->total_fee);
                }catch (\Exception $e){
                    Db::name('log')->insert([
                        'msg'=>'回调错误3',
                        'data'=>json_encode($attach)
                    ]);
                }

                // 保存订单
            }

            return true; // 返回处理完成
        });
    }


    public function demo(){
        $AllUserId = UserRw::where(['rw_id'=>113])->where('out_trade_no','neq',"wx201801111515671912")->column('user_id');

        dump($AllUserId);die;
        Db::name('log')->insert([
            'msg'=>'进来了2--任务修改',
            'data'=> $AllUserId
        ]);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 用于支付测试
     */
    public function OwnerPayDemo(){
        $orderData = input();
        $options = config('options');
        $app = new Application($options);
        $payment = $app->payment;
        if($orderData['payType'] == 1){
            // 对已生成的订单进行付费
            $taskId     = UserRw::where('orderId',$orderData['orderId'])->value('rw_id');
            $out_trade_no  = 'wx'.$orderData['orderId'].rand(1000,9999);
            $user_id = UserRw::where('orderId',$orderData['orderId'])->value('user_id');
        }else{
            // 投标先付费再生成订单
            $taskId = $orderData['id'];
            $out_trade_no  = 'wx'.time().rand(1000,9999);
            $user_id = $orderData['user_id'];
        }
        $detail     = Renwu::where('id',$taskId)->value('rw_title');

        $attributes = [
            'trade_type'       => 'APP', // JSAPI，NATIVE，APP...
            'body'             => '支付押金',
            'detail'           => $detail,
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $orderData['rw_yj']*100, // 单位：分
            'attach'           => $orderData['payType'].'-'.$user_id.'-'.$taskId  //
        ];
//        $createString = $this->createLinkstring($attributes) . "&key=D0A3272AA1A26C14D418859B60C80B5C";
        // 生成签名
//        $sign = strtoupper(MD5($createString));

        $order = new Order($attributes);
        $result = $payment->prepare($order);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            UserRw::where('orderId',$orderData['orderId'])->update([
                'out_trade_no'=>$out_trade_no
            ]);
            $prepayId = $result->prepay_id;
            $config = $payment->configForAppPayment($prepayId);

            return json($this->return_data($config));
        }
    }
}
