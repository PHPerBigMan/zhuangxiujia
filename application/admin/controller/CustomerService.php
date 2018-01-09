<?php
namespace app\admin\controller;
use app\admin\model\JpushMessage as J;
use app\admin\model\Percentage;
use app\admin\model\Service;
use JPush;
class CustomerService extends Base
{
    public function index(){
        $data = Service::find();
        $title = "客服电话";

        return view('customer/index',compact('title','data'));
    }


    public function save(){
        $post = input();

        $s = Service::where('id',1)->update($post);

        return 200;
    }

    public function percentage(){
        $data = Percentage::find();

        $title = "抽成比例";

        return view('customer/percentage',compact('title','data'));
    }

    public function percentageSave(){
        $post = input();

        Percentage::where('id',1)->update($post);

        return 200;
    }
}
