<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});
// Auth::routes();

Route::group(['prefix' => 'admin'], function () {
    //Route::post('/form/save', 'Admin\IndexController@save');
    Route::group(['prefix' => '/table'], function () {
        $actions=[
            'table',//表单数据
            'tableView',//默认表格配置
            'field',//字段信息
            'view',//获取视图
            'save',//保存表单
            'tables',//所有表
            'row',//表格一行数据
        ];
        foreach($actions as $key=>$action){
            Route::any('/'.$action, 'Admin\table@'.$action);
        }
    });
});
// Route::get('/{module?}/{view?}', function ($module='',$view = 'index') {
//     $view_route="$module.$view";
//     if(!view()->exists($view_route)){
//         $view_route='index';
//     }
//     return view($view_route);
// }); 
    