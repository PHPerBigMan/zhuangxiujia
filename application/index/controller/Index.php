<?php
namespace app\index\controller;

use think\Db;

class Index
{
    /**
     * @return \think\response\Json
     * 首页的一级分类
     */

    public function first(){
        $data = Db::name('Cat')->where(['level'=>1])->field('cat_name,level,id c_id,cat_img img')->select();
        $msg  = empty($data) ?  "数据为空": "获取数据成功";

        $j = [
            'data'  =>$data,
            'status'=>200,
            'msg'   =>$msg
        ];

        return json($j);
    }


    /**
     * @return \think\response\Json
     * 二级分类
     */

    public function sec(){

        $id     = input('c_id');

        $data = Db::name('Cat')->where(['p_id'=>$id])->field('id,level,cat_name')->select();

        if(empty($data)){
            $msg = "该分类下无二级分类";
        }else{
            $msg = "获取数据成功";
        }

        $j = [
            'data'      =>empty($data)? array() :$data,
            'status'    =>200,
            'msg'       =>$msg
        ];

        return json($j);
    }

    /**
     * @return \think\response\Json
     * 二级分类下的任务
     */
    public function sec_rw(){
        $id = input('id');
        $area = input('area');
        if(!empty($area)){
            $data   = Db::name('Renwu')->where(['rw_cat'=>$id])->where("rw_status = 2")->where("is_show = 1")->where("rw_area","like","%$area%")->field('rw_title,start_time,id,rw_yj,rw_cover img')->select();
        }else{
            $data   = Db::name('Renwu')->where(['rw_cat'=>$id])->where("rw_status = 2")->where("is_show = 1")->field('rw_title,start_time,id,rw_yj,rw_cover img')->select();
        }

        foreach($data as $k=>$v){
            $data[$k]['start_time'] = date('Y/m/d');
        }
        $j = $this->return_data($data);

        return json($j);
    }

    public function banner()
    {
        $Img    = Db::name('Banner')->field('banner_url url')->where(['type'=>0])->select();

        if(empty($Img)){
            $status = 200;
            $msg    = "数据为空";
        }else{
            $status = 200;
            $msg    = "获取数据成功";
        }
        $j = [
            'data'  =>$Img,
            'status'=>$status,
            'msg'   =>$msg
        ];
        return json($j);
    }


    /**
     * 热门任务
     */

