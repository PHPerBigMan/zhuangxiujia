<?php
namespace app\index\controller;

use think\Db;

class Web
{

    /**
     * @return \think\response\Json
     * 首页banner
     */

   public function banner(){
       $data = Db::name('Banner')->where(['type'=>1])->select();

       return json($data);
   }


    /**
     * @return \think\response\Json
     * author  hongwenyang
     * method description : 网站公告
     */

    public function web_notice(){
        $data = Db::name('WebNotice')->field('id,title')->order('create_time desc')->limit(7)->select();

        return json($data);
    }


    /**
     *
     * @return \think\response\Json
     * author  hongwenyang
     * method description : 资料数据
     */

    public function find(){
        $data = Db::name('ZlCat')->field('id,cat_name')->select();
        foreach($data as  $k=>$v){
            $data[$k]['main'] = Db::name('Zl')->where(['zl_cat'=>$v['id']])->field('id,zl_title,zl_pic')->where(['zl_type'=>0])->limit(6)->select();
            $data[$k]['cat_name'] = str_split($data[$k]['cat_name'],6);
            foreach($data[$k]['main'] as $k1=>$v1){
                $a[$k][$k1] = json_decode($v1['zl_pic'],true);
                if($a[$k][$k1] != NULL){
                    $data[$k]['main'][$k1]['zl_pic'] = $a[$k][$k1][0];
                }
            }
        }
       return json($data);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 首页最新优惠
     */

    public function youhui(){
        $data = Db::name('Youhui')->field('id,title')->where(['is_show'=>1])->order('create_time desc')->limit(7)->select();

        return json($data);
    }


    /**
     *
     * @return \think\response\Json
     * author hongwenyang
     * method description : 首页热点
     */

    public function remen(){
        $data = Db::name('Hot')->field('id,title')->where(['is_show'=>1])->order('create_time desc')->select();

        return json($data);
    }

    /**
     *
     * @return \think\response\Json
     * author hongwenyang
     * method description : 首页省份
     */

    public function province(){
        $data = Db::name('HatProvince')->select();

        return json($data);
    }

    /**
     *
     * @return \think\response\Json
     * author hongwenyang
     * method description : 首页城市
     */

    public function city(){
        $father= input('father');
        $data = Db::name('HatCity')->where(['father'=>$father])->select();

        return json($data);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 首页地区
     */

    public function area(){
        $father = input('father');

        $data = Db::name('HatArea')->where(['father'=>$father])->select();

        foreach($data as $k=>$v){
//            $data[$k]['area'] = mb_substr($data[$k]['area'],0,4);
        }
        return json($data);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 首页广告位
     */

    public function ad(){
        $data = Db::name('Ad')->where(['is_show'=>1])->field('url,href')->limit(2)->select();

        return json($data);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 首页案例
     */

    public function index_anli(){
        $data = Db::name('UserAnli')->where(['jidian'=>1])->field('id,pic')->limit(3)->select();

        foreach($data as $k=>$v){
            $img = json_decode($v['pic'],true);
            if($img != NULL){
                $data[$k]['pic'] = $img[0];
            }

        }

        return json($data);
    }

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 案例分类
     */

    /*public function anli_cat(){
        $fir = Db::name('AnliCat')->where(['is_show'=>1,'level'=>1])->field('id,cat_name')->select();
        foreach ($fir as $k=>$v){
            $data[$k]['cat_name'] = $v['cat_name'];
            $data[$k]['sec'] = Db::name('AnliCat')->where(['p_id'=>$v['id']])->field('id,cat_name')->select();
        }

        return json($data);
    }*/

    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 首页二级导航  后期可能要改
     */

    public function nav(){
        $data = Db::name('ZlCat')->field('id,cat_name')->select();

        return json($data);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 申请列表
     */

    public function sqList(){
        $data = Db::name('Zx')->limit(5)->select();
        foreach($data as $k=>$v){
            $data[$k]['user_name'] = mb_substr(Db::name('User')->where(['id'=>$v['user_id']])->value('user_name'),0,1)."**";
            $data[$k]['create_time'] = date("Y/m/d",$v['create_time']);
        }

        return json($data);
    }


    public function anli_cat_read(){
        echo input('id');
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description :  我要装修表单提交
     */

    public function sq(){

        $data = input('post.');
        if(empty(session('web_user'))){
            $msg['code'] = 404;
            $msg['msg']  = "请先登录";
        }else{
            $data['user_id'] = session('web_user');
            $data['create_time'] = time();
            Db::name('Zx')->insert($data);
            $msg['code'] = 200;
            $msg['msg']  = "申请成功,客服人员会及时和您联系";
        }

        return json($msg);
    }


    public function web(){
        $data = Db::name('Intro')->where(['type'=>1])->select();


        return json_encode($data);
    }


    /**
     * @return string
     * author hongwenyang
     * method description : 免费招标等数据
     */
    public function free(){
        $type = input('type');
        $data = Db::name('Intro')->whereOr("type","3")->field('title,img,id')->select();

        $index  = 1;
        foreach($data as $k=>$v) {
            $data[$k]['href'] = "#index-bottom-tabs-" . $index;
            $data[$k]['img_id'] = "index-bottom-tabs-" . $index;
            if (empty($type)) {
                if ($k == 0) {
                    $data[$k]['class'] = 'active';
                    $data[$k]['img_class'] = 'tab-pane active';
                } else {
                    $data[$k]['class'] = '';
                    $data[$k]['img_class'] = 'tab-pane';
                }
            } else {
                if ($v['id'] == $type) {
                    $data[$k]['class'] = 'active';
                    $data[$k]['img_class'] = 'tab-pane active';
                } else {
                    $data[$k]['class'] = '';
                    $data[$k]['img_class'] = 'tab-pane';
                }
            }
            $index++;
        }
        return json_encode($data);
    }



    public function isLogin(){
        $sign['code'] = 1;
        $sign['user_name'] = "";
        if(!empty(session('web_user'))){
            $sign['code'] = 0;
            $sign['user_name'] = Db::name('User')->where(['id'=>session('web_user')])->value('user_name');
        }

        return json_encode($sign);
    }


    /**
     * @return string
     * author hongwenyang
     * method description : 案例列表
     */

    public function anli_data()
    {
        $id = input('post.');


        if(!empty($id['page'])){
            $page = $id['page'];
            unset($id['page']);
            unset($id['name']);
            unset($id['say']);

            $count = Db::name('UserAnli')->where($id)->where(['is_pass'=>1])->count();

            if($page == 1){
                $limit = '0'.','.'4';
            }else{
                $limit = (($page-1)*4).','.'4';

            }
            $anli = Db::name('UserAnli')->where($id)->where(['is_pass'=>1])->limit($limit)->select();

        }else{
            $count = Db::name('UserAnli')->where($id)->where(['is_pass'=>1])->count();
            $anli = Db::name('UserAnli')->where($id)->where(['is_pass'=>1])->limit(4)->select();
        }
        $pagecount = ceil($count / 4);

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
        $data['pagecount'] = $pagecount;
        $data['anli'] = $anli;
        return json_encode($data);
    }


    /**
     * author hongwenyang
     * method description : 装修商城-商城列表
     */

    public function product(){

        $id     = input('id');
        $type   = input('type');
        switch ($type){
            case 0:
                    $product = Db::name('Product')->paginate(12);

                break;
            case 1:
                $map['pro_cat'] = $id;
                break;
            default:
                $map['pro_brand'] = $id;
        }

        return json_encode($product);
    }


    /**
     * @return string
     * author hongwenyang
     * method description : 首页顶部免费申请
     */

    public function index_free(){
        $data = Db::name('Intro')->where(['type'=>2])->select();
        foreach($data as $k=>$v){
            $data[$k]['new_title'][0] = mb_substr($v['title'],0,2);
            $data[$k]['new_title'][1] = mb_substr($v['title'],2,4);
        }

        return json_encode($data);
    }

    public function yhxy(){
        $data = Db::name('Intro')->where(['type'=>4])->find();

        return json_encode($data);
    }

    /**
     * @return string
     * author hongwenyang
     * method description : 个人中心 用户编辑  头像预览
     */

    public function head_url(){
        if ((request()->file('file')) != NULL) {
            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            $img = '/uploads/' . $info->getSaveName();

        }

        session('user_pic',$img);
        $msg['url'] = $img;
        return json_encode($msg);
    }

    /**
     * @return string
     * author hongwenyang
     * method description :保存用户信息
     */

    public function user_edit(){
        $data = input('post.');
        $data['user_area'] = Db::name('HatProvince')->where(['provinceID'=>$data['user_area']])->value('province');
        $data['user_city'] = Db::name('HatCity')->where(['cityID'=>$data['user_city']])->value('city');


        if(!empty(session('user_pic'))){
            $data['user_pic'] = session('user_pic');
        }
        $s = Db::name('User')->where(['id'=>session('web_user')])->update($data);

        if($s){
            $msg['code'] = 200;
            $msg['msg']  = '修改成功';
            session('user_pic',' ');
        }

        return json($msg);
    }


    /**
     * @return \think\response\Json
     * author hongwenyang
     * method description : 保存首页免费申请信息
     */

   public function free_data(){
        $data = input('post.');
       $t = time();
       $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
       $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
       $re=Db::name('shenqing')->where(['ip'=>$data['ip']])->where('create_time','between',"$start,$end")->count();
       $degree=Db::name("user")->find();

       if ($re<$degree['degree'])
       {
           $province = Db::name('HatProvince')->where(['provinceID' => $data['free_province']])->value('province');
           $city = Db::name('HatCity')->where(['cityID' => $data['free_city']])->value('city');
           $area = Db::name('HatArea')->where(['areaID' => $data['free_area']])->value('area');

           $data['free_address'] = $province . $city . $area . $data['free_address'];
           unset($data['free_province']);
           unset($data['free_city']);
           unset($data['free_area']);
           $data['create_time'] = time();
           $s = Db::name('Shenqing')->insert($data);

           if ($s) {
               $msg['code'] = 200;
               $msg['msg'] = '提交成功';
           }
       }else{
           $msg['code'] = 10;
           $msg['msg'] = '您的免费申请次数已经用完了';
       }

       return json($msg);

       }


}
