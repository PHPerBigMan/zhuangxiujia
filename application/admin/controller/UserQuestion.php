<?php
namespace app\admin\controller;

use think\Db;
use app\admin\model\userQuestion as P;
use think\Request;


class UserQuestion extends Base
{
    protected $userQuestion;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->userQuestion = new P();
    }

    public function index()
    {

        $title = "用户提问";
        return view("PcWeb/userQuestion", compact('title'));
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取数据列表
     */
    public function GetList(){

        $data = [];
        $data = $this->userQuestion->select();
        foreach ($data as $v){
            $v->caozuo = "<a style=\"text-decoration:none\" onclick=\"hot(this,$v->id)\">推荐</a>
 | <a onclick=answer($v->id,"."'".$v->answer."'".") title=\"编辑\" ><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | 
 <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"del(this,$v->id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";

        }

        return json(['data'=>$data]);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 修改为是否推荐
     */

    public function handel(){
        $Get = input('post.');

        $s = $this->userQuestion->where('id',$Get['id'])->update([
            'is_hot'=>$Get['is_hot']
        ]);

        return returnStatus($s);
    }

    /**
     * 删除数据
     * @return \think\response\Json
     */

    public function del(){
        $id = input('id');
        $s = $this->userQuestion->where('id',$id)->delete();
        return returnStatus($s);
    }

    /**
     * 修改回答内容
     * @return \think\response\Json
     */

    public function saveAnswer(){
       $data = input('post.');
       $s = $this->userQuestion->where('id',$data['id'])->update([
           'answer'=>$data['answer']
       ]);
       return returnStatus($s);
    }
}
