<?php
namespace app\admin\controller;

use think\Db;
use app\admin\model\NormalQuestion as N;
use think\Request;


class NormalQuestion extends Base
{
    protected $normalQuestion;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->normalQuestion = new N();
    }

    public function index()
    {

        $title = "常见问答";
        return view("PcWeb/normalQuestion", compact('title'));
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 获取数据列表
     */
    public function GetList(){

        $data = [];
        $data = $this->normalQuestion->select();

        foreach ($data as $v){
            $v->caozuo = " <a onclick=answer($v->id,"."'".$v->answer."'".","."'".$v->question."'".") title=\"编辑\"><i class=\"Hui-iconfont\" >&#xe6df;</i></a> | 
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

        $s = $this->normalQuestion->where('id',$Get['id'])->update([
            'is_hot'=>$Get['is_hot']
        ]);

        return returnStatus($s);
    }


    /**
     * 修改回答内容
     * @return \think\response\Json
     */

    public function saveAnswer(){
        $data = input('post.');
        if($data['id']){
            $s = $this->normalQuestion->where('id',$data['id'])->update([
                'answer'=>$data['answer'],
                'question'=>$data['question']
            ]);
        }else{
            $s = $this->normalQuestion->insert([
                'answer'=>$data['answer'],
                'question'=>$data['question']
            ]);
        }
        return returnStatus($s);
    }


    public function del(){
        $id = input('id');
        $s = $this->normalQuestion->where('id',$id)->delete();
        return returnStatus($s);
    }
}
