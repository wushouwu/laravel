<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class table extends Controller{
    //条件搜索
    public function query(Request $request,$tables,$queryName){
        if($request->has($queryName)){
            $query=$request->input($queryName);
            switch($query['operator']){
                case 'like':
                case 'not like':
                    $tables->where($query['field'],$query['operator'],'%'.$query['value'].'%');
                    break;
                case 'between':
                    $tables->whereBetween($query['field'],$query['value']);
                    break;
                case 'not between':
                    $tables->whereNotBetween($query['field'],$query['value']);
                    break;
                default:
                    $tables->where($query['field'],$query['operator'],$query['value']);
            }
            
        }
    }
    //获取相应表分页数据
    public function table(Request $request,$callback=''){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        if($TABLE_NAME){
            $fields=$request->input('fields','*');
            $tables = DB::table($TABLE_NAME)->select(DB::raw($fields));
            if($TABLE_NAME=='INFORMATION_SCHEMA.TABLES'||$TABLE_NAME=='INFORMATION_SCHEMA.COLUMNS'){
                 $tables->where('TABLE_SCHEMA',env('DB_DATABASE'));
            }  
            $this->query($request,$tables,'where');     
            $this->query($request,$tables,'query');
            $total=$tables->count();
            if($request->has('pageSize')){
                $pageSize=$request->input('pageSize');
                $currentPage=$request->input('currentPage',1);            
                $page=$tables->offset(($currentPage-1)*$pageSize)->limit($pageSize);
            }else{
                $page=$tables;
            }
            if(is_callable($callback)){
                call_user_func_array($callback,array($page));
            }
            $data=['data'=>$page->get(),'total'=>$total];
        }else{
            $data=['msg'=>'表名不存在'];
        }
        return response()->json($data);
    }
    //获取表格一行数据
    public function row(Request $request,$callback=''){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        if($TABLE_NAME){
            $fields=$request->input('fields','*');
            $tables = DB::table($TABLE_NAME)->select(DB::raw($fields));
            if($TABLE_NAME=='INFORMATION_SCHEMA.TABLES'||$TABLE_NAME=='INFORMATION_SCHEMA.COLUMNS'){
                $tables->where('TABLE_SCHEMA',env('DB_DATABASE'));
                if($TABLE_NAME=='INFORMATION_SCHEMA.COLUMNS'){
                    $row=$request->input('row');
                    $request->offsetSet('where', ['field'=>'TABLE_NAME','operator'=>'=','value'=>$row['TABLE_NAME']]);
                    $request->offsetSet('query', ['field'=>'COLUMN_NAME','operator'=>'=','value'=>$row['COLUMN_NAME']]);
                 }
            }else{
                if($request->has('id') && $id=$request->input('id')){
                    $request->offsetSet('where', ['field'=>'id','operator'=>'=','value'=>$id]);
                }
            }
            $this->query($request,$tables,'where');     
            $this->query($request,$tables,'query');
            if(is_callable($callback)){
                call_user_func_array($callback,array($tables));
            }
            $data=['data'=>$tables->first()];
        }else{
            $data=['msg'=>'表名不存在'];
        }
        return response()->json($data);
    }    
    //DB::connection()->enableQueryLog();
    //dd(DB::getQueryLog());
    //获取视图配置
    public function view(Request $request){  
        $view=$request->input('view_name','');
        $type=$request->input('type','');
        $TABLE_NAME=$request->input('TABLE_NAME','');
        if($view){
            $tables = DB::table('view')->where([
                ['view_name','=',$view],
                ['type','=',$type],
                ['TABLE_NAME','=',$TABLE_NAME]
            ])->value('json');
            $data=$tables?['data'=> json_decode($tables,true)]:['msg'=>'没有视图配置'];
        }else{
            if($type=='table'){
                $data=['data'=>$this->tableView($request)->original];
            }else{
                $data=['msg'=>'没有视图配置'];
            }
        }
        return response()->json($data);
    }  
    //所有表
    public function tables(Request $request){
        $request->offsetSet('TABLE_NAME', 'INFORMATION_SCHEMA.TABLES');
        $request->offsetSet('fields', 'TABLE_NAME AS value,TABLE_COMMENT AS label,"" AS children');
        $request->offsetSet('pageSize', '999');
        $tables=$this->table($request)->original['data'];
        foreach($tables as $key=>$val){
            $val->children=[];
        }
        return response()->json(['data'=>$tables]);
    }   
    //获取相应表字段数据
    public function field(Request $request){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        if($TABLE_NAME){
            $db=env('DB_DATABASE');
            $fields = DB::table('INFORMATION_SCHEMA.COLUMNS')
                    ->select(DB::raw(
                        '`COLUMN_NAME` value,
                        `COLUMN_NAME` name,
                        `COLUMN_COMMENT` label,
                        ifnull("",`CHARACTER_MAXIMUM_LENGTH`) `max`,
                        false `fixed`,
                        true `resizable`,
                        true `sortable`,
                        true `showOverflowTooltip`,
                        case `DATA_TYPE` 
                            WHEN "int" THEN "inputNumber" 
                            WHEN "varchar" THEN "text" 
                            WHEN "text" THEN "textarea"
                            WHEN "longtext" THEN "textarea"
                            ELSE `DATA_TYPE`
                        end `type`'
                    ))
                    ->where('TABLE_SCHEMA',$db)
                    ->where('TABLE_NAME',$TABLE_NAME)
                    ->get();
            $data=['data'=>$fields];
        }else{
            $data=['msg'=>'表名不存在'];
        }
        return response()->json($data);
    }    
    //获取表格模板默认配置
    public function tableView(Request $request){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        if($TABLE_NAME){
            $json=array (
                'tools' => 
                array (
                    0 => 
                    array (
                    'name' => 'add',
                    'buttonType' => 'primary',
                    'type' => 'elementButton',
                    'icon' => 'el-icon-plus',
                    ),
                    1 => 
                    array (
                    'name' => 'import',
                    'type' => 'elementButton',
                    'icon' => 'el-icon-upload2',
                    ),
                    2 => 
                    array (
                    'name' => 'export',
                    'type' => 'elementButton',
                    'icon' => 'el-icon-download',
                    ),
                ),
                'form' => 
                array (
                    'field' => 'TABLE_COMMENT',
                    'operator' => 'like',
                    'value' => '',
                ),
                'table' => 
                array (
                    'border' => true,
                    'stripe' => true,
                    'defaultSort' => 
                    array (
                    'prop' => 'TABLE_COMMENT',
                    'order' => 'ascending',
                    ),
                    'defaultSearch' => 
                    array (
                    'value' => 'TABLE_COMMENT',
                    'label' => '表名',
                    'type' => 'datetime',
                    'sortable' => true,
                    'fixed' => true,
                    'resizable' => true,
                    ),
                    'fields' => 
                    array (
                    0 => 
                    array (
                        'value' => 'TABLE_COMMENT',
                        'label' => '表名',
                        'type' => 'datetime',
                        'sortable' => true,
                        'fixed' => true,
                        'resizable' => true,
                    ),
                    1 => 
                    array (
                        'value' => 'TABLE_NAME',
                        'label' => '别名',
                        'type' => 'inputNumber',
                        'sortable' => true,
                        'fixed' => false,
                        'resizable' => true,
                    ),
                    2 => 
                    array (
                        'value' => 'desc',
                        'label' => '备注',
                        'fixed' => false,
                        'resizable' => false,
                        'width' => '400',
                    ),
                    ),
                    'operator' => 
                    array (
                    0 => 
                    array (
                        'text' => '查看',
                        'query' => 
                        array (
                        'view_name' => 'hello',
                        ),
                        'script' => 'this.view(row,operator)',
                    ),
                    1 => 
                    array (
                        'text' => '编辑',
                        'script' => 'this.edit(row,operator)',
                    ),
                    2 => 
                    array (
                        'text' => '删除',
                        'script' => 'this.delete(row,operator)',
                    ),
                    ),
                ),
                'pagination' => 
                array (
                    'currentPage' => 1,
                    'pageSize' => 10,
                ),
            );
            $fields=$this->field($request)->original['data']; 
            $count=count($fields);
            if($count){
                $fields[0]->fixed=true;
                $fields[0]->width=60;
                $fields[$count-1]->resizable=false;
                $json['table']['defaultSearch']=$fields[0];
                $json['form']['field']=$fields[0]->value;
                $json['table']['fields']=$fields;
                $json['table']['defaultSort']=array('prop'=>$fields[0]->value,'sort'=>'ascending');
            }
            $data=$json;
        }else{
            $data=['msg'=>'表名不存在'];
        }
        return response()->json($data);
    }
    //保存表单
    public function save(Request $request){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        $form=$request->input('form',[]);
        if($TABLE_NAME&&$form){
            //字段处理
            /*if(isset($form['json'])){
                $json=json_decode($form['json'],true);
                if($json){
                    $form['json']=json_encode($json,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
                }
            }*/
            if(isset($form['id']) && $form['id']){
                //更新
                $id=$form['id'];
                unset($form['id']);
                $table=DB::table($TABLE_NAME)->where('id',$id)->update($form);
            }else{
                //新增
                $table=DB::table($TABLE_NAME)->insertGetId($form);
            }
             $data=['data'=>$table];
        }else{

            $data=['msg'=>$TABLE_NAME?'表单信息未提交':'表名不存在'];
        }
        return response()->json($data);

    }
    //添加视图的默认配置
    /*public function addView(Request $request){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        if($TABLE_NAME){
            $fields=$this->field($request)->original['fields'];
            $data=['data'=>[
                'fields'=>$fields,
                'tools'=>[
                ]
            ]];
        }else{
            $data=['msg'=>'表名不存在'];
        }
        return response()->json($data);
    }*/
}
