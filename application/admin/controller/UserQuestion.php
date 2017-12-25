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
        $data = $this->userQuestion->where('is_common',0)->order('created_at','asc')->select();
        foreach ($data as $v){
            $v->caozuo = "<a style=\"text-decoration:none\" onclick=\"hot(this,$v->id)\">是否精彩问答</a>
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
           'answer'=>$data['answer'],
           'answer_at'=>date("Y-m-d",time()),
           'is_answer'=>1,
           'author'=>'翼家装饰'
       ]);
       return returnStatus($s);
    }

    public function save(){
        $data = input('post.');
        $s = $this->userQuestion->where('id',$data['id'])->update([
            'is_common'=>1
        ]);

        return returnStatus($s);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 新增装修小知识
     */

    public function add(){
        $data = input('post.');
        if(empty($data['id'])){
            $s = $this->userQuestion->insert([
                'question'=>$data['title'],
                'answer'=>$data['content'],
                'is_common'=>1
            ]);
        }else{
            $s = $this->userQuestion->where('id',$data['id'])->update([
                'question'=>$data['title'],
                'answer'=>$data['content'],
                'is_common'=>1
            ]);
        }

        return returnStatus($s);
    }


    public function common(){
        $title = "装修小常识";

        return view('PcWeb/common',compact('title'));
    }


    public function GetListCommon(){
        $data = $this->userQuestion->where('is_common',1)->select();


        foreach($data as $v){
            $v->caozuo = "<a href='commonRead?id=$v->id' title=\"编辑\" ><i class=\"Hui-iconfont\" >&#xe6df;</i></a> |
 <a style=\"text-decoration:none\" class=\"ml-5\" onClick=\"del(this,$v->id)\" href=\"javascript:;\" title=\"删除\"><i class=\"Hui-iconfont\">&#xe6e2;</i></a>";
        }

        return json(['data'=>$data]);
    }

    public function commonRead(){
        $id = input('id');

        $title = '装修小常识编辑';

        $data = $this->userQuestion->where('id',$id)->find();

        if(!$id){
            $data = json_decode('{}');
            $data->id = "";
        }
        return view('/PcWeb/commonRead',compact('data','title'));
    }
}
