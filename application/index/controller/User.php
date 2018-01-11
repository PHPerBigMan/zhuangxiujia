<?php
namespace app\index\controller;

use app\model\Decorate;
use app\model\MoneyList;
use app\model\UserRw;
use think\Db;
use JPush\Client as JPush;
class User
{
    /**
     * @return \think\response\Json
     * 个人中心首页
     */

    public function index(){
        $id = input('user_id');
        $data = Db::name('User')->where(['id'=>$id])->field('user_phone,user_name,user_money,user_pic,user_sex,user_job,user_area')->find();
        $j = $this->return_data($data);


        return json($j);
    }

    /**
     * 我的任务备用
     * @return \think\response\Json
     */

    public function myTask(){
        $post = input();
         return json($this->return_data(UserRw::MyTask($post)));
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 我的任务 业主端
     */
    public function OwnerTask(){
        $post = input();
        return json($this->return_data(UserRw::OwnerTask($post)));
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description :业主端申请记录
     */
    public function OwnerApply(){
        $post = input();
        return json($this->return_data(Decorate::getAllData($post)));
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 申请详情
     */
    public function OwnerApplyRead(){
        $post = input();
        return json($this->return_data(Decorate::getOneData($post)));
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 提现申请
     */

    public function Withdrawals(){
        $post = input();
        // 添加数据到列表
        $s = MoneyList::add($post);
        // 减少用户的余额

        if($s == 200){
            \app\model\User::where('id',$post['user_id'])->setDec('user_money',$post['money']);
        }

        return returnStatus($s);
    }
    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取用户约
     */
    public function getUserBalance(){
        $post = input();

        $data = \app\model\User::where('id',$post['user_id'])->value('user_money');

        return json($this->return_data($data));
    }
    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 服务商端 我的任务
     */

    public function MerchantTask(){
        $post = input();
        switch ($post['type']){
            // 全部
            case 0:
                $status = [0,1,2,3,4,5,6,7,8,9,10];
                break;
            // 投标中
            case 1:
                $status = [7];
                break;
            // 已中标
            case 2:
                $status = [8];
                break;
            // 设计稿确认中
            case 3:
                $status = [10];
                break;
            // 已完成
            case 4:
                $status = [4];
                break;
        }

        $data = UserRw::alias('ur')->join('renwu t','t.id = ur.rw_id')
            ->where(['ur.user_id'=>$post['user_id'],'t.type'=>2])
            ->whereIn('ur.order_status',$status)
            ->order('ur.id','desc')
           ->select();
        $data = UserRw::ReturnOrderList($data);
        return json($this->return_data($data));
    }


    /**
     * 我的任务列表
     * 0:未付款
     * 1:进行中
     * 2:已完成
     * 5:全部订单
     */

    public function my_renwu()
    {
        $id = input('user_id');
        $type = input('type');
        switch ($type) {
            case 0:
                $where = "user_id = $id AND order_status = 0";
                break;
            case 1:
                $where = "user_id = $id AND order_status = 1 OR order_status = 2 OR order_status = 3";
                break;
            case 2:
                $where = "user_id = $id AND order_status = 4 AND order_status != 5 AND order_status != 6";
                break;
            case 5:
                $where = "user_id = $id";
                break;
            case 7:
                $where = "user_id = $id AND order_status = 7";
        }
        $data = Db::name('UserRw')->where($where)->where('order_type','1')->select();


        foreach ($data as $k => $v) {
            $data[$k]['create_time'] = date('Y/m/d', $v['create_time']);
            $rewu = Db::name('Renwu')->where(['id' => $v['rw_id']])->find();
//            dump(time() - $v['create_time']."time");
//            dump($rewu['pay_limit_time']);
            if (($type == 0) || ($type == 5 && $v['order_status'] == 0)) {
                if ((time() - $v['create_time']) > $rewu['pay_limit_time']) {
                    $data[$k]['pay_limit_time'] = 1;
                    //如果时间超时更高状态为超时
//                    Db::name('UserRw')->where(['orderId' => $v['orderId']])->update(['order_status' => 5]);
//                    Db::name('Renwu')->where(['id' => $v['id']])->update(['rw_status' => 2]);
                } else {
//                    $data[$k]['pay_limit_time']     = ($rewu['pay_limit_time']-(time()-$rewu['pay_limit_time'])) / 60;

                    // TODO::支付倒计时
                    $time = $rewu['pay_limit_time'] - (time() - $v['create_time']);

                    $data[$k]['pay_limit_time'] = date('s',$time) * 1000;
                }
            }
            $data[$k]['img']                = $rewu['rw_cover'];
            $data[$k]['rw_title']           = $rewu['rw_title'];
            $data[$k]['rw_yj']              = $rewu['rw_yj'];
            $data[$k]['rw_ding']            = $rewu['rw_ding'];
            $data[$k]['order_status']       = Db::name('UserRw')->where(['orderId'=>$v['orderId']])->value('order_status');
            switch ($data[$k]['order_status']){
                case 0:
                    $msg = "待付款";
                    break;
                case 1:
                    $msg = "进行中";
                    break;
                case 2:
                    $msg = "进行中";
                    break;
                case 3:
                    $msg = "进行中";
                    break;
                case 4:
                    $msg = "审核通过任务完成";
                    break;
                case 5:
                    $msg = "已取消";
                    break;
                case 6:
                    $msg ="已取消" ;
                    break;
                case 7:
                    $msg = "投标中";
                    break;
                case 8:
                    $msg = "已中标";
                    break;
                case 9:
                    $msg = "未中标";
                    break;

                default :
                    $msg = "已完成";

            }
            $data[$k]['status_msg'] = $msg;
        }
//        dump($data);die;
        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 任务确定
     */

    public function order_read(){
//        echo time();die;
        $orderId            = input('orderId');
        $data               = Db::name('UserRw')->where(['orderId'=>$orderId])->field('rw_id,order_status,create_time,order_remark')->find();
        $rw                 = Db::name('Renwu')->where(['id'=>$data['rw_id']])->field('rw_title,create_time,start_time,rw_yj,rw_ding,pay_limit_time')->find();
        $data['rw_title']   = $rw['rw_title'];

        $data['start_time']     = date("Y/m/d",$rw['start_time']);
        $data['rw_yj']          = $rw['rw_yj'];
        $data['rw_ding']        = $rw['rw_ding'];

        switch ($data['order_status']){
            case 0:
                $msg = "定金支付剩余时间";
                if ((time() - $data['create_time']) > $rw['pay_limit_time']) {
                    //如果时间超时更高状态为超时
                    Db::name('UserRw')->where(['orderId' => $data['orderId']])->update(['order_status' => 10]);
//                    Db::name('Renwu')->where(['id' => $data['id']])->update(['rw_status' => 2]);
                } else {
//                    $data[$k]['pay_limit_time']     = ($rewu['pay_limit_time']-(time()-$rewu['pay_limit_time'])) / 60;

                    $time = $rw['pay_limit_time'] - (time() - $data['create_time']);

                    $data['pay_limit_time'] = date('s',$time) * 1000;
                }
                break;
            case 1:
                $msg = "任务进行中";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
                break;
            case 2:
                $msg = "任务审核中";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
                break;
            case 3:
                $msg = "审核未通过";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
                break;
            case 4:
                $msg = "任务已完成";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
                break;
            case 5:
                $msg = "定金支付超时,此订单已自动取消";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
                break;
            case 7:
                $msg = "任务投标中";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
                break;
            case 8:
                $msg = "任务投标已中";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
                break;
            case 9:
                $msg = "任务投标未中";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
                break;
            default:
                $msg = "订单已取消";
                $data['pay_limit_time']        = $rw['pay_limit_time'];
        }
        $data['msg'] = $msg;
        $data['create_time']    = date("Y/m/d",$rw['create_time']);
        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * 用户完成任务
     */

    public function order_status(){
        $orderId            = input('orderId');
        $type               = input('type');
        $order_status       = input('order_status');
        switch ($order_status){
            case 1:
                $o_status = 2;
                break;
            case 5:
                $o_status = 5;
                break;
            case 6:
                $o_status = 6;
                break;
        }
        if($o_status == 6){
            // 取消订单 任务释放
            $TaskId = UserRw::where('orderId',$orderId)->value('rw_id');
            \app\model\Renwu::where('id',$TaskId)->update([
                'status'=>2
            ]);
        }

        $s = Db::name('UserRw')->where(['orderId'=>$orderId])->update(['order_status'=>$o_status]);


        if($s){
            $msg['code'] = 200;
            $msg['msg']  = "操作成功";
        }else{
            $msg['code'] = 404;
            $msg['msg']  = "操作失败";
        }

        return json($msg);
    }

    /**
     * @return \think\response\Json
     * 我的案例
     */
    public function my_anli(){
        $id = input('user_id');
        $data = Db::name('UserAnli')->where(['user_id'=>$id])->field('id,pic,title,priceRanges as quarters,decorativeStyles as style,
        houseTypes as house,acreageTypes as acreage')->select();

        foreach($data as $k=>$v){
            if(!empty($v['pic'])){
                $img = json_decode($v['pic'],true);
                $data[$k]['pic'] = $img[0];
            }else{
                $data[$k]['pic'] = "";
            }
        }

        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 我的案例详情
     */

    public function anli(){
       $id = input('id');
       $data = Db::name('UserAnli')->where(['id'=>$id])->field('id,title,content,pic')->find();
       $data['pic'] = json_decode($data['pic'],true);
       $j = $this->return_data($data);

       return json($j);
    }

    /**
     * @return \think\response\Json
     * 系统消息列表
     */

    public function my_notice(){
        $id = input('user_id');
        $data = Db::name('Notice')->where(['user_id'=>$id])->field('id,content,title,create_time,detail')->select();
        foreach($data as $k=>$v){
            $data[$k]['create_time'] = date('Y-m-d',$v['create_time']);
            $data[$k]['content'] = mb_substr($v['content'],0,15);
        }
        $j =$this->return_data($data);
        return json($j);
    }


    /**
     * @return \think\response\Json
     * 系统消息详情
     */

    public function notice(){
        $id         = input('id');
        $data                   =  Db::name('Notice')->where(['id'=>$id])->field('id,title,content,img,create_time,detail')->find();
        $data['create_time']    = date('Y-m-d H:i',$data['create_time']);
//        $data['img']            = json_decode($data['img'],true);
        $j = $this->return_data($data);
        return json($j);
    }


    /**
     * @return \think\response\Json
     * 客服电话
     */

    public function kefu(){
        $data = Db::name('Kefu')->find();
        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 用户信息
     */

    public function my_data(){
        $id = input('user_id');

        $data = Db::name('User')->where(['id'=>$id])->field('id,user_phone,user_name,user_pic,user_city,user_sex,user_job,user_area')->find();
//        var_dump($data);die;
        $j = $this->return_data($data);

        return json($j);
    }

    /**
     * @return \think\response\Json
     * 保存个人信息
     */

    public function my_save(){
//        echo 1;die;
        $id = input('user_id');
        $user_name = input('user_name');
        $user_sex = input('user_sex');
        $user_job = input('user_job');
        $user_area = input('user_area');
        $user_city = input('user_city');
        if ((request()->file('user_pic')) != NULL) {
            $file = request()->file('user_pic');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            $user_pic = '/uploads/' . $info->getSaveName();
        }
        $s = Db::name('User')->where(['id'=>$id])->update([
            'user_name'=>$user_name,
            'user_pic'=>empty($user_pic) ? NULL :$user_pic,
            'user_sex'=>$user_sex,
            'user_job'=>$user_job,
            'user_area'=>$user_area,
            'user_city'=>$user_city
        ]);

        if($s){
            $code = 200;
            $msg = "保存成功";
        }else{
            $code = 404;
            $msg = "保存失败,请检查";
        }

        $j = [
            'code'=>$code,
            'msg'=>$msg
        ];
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 我要装修
     */

    public function zx(){
        $data['user_id']    = input('user_id');
        $data['room_type']  = input('room_type');
        $data['room_mj']    = input('room_mj');
        $data['room_ys']    = input('room_ys');
        $data['room_time']  = input('room_time');
        $data['phone']      = input('phone');
        $data['remark']     = input('remark');

        $files = request()->file('imgs');
        if($files) {
            foreach ($files as $k => $file) {
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    $img[$k] = '/uploads/' . $info->getSaveName();
                }
            }

            $data['imgs'] = json_encode($img);
        }


        $s = Decorate::create($data);
        if($s){
            $msg['code'] = 200;
            $msg['msg']  = "保存成功";
        }else{
            $msg['code'] = 404;
            $msg['msg']  = "保存失败";
        }

        return json($msg);
    }


    /**
     * @return \think\response\Json
     * 保存案例
     */

    public function anli_save(){
        $data = input('');
        $data['is_pass']    = 0;
        $data['jidian']     = 0;
        $data['create_time']= time();

        // 案例图片
        if(request()->file('pic') !=NULL){
            $files = request()->file('pic');
            foreach($files as $k=>$file){
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $user_pic[$k] = DS.'uploads'.DS.$info->getSaveName();
                }
            }
        }

        $url = SITE_URL;
        foreach($user_pic as $k=>$v){
            $save_url = $url.'/'.$v;
            $user_img[$k] = "<img src='$save_url' style='width: 330px;height: 180px'>";
        }

        Db::name('log')->insert([
            'msg'=>'案例',
            'data'=>json_encode($user_pic)
        ]);
        // 案例封面
        if(request()->file('cover') !=NULL){
            $files = request()->file('cover');

            foreach($files as $k=>$file){
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $cover[$k] = DS.'uploads'.DS.$info->getSaveName();
                }
            }

            $data['cover'] = json_encode($cover);
        }

        //初始圖片img 样式
        $data['content'] = "<p>".$data['content']."</p>";
        $user_img = implode('',$user_img);
        $data['pass_content'] = $data['content'].$user_img;
//        dump($data);die;


        // 案例多图
        $data['pic']  = json_encode($user_pic);

        $data['priceRanges']        = $data['quarters'];
        $data['decorativeStyles']   = $data['style'];
        $data['houseTypes']         = $data['house'];
        $data['acreageTypes']       = $data['acreage'];

        unset( $data['quarters']);
        unset( $data['style']);
        unset( $data['house']);
        unset( $data['acreage']);

        $s = Db::name('UserAnli')->insert($data);

        if($s){
            $msg['code'] = 200;
            $msg['msg'] = "保存成功";
        }else{
            $msg['code'] = 404;
            $msg['msg'] = "保存失败";
        }
        return json($msg);
    }


    /**
     * @return \think\response\Json
     * 流水列表
     */

    public function money_list(){
//        echo time();die;
        $id = input('user_id');
        $data = Db::name('MoneyList')->where(['user_id'=>$id])->select();
        foreach ($data as $k=>$v){
            $data[$k]['create_time'] = date('Y-m-d',$v['create_time']);
            $order_id = $v['order_id'];
            $rw_id = Db::name('UserRw')->where(['orderId'=>$order_id])->value('rw_id');
            $data[$k]['rw_title'] = Db::name('Renwu')->where(['id'=>$rw_id])->value('rw_title');
        }

        $j = $this->return_data($data);

        return json($j);
    }



    public function ji_tui(){

        $app_key = "7199c61d94a3475cf74582a8";
        $master_secret = "7711af29e9f1dbc4816fdd3b";
        $client = new JPush($app_key,$master_secret);

        $pusher = $client->push();
        $pusher->setPlatform('all');
        $pusher->addAllAudience();
        $pusher->setNotificationAlert('Hello, JPush');
        try {
            $pusher->send();
        } catch (\JPush\Exceptions\JPushException $e) {
            // try something else here
            print $e;
        }
    }


    protected function return_data($data){
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


    public function is_login(){
        if(empty(session('user_id'))){
            $j = [
                'status'=>404,
                'msg'=>'用户未登录'
            ];
        }else{
            $j = [
                'status'=>200,
                'msg'=>'用户已登录'
            ];
        }
        return json($j);
    }


    public function de(){
        return view('User/index');
    }

    /**
     * @return \think\response\Json
     * 用户删除案例
     */

    public function del(){
        $id = input('param.id/d');
        $s  = Db::name('user_anli')->where("id",$id)->delete();

        if($s){
         $code = 200;
         $msg = '案例删除成功';

        }else{
            $code = 404;
           $msg = '案例删除失败';
        }
        $j = [
            'code'=>$code,
            'msg'=>$msg
        ];

        return json($j);


    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取设计师简历
     */
    public function Resume(){
        $data = \app\model\User::findOrFail(input('user_id'));


        return json($this->return_data($data));
    }
}
