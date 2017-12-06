<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::get('/', 'index/index/index');
Route::get('/login', 'index/index/login');
Route::get('/register', 'index/index/register');
Route::get('/forget', 'index/index/forget');
Route::get('/list/:type', 'index/index/Ilist');
Route::get('/detail/:type/[:id]', 'index/index/detail');
Route::get('/free', 'index/index/free');
Route::get('/user', 'index/index/user');
Route::get('/user/edit', 'index/index/userEdit');
Route::get('/user/get', 'index/index/userTask');
Route::get('/user/post', 'index/index/userTask');

Route::get('/index/web/free/[:type]', 'index/web/free');
Route::get('/want-decorate/[:type]', 'index/index/wantDecorate');
Route::get('/decorate-train/:id/[:keyword]', 'index/index/decorateTrain');

Route::any('/decorate-case/[:cat1]/[:cat2]', 'index/index/decorateCase');


Route::get('/case-detail', 'index/index/caseDetail');
Route::get('/decorate-mall', 'index/index/decorateMall');
Route::get('/goods-list/:type/[:first]/[:second]/[:third]/', 'index/index/goodsList');
Route::get('/goods-read/', 'index/web/product');
Route::post('/sq', 'index/web/sq');
//分类id
Route::rule('acat/:id','Web/anli_cat_read','GET');
//资料详情
Route::rule('zl_read/:id','Web/zl_read','GET');
//首页公告
Route::rule('notice_read/:id','Web/notice_read','GET');
//优惠
Route::rule('youhui_read/:id','Web/youhui_read','GET');
//首页热门
Route::rule('hot/:id','Web/hot','GET');

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];




