<?php
namespace app\admin\controller;
use app\admin\model\JpushMessage as J;
use JPush;
class JpushMessage extends Base
{
    public function index(){
        $data = J::order("id","desc")->select();

        $title = "系统消息";

        return view('Jpush/list',compact('data','title'));
    }


    public function send(){
        $title = "发送消息";

        return view('Jpush/send',compact('title'));
    }

    /**
     * author hongwenyang
     * method description : 发送极光推送消息
     */

    public function sendMessage(){
        $post = input();

        $config = config('JPush');


        if($post['brandclass']){
            // 单独发送
            // 先根据手机号查询用户信息
//            $suer_id = \app\model\User::where('user_phone',$post['']);
        }else{
            // 全体消息
            $code = \app\admin\model\JpushMessage::sendAllMessage($config,$post['message']);
            $user_id = 0;
        }

        if($code == 200){
            // 保存到数据表
            J::create([
                'user_id'=>$user_id,
                'message'=>$post['message']
            ]);
        }

        return json($code);
    }
}