    public function rewu(){
        $area = input('area');
        if(!empty($area)){
            $data = Db::name('Renwu')->where("rw_hot = 1 AND is_show = 1 AND rw_status = 2")->where('rw_area','like',"%$area%")->field('id,rw_title,rw_yj,start_time,rw_cover img')->select();
        }else{
            $data = Db::name('Renwu')->where(['rw_hot'=>1,'is_show'=>1,'rw_status'=>2])->field('id,rw_title,rw_yj,start_time,rw_cover img')->select();
        }

        foreach($data as $k=>$v){
            $data[$k]['start_time'] = date('m'.'日'.'d'.'号',$v['start_time']);
        }

        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 经典案例
     */

    public function anli(){
        $data = Db::name('UserAnli')->where(['jidian'=>1])->field('id,pic,title')->limit(4)->select();
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
     * 获取市级信息
     */

    public function province(){
        $id     = input('id');
        $provinceId = Db::name('HatProvince')->where(['id'=>$id])->value('provinceID');
        $data   = Db::name('HatCity')->where(['father'=>$provinceId])->field('city')->select();
        foreach($data as $k=>$v){
            $data[$k] = $v['city'];
        }
        $j = $this->return_data($data);
        return json($j);
    }

    /**
     * @return \think\response\Json
     * 获取市级信息
     */

    public function pro(){
        $data = Db::name('HatProvince')->field('id,province p_name')->select();
        $j = $this->return_data($data);
        return json($j);
    }


    /**
     * 平台介绍
     */

    public function intro(){
        $data = Db::name('Intro')->find();
        $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);
        $version = Db::name('Config')->field('version,download')->find();
        $version['version'] = (int)$version['version'];

        if(empty($data)){
            $data = array();
            $msg = '数据为空';
        }else{
            $msg = '获取数据成功';
        }

        $j = [
            'data'=>$data,
            'version'=>$version,
            'status'=>200,
            'msg'=>$msg
        ];
        return json($j);
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


    public function index()
    {
        $fir = Db::name('AnliCat')->where(['is_show'=>1,'level'=>1])->field('id,cat_name')->select();
        foreach ($fir as $k=>$v){
            $data[$k]['cat_name']   = $v['cat_name'];
            $data[$k]['id']         = $v['id'];
            $data[$k]['sec']        = Db::name('AnliCat')->where(['p_id'=>$v['id']])->field('id,cat_name')->select();
        }
        $j = [
            'data'=>$data,
            'title'=>'首页',
        ];
        return view('home',$j);
    }


    public function login()
    {
        $j = [
            'title'=>'首页'
        ];
        return view('login',$j);
    }

    public function register()
    {
        $j = [
            'title'=>'首页'
        ];
        return view('register',$j);
    }

    public function forget()
    {
        $j = [
            'title'=>'首页'
        ];
        return view('forget',$j);
    }

    public function Ilist()
    {
        $type = input('type');
        switch ($type){
            case 1:
                $data = Db::name('WebNotice')->order('create_time desc')->paginate(12);
                $nav = [
                    ["url"=>"/list/1", "name"=>"公告"],
                    ["url"=>"/list/1", "name"=>"公告列表"]
                ];
                $url = '/detail/1/';
                break;
            case 2:
                $data = Db::name('Youhui')->where(['is_show'=>1])->order('create_time desc')->paginate(12);
                $nav = [
                    ["url"=>"/list/2", "name"=>"最新优惠"],
                    ["url"=>"/list/2", "name"=>"优惠列表"]
                ];
                $url = '/detail/2/';
                break;
            case 3:
                $data = Db::name('Hot')->where(['is_show'=>1])->order('create_time desc')->paginate(12);
                $nav = [
                    ["url"=>"/list/3", "name"=>"热点"],
                    ["url"=>"/list/3", "name"=>"热点列表"]
                ];
                $url = '/detail/3/';
                break;
            case 4:
                $data = Db::name('new')->where(['is_show'=>1])->order('create_time desc')->paginate(12);
                $nav = [
                    ["url"=>"/list/4", "name"=>"公司新闻"]
                ];
                $url = '/detail/7/';
                $title = "公司介绍";
        }
        $page = $data->render();
        $data = $data->all();
        foreach($data as $k=>$v){
            $data[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
            $data[$k]['url'] = $url.$v['id'];
        }


        $j = [
            'nav'=>$nav,
            'data'=>json_encode($data),
            'page'=>$page,
            'title'=> empty($title) ? "首页" : $title
        ];
        return view('list',$j);
    }

    public function detail()
    {
        $type   = input('type');
        $id     = input('id');
        $map['id'] = $id;
        switch ($type){
            case 1:
                $data = Db::name('WebNotice')->where($map)->find();
                $nav = [
                    ["url"=>"/list/1", "name"=>"公告"],
                    ["url"=>"", "name"=>"公告详情"],
                ];
                $title = '首页';
                break;
            case 2:
                $data = Db::name('Youhui')->where($map)->find();
                $nav = [
                    ["url"=>"/list/2", "name"=>"最新优惠"],
                    ["url"=>"", "name"=>"优惠详情"],
                ];
                $title = '首页';
                break;
            case 3:
                $data = Db::name('Hot')->where($map)->find();
                $nav = [
                    ["url"=>"/list/3", "name"=>"热门"],
                    ["url"=>"", "name"=>"热门详情"],
                ];
                $title = '首页';
                break;
            case 4:
                $data = Db::name('Zl')->where($map)->field('zl_title title,content,create_time')->find();
                $nav = [
                    ["url"=>"/decorate-train/1", "name"=>"资料"],
                    ["url"=>"", "name"=>"资料详情"],
                ];
                $title = '装修管理';
                break;
            case 5:
                $data = Db::name('UserAnli')->where($map)->field('title,pass_content content,create_time')->find();
                $nav = [
                    ["url"=>"/decorate-case/0", "name"=>"装修案例"],
                    ["url"=>"", "name"=>"案例详情"],
                ];
                $title = '装修案例';
                break;
            case 6:
                $data = Db::name('Intro')->where(['type'=>0])->find();
                $nav = [
                    ["url"=>"/detail/6", "name"=>"公司简介"],
                ];
                $title = '公司介绍';
                break;
            default:
                $data = Db::name('new')->where($map)->find();
                $nav = [
                    ["url"=>"", "name"=>"公司新闻"],
                ];
                $title = '公司介绍';
        }

        $j = [
            'data'=>$data,
            'nav'=>$nav,
            'title'=>$title
        ];
        return view('detail',$j);
    }

    public function free()
    {
        return view('free');
    }

    public function user()
    {
        $data = Db::name('User')->where(['id'=>session('web_user')])->find();

        $j = [
            'title'=>'首页',
            'data'=>$data
        ];
        return view('user',$j);
    }

    public function userEdit()
    {
        $data = Db::name('User')->where(['id'=>session('web_user')])->find();
        $j = [
            'title'=>'首页',
            'data'=>$data
        ];
        return view('user-edit',$j);
    }

    // 装修任务 发布 or 接受
    public function userTask()
    {
        $type = input('type');
        switch ($type){
            case 1:
                $data = Db::name('zx')->where(['user_id'=>session('web_user')])->select();
                $active = 1;
                break;
            case 2:
                $data = Db::name('zx')->where(['user_id'=>session('web_user')])->select();
                $active = 2;
                break;
        }
        $j = [
            'title'=>'首页',
            'data'=>$data,
            'active'=>$active,
        ];
        return view('user-task',$j);
    }

    // 我要装修
    public function wantDecorate()
    {

        $data = Db::name('Intro')->where(['type'=>1])->select();
        if(!empty(input('type'))){
            switch (input('type')){
                case 1:
                    $title = "免费设计";
                    break;
                case 2:
                    $title = "免费验房";
                    break;
                case 3:
                    $title = "免费量房";
                    break;
                case 4:
                    $title = "免费报价";
                    break;
                case 5:
                    $title = "免费装修";
                    break;
            }
            $desc = Db::name('Intro')->where(['title'=>$title,'type'=>2])->find();

            $fuwu = Db::name('Intro')->where(['type'=>5])->value('content');
        }

        $j = [
            'data'=>$data,
            'title'=>'我要装修',
            'type'=>input('type'),
            'desc'=>empty($desc) ? "" :$desc,
            'fuwu'=>empty($fuwu) ? "" :$fuwu,
        ];

        return view('want-decorate',$j);
    }

    // 装修培训
    public function decorateTrain()
    {
        $keyword = input('keyword');
        if(empty($keyword)){
            $map['zl_cat'] = input('id');
            $data = Db::name('Zl')->where($map)->paginate(16);
            $cat_name = Db::name('ZlCat')->where(['id'=>$map['zl_cat']])->value('cat_name');
        }else{
            $data = Db::name('Zl')->where("zl_title","like","%$keyword%")->paginate(16);
            $cat_name = '装修培训';
        }
        $page = $data->render();
        $data = $data->all();
        foreach($data as $k=>$v){
            $pic = json_decode($v['zl_pic'],true);
            $data[$k]['zl_pic']         = $pic[0];
            $data[$k]['zl_title']       = mb_substr($v['zl_title'],0,10);
            $data[$k]['create_time']    = date('Y.m.d',$v['create_time']);
        }

        $nav = [
            ["url"=>"/decorate-train/".input('id'), "name"=>"装修管理"],
            ["url"=>"/decorate-train/".input('id'), "name"=>"$cat_name"],
        ];
        $j = [
            'data'=>json_encode($data),
            'page'=>$page,
            'nav'=>$nav,
            'title'=>'装修管理'
        ];
        return view('decorate-train',$j);
    }

    // 装修案例
    public function decorateCase()
    {
        $id     = input('cat1');
        $type   = input('cat2');

        //分类数据
        $data = Db::name('AnliCat')->where(['level'=>1])->field('id,cat_name')->select();

        foreach($data as $k=>$v){
            $data[$k]['sec'] = Db::name('AnliCat')->where(['p_id'=>$v['id']])->field('id,cat_name')->select();

            foreach($data[$k]['sec'] as $k1=>$v1){
                if($id != 0){
                    if($v1['id'] == $id){
                        $data[$k]['sec'][$k1]['active'] = 1;
                        if($data[$k]['sec'][$k1]['active'] == 1){
                            $data[$k]['active'] = 1;
                        }
                    }else{
                        $data[$k]['sec'][$k1]['active'] = 0;
                    }
                }else{
                    $data[$k]['sec'][$k1]['active'] = 0;
                    $data[$k]['active'] = 0;
                }
            }
        }


        if($id == 's'){
            $anli = Db::name('UserAnli')->where('title','like',"%$type%")->where(['is_pass'=>1])->limit(4)->select();
            $count = ceil(Db::name('UserAnli')->where('title','like',"%$type%")->where(['is_pass'=>1])->count()/4);
        }else{
            if(!empty($type)){
                switch($type){
                    case 1:
                        $map['houseTypes'] = $id;
                        break;
                    case 2:
                        $map['acreageTypes'] = $id;
                        break;
                    case 3:
                        $map['decorativeStyles'] = $id;
                        break;
                    default:
                        $map['priceRanges'] = $id;
                }
                //案例数据
                $anli = Db::name('UserAnli')->where($map)->where(['is_pass'=>1])->limit(4)->select();
                $count = ceil(Db::name('UserAnli')->where(['is_pass'=>1])->where($map)->count()/4);
            }else{
                $anli = Db::name('UserAnli')->where(['is_pass'=>1])->limit(4)->select();
                $count = ceil(Db::name('UserAnli')->where(['is_pass'=>1])->count()/4);
            }
        }
        foreach($anli as $k=>$v){
            $anli[$k]['pic'] = json_decode($v['pic'],true);
            $anli[$k]['title'] = mb_substr($v['title'],0,10,'utf-8');
            if(count($anli[$k]['pic'] ) >= 4){
                $anli[$k]['pic'] = array_slice($anli[$k]['pic'],0,3);
            }else{
                $anli[$k]['pic'] = array_slice($anli[$k]['pic'],0,count($anli[$k]['pic']));
            }

            $anli[$k]['count'] = count($anli[$k]['pic']);
        }
        $nav = [
            ["url"=>"/decorate-case/0", "name"=>"装修案例"],
        ];
        $j = [
            'data'=>$data,
            'title'=>'装修案例',
            'anli'=> empty($anli) ? "" :$anli,
            'count'=>$count == "" ? "" : $count,
            'nav'=>$nav
        ];
        return view('decorate-case',$j);
    }

    // 装修案例详情
    public function caseDetail()
    {
        return view('case-detail');
    }

    // 装修案例详情
    public function decorateMall()
    {
        $cat = Db::name('MallCat')->where(['level'=>1])->select();

        $data = Db::name('Product')
            ->alias('p')
            ->join('MallCat m','m.id = p.pro_cat')
            ->where('p.is_show','1')
            ->field('p.id,p.pro_name,p.pro_price,m.p_id,p.pro_img,p.pro_url,p.pro_desc,p.pro_cat')
            ->limit(8)
            ->select();

        foreach($cat as $k=>$v){
            foreach ($data as $k1=>$v1){
                if($v['id'] == $v1['p_id']){
                    $pro[$v['id']]['pro'][] = $v1;
                    $pro[$v['id']]['cat_name'] = Db::name('MallCat')->where(['id'=>$v['id']])->value('cat_name');
                    $pro[$v['id']]['cat_id'] = $v['id'];
                }
            }
            $cat[$k]['sec'] = Db::name('MallCat')->where(['p_id'=>$v['id']])->select();
        }

        $product = Db::name('Product')->limit(4)->select();
        if(empty($product)){
            for($i=0;$i<4;$i++){
                $img[$i] = MALL_IMG;
            }
        }else{
            foreach($product as $k=>$v){
                if($v['pro_img'] == ""){
                    $img[$k] = MALL_IMG;
                }else{
                    $img[$k] = $v['pro_img'];
                }
            }
        }
        if(empty($img)){
            $img[0] = "/uploads/20170725/b0f7b27c735a891567305766c47850cc.png";
            $img[1] = "/uploads/20170725/b0f7b27c735a891567305766c47850cc.png";
            $img[2] = "/uploads/20170725/b0f7b27c735a891567305766c47850cc.png";
            $img[3] = "/uploads/20170725/b0f7b27c735a891567305766c47850cc.png";
        }
        $j = [
            'data'=>json_encode($pro),
            'cat'=>json_encode($cat),
            'title'=>'装修商城',
            'pic'=>$img
        ];

        return view('decorate-mall',$j);
    }


    //商品列表
    public function goodsList()
    {
        //type == 0 表示全部
        $type           = input('type');

        $first_id       = input('first');
        $second_id      = input('second');
        $third_id       = input('third');

        $first = Db::name('MallCat')->where(['level'=>1])->field('id,cat_name')->select();

        $second = Db::name('MallCat')->where(['level'=>2])->field('id,cat_name,p_id')->select();

        $third = Db::name('MallCat')->where(['level'=>3])->field('id,cat_name')->select();
        $product = Db::name('Product')->field('id,pro_name,pro_price,pro_desc,pro_img,pro_url')->where(['is_show'=>1])->paginate(12);

        if(!empty($first_id) && (empty($second_id) &&(empty($third_id)))){
            $product = Db::name('Product')->field('id,pro_name,pro_price,pro_desc,pro_img,pro_url')->where(['is_show'=>1,'pro_first'=>$first_id])->paginate(12);

        } else if(($second_id != 0) && empty($third_id) && ($first_id!=0)){
            $product  = Db::name('Product')->field('id,pro_name,pro_price,pro_desc,pro_img,pro_url')->where([
                'pro_cat'=>$second_id,
                'pro_first'=>$first_id,
                'is_show'=>1
            ])->paginate(12);
        }else if(($second_id != 0) && empty($third_id) && ($first_id==0)){
            $product  = Db::name('Product')->field('id,pro_name,pro_price,pro_desc,pro_img,pro_url')->where([
                'pro_cat'=>$second_id,
                'is_show'=>1
            ])->paginate(12);
        }else if($second_id != 0 && (!empty($third_id)) && (!empty($first_id))){

            $product = Db::name('Product')->field('id,pro_name,pro_price,pro_desc,pro_img,pro_url')->where([
                'pro_cat'=>$second_id,
                'pro_brand'=>$third_id,
                'pro_first'=>$first_id,
                'is_show'=>1
            ])->paginate(12);
        }else if($second_id == 0 && ($first_id == 0) && ($third_id != 0)){
            $product = Db::name('Product')->field('id,pro_name,pro_price,pro_desc,pro_img,pro_url')->where([
                'pro_brand'=>$third_id,
                'is_show'=>1
            ])->paginate(12);
        }else if($second_id != 0 && ($first_id == 0) && ($third_id != 0)){
            $product = Db::name('Product')->field('id,pro_name,pro_price,pro_desc,pro_img,pro_url')->where([
                'pro_brand'=>$third_id,
                'pro_cat'=>$second_id,
                'is_show'=>1
            ])->paginate(12);
        }
        $nav = [
            ["url"=>"/decorate-mall", "name"=>"装修商城"],
            ["url"=>"/goods-list/0", "name"=>"商城列表"],
        ];


        $j = [
            'first'=>$first,
            'second'=>$second,
            'third'=>$third,
            'product'=>$product,
            'first_id'=>empty($first_id) ? 0 : $first_id,
            'second_id'=>empty($second_id) ? 0 :$second_id,
            'second_url'=>empty($first_id) ? "/goods-list/2/0" :'/goods-list/2/'.$first_id,
            'third_id'=>empty($third_id) ? 0 :$third_id,
            'third_url'=>empty($first_id) && empty($second_id) ? "/goods-list/3/0/0" : '/goods-list/3/'.$first_id.'/'.$second_id,
            'title'=>'装修商城',
            'nav'=>$nav
        ];

        return view('goods-list',$j);
    }



    public function page(){
        return view('template/pagination');
    }
}