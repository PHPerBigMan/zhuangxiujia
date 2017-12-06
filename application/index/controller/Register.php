<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Flc\Alidayu\Requests\IRequest;

class Register extends Controller
{

    public function phone(){



    }

    /**
     * 获取验证码
     */

   public function code(){
       //发送短信
       $mobile = input('mobile');
       $code = "";
       if (empty($code)) {
           $code = rand(1000, 9999);
           session('user.code',$code);
       }
       $message = new Sms(
           'LTAImTp8dtSHGBy9',
           'KNPpBEUVkBhc0GvAR7JDrmDxT0GLhf'
       );
       $response = $message->sendSms(
           "装修之家", // 短信签名
           "SMS_78720146", // 短信模板编号
           "$mobile", // 短信接收者
           Array(  // 短信模板中字段的值
               "code"=>"$code",
               "product"=>"dsd"
           ),
           "123"
       );
       if($response->Message == "OK"){
           $code = 200;
           $msg  = "短信发送成功";
       }else{
           $code = 404;
           $msg  = "账户余额不足";
       }

       session('user.phone',$mobile);

       $j = [
           'code'=>$code,
           'msg'=>$msg
       ];

       return json($j);
   }


    /**
     * @return \think\response\Json
     * 手机验证码登录
     */

   public function login(){
       session('user.code',1);
       session('user.phone',13858126467);
       $mobile  = input('mobile');
       $code    = input('code');
       if(empty($mobile)){
           $code = 404;
           $msg = "手机号不能为空";
       }else{
           if($mobile != session('user.phone') ){
               $code  = 404;
               $msg   = "请检查手机号是否正确";
           }else if($mobile == session('user.phone')&& $code != session('user.code')){
               $code = 404;
               $msg  = "请输入正确的验证码";
           }else{
               if(empty(Db::name('User')->where(['user_phone'=>$mobile])->value('id'))){
                   $data['user_phone'] = $mobile;
                   $data['create_time'] = time();
                   Db::name('User')->insert($data);
               }
               $data['user_phone'] = $mobile;
               $id = Db::name('User')->where(['user_phone'=>$mobile])->value('id');
               session('user.id',$id);
               $code   = 200;
               $msg  = "登录成功";
           }
       }

       $j = [
           'code'=>$code,
           'msg' =>$msg,
           'user_id'=> empty($id) ? "" :(int)$id
       ];
       return json($j);
   }

    /**
     * @return \think\response\Json
     * web 端注册
     */

   public function add_pwd(){
      $data                 = input('post.');
      $map['user_phone']    = $data['user_phone'];
      $d = Db::name('User')->where($map)->find();
      if($d){
          if(empty($d['user_pwd']) || $data['type'] == 1){
              Db::name('User')->where($map)->update(['user_pwd'=>sha1($data['user_pwd'])]);
              $code = 200;
              $msg = "绑定密码成功";
          }else{
              if($data['type'] == 0){
                  $code = 403;
                  $msg = "已注册,无需重复操作";
              }
          }
      }else{
          $data['create_time'] = time();
          Db::name('User')->insert($data);
          $code = 200;
          $msg = '注册成功';
      }

      $j = [
          'code'=>$code,
          'msg'=>$msg
      ];

      return json($j);
   }


   public function a(){
       $msg['code'] = 200;
       return json($msg);
   }

   public function web_login(){
       $data = input('post.');
       $data['user_pwd'] = sha1($data['user_pwd']);
       $d = Db::name('User')->where($data)->find();
       if($d){
           $code = 200;
           $msg = "登录成功";
           session('web_user',$d['id']);
       }else{
           $code = 404;
           $msg = "请检查账号密码是否正确";
       }

       $j = [
           'code'=>$code,
           'msg'=>$msg
       ];
       return json($j);
   }

   public function login_out(){
       session('web_user',null);

   }
}
