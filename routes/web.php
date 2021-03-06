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
Auth::routes();
Route::get('/', function () {       
    return view('index');
})->middleware('auth');

//Route::get('/home', 'HomeController@index');

Route::group(['prefix' => 'admin'], function () {
    Route::group(['prefix' => '/table'], function () {
        $actions=[
            'table',//表单数据
            'tableView',//默认表格配置
            'addView',//默认添加、编辑配置
            'viewView',//默认查看配置
            'delete',//删除记录
            'field',//字段信息
            'view',//获取视图
            'save',//保存表单
            'tables',//所有表
            'row',//表格一行数据
            'fieldOperate',//字段操作
            'tableSave'//保存表格
        ];
        foreach($actions as $key=>$action){
            Route::any('/'.$action, 'Admin\table@'.$action);
        }
    });
});
    