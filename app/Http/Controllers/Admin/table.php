<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
class table extends Controller{
    public function __construct(){
        $this->db=config('database.connections.mysql.database');
    }
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
                 $tables->where('TABLE_SCHEMA',$this->db);
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
                $tables->where('TABLE_SCHEMA',$this->db);
                if($TABLE_NAME=='INFORMATION_SCHEMA.COLUMNS'){
                    $row=$request->input('row');
                    $request->offsetSet('where', ['field'=>'TABLE_NAME','operator'=>'=','value'=>$row['TABLE_NAME']]);
                    $request->offsetSet('query', ['field'=>'COLUMN_NAME','operator'=>'=','value'=>$row['COLUMN_NAME']]);
                 }
            }else{
                if($request->has('id')){
                    $id=$request->input('id');
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
        $priv=$request->input('priv',true);
        if($view){
            $json=$tables = DB::table('view')->where([
                ['view_name','=',$view],
                ['type','=',$type],
                ['TABLE_NAME','=',$TABLE_NAME]
            ])->value('json');
            $json=json_decode($json);
            if($json){
                if($type=='table' && $TABLE_NAME){
                    $fields=$this->field($request)->original['data'];
                    if(count($fields)){
                        $json->table->header->fields=$fields;
                    }
                    //非超管权限控制
                    if($request->user()->role!=0 && $priv){
                        $role_parents=$request->session()->get('role_parents');
                        $button=['tools'=>$json->tools,'operator'=>$json->table->operator];
                        foreach($button as $key=>$val){
                            foreach($val as $k=>$v){
                                if(isset($v->priv)){
                                    $role=array_intersect($v->priv->role,$role_parents);
                                    $user=in_array($request->user()->id,$v->priv->user);
                                    if(!$role && !$user){
                                        unset($button[$key][$k]);
                                    }
                                }else{   
                                    unset($button[$key][$k]); 
                                }                                
                            }
                        }
                        $json->tools=$button['tools'];
                        $json->table->operator=$button['operator'];
                    }
                }
                $data=['data'=> $json];
            }else{
                $data=['msg'=>'没有视图配置'];
            }
        }else{
            if($type=='table'){
                $data=['data'=>$this->tableView($request)->original];
            }else{
                $data=['msg'=>'没有视图配置'];
            }
        }
        return response()->json($data);//->setEncodingOptions(JSON_FORCE_OBJECT);
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
                    ->where('TABLE_SCHEMA',$this->db)
                    ->where('TABLE_NAME',$TABLE_NAME)
                    ->get();
            $data=['data'=>$fields];
        }else{
            $data=['msg'=>'表名不存在'];
        }
        return response()->json($data);
    } 
    //字段处理
    public function fieldOperate(Request $request){
        $where=$request->input('where',['value'=>'']);
        $table=$where['value'];
        if (Schema::hasTable($table)) {
            Schema::table($table, function (Blueprint $table) {
                $request=$GLOBALS['request'];
                $form=$request->input('form',[]);
                $row=$request->input('row',[]);
                if($form){
                    $COLUMN_NAME=$form['COLUMN_NAME'];
                    //重命名
                    if($row && $form['COLUMN_NAME']!=$row['COLUMN_NAME']){
                        $where=$request->input('where',['value'=>'']);
                        Schema::table($where['value'], function (Blueprint $table) {
                            $request=$GLOBALS['request'];
                            $form=$request->input('form',[]);
                            $row=$request->input('row',[]);
                            $table->renameColumn($row['COLUMN_NAME'], $form['COLUMN_NAME']);
                        });                        
                    }
                    //字段类型
                    switch($form['DATA_TYPE']){
                        case 'varchar':
                            $field=$table->string($COLUMN_NAME,$form['CHARACTER_MAXIMUM_LENGTH']);
                            break;
                        case 'text':
                            $field=$table->text($COLUMN_NAME);
                            break;
                        case 'int':
                            $field=$table->integer($COLUMN_NAME);
                            break;
                        case 'longtext':
                            $field=$table->json($COLUMN_NAME);
                            break;
                        case 'datetime':
                            $field=$table->dateTime($COLUMN_NAME);
                            break;
                        case 'date':
                            $field=$table->date($COLUMN_NAME);
                            break; 
                        case 'time':
                            $field=$table->time($COLUMN_NAME);
                            break; 
                        default:
                            $request->offsetSet('res',['msg'=>_('字段类型未处理')]);
                            die();
                    }
                    //字段名--注释
                    if(!$row || $row && $form['COLUMN_COMMENT']!=$row['COLUMN_COMMENT']){
                        $res=$field->comment($form['COLUMN_COMMENT']);
                    }
                    if($row){
                        $res=$field->change();
                    }                    
                }else{
                    if($row){
                        //删除字段
                        $res=$table->dropColumn($row['COLUMN_NAME']);
                    }else{
                        $res=['msg'=>_('表单信息未提交')];
                    }
                } 
                $request->offsetSet('res',$res);
            });
            $res=$request->input('res');
            $data=['data'=>$res];
            if(isset($res['msg'])){
                $data['msg']=$res['msg'];
            }
        }else{
            $data=['msg'=>_('表名不存在')];
        }       
        return response()->json($data);

    }  
    //保存表
    public function tableSave(Request $request){
        $form=$request->input('form',['TABLE_COMMENT'=>'','TABLE_NAME'=>'']);
        $table = DB::table('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_SCHEMA',$this->db)
            ->where('TABLE_COMMENT',$form['TABLE_COMMENT'])
            ->get();
        if(count($table)){
            return response()->json(['msg'=>'表名已存在','data'=>$table]);
        }
        if (Schema::hasTable($form['TABLE_NAME'])) {
            $data=['msg'=>'别名已存在'];
        }else{
            $name=Schema::create($form['TABLE_NAME'], function (Blueprint $table) {
                $table->increments('id')->comment('id');
            });
            $comment=DB::statement("ALTER TABLE `{$form['TABLE_NAME']}` comment '{$form['TABLE_COMMENT']}' "); 
            $data=['name'=>$name,'comment'=>$comment];
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
                    "query"=> [
                        "url"=> "admin/table/addView",
                        "TABLE_NAME"=> $TABLE_NAME,
                        'view_name'=>'数据-添加'
                    ],
                    "operator"=> "elementTextarea",
                    "script"=> "this.add(event,config)",
                    "text"=> "添加"                    
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
                    'header'=>array(
                        'show'=> true,                       
                    ),
                    "operator"=> [[
                        "text"=> "查看",
                        "type"=> "elementButton",
                        "wrapper"=> "span",
                        "script"=> "this.view(row,operator)",
                        "icon"=> "el-icon-view",
                        "title"=> "查看",
                        "buttonType"=> "primary",
                        "saturation"=> "plain",
                        "shape"=> "circle",
                        "size"=> "medium",
                        "operator"=> "elementTextarea",
                        "query"=> [
                            "TABLE_NAME"=> $TABLE_NAME,
                            "url"=> "admin/table/viewView",                              
                            "view_name"=> "数据-查看"
                        ],
                        "priv"=> [
                            "role"=> [],
                            "dept"=> [],
                            "user"=> []
                        ]
                    ],[
                        "text"=> "编辑",
                        "type"=> "elementButton",
                        "wrapper"=> "span",
                        "script"=> "this.edit(row,operator)",
                        "title"=> "编辑",
                        "icon"=> " el-icon-edit-outline",
                        "buttonType"=> "success",
                        "saturation"=> "plain",
                        "shape"=> "circle",
                        "size"=> "medium",
                        "operator"=> "elementTextarea",
                        "query"=> [
                            "TABLE_NAME"=> $TABLE_NAME,
                            "url"=> "admin/table/addView",                              
                            "view_name"=> "数据-编辑"
                        ],
                        "priv"=> [
                            "role"=> [],
                            "dept"=> [],
                            "user"=> []
                        ]
                    ],[
                        "text"=> "删除",
                        "type"=> "elementButton",
                        "wrapper"=> "span",
                        "script"=> "this.delete(row,operator)",
                        "title"=> "删除",
                        "icon"=> "el-icon-delete",
                        "buttonType"=> "danger",
                        "saturation"=> "plain",
                        "shape"=> "circle",
                        "size"=> "medium",
                        "operator"=> "elementTextarea",
                        "query"=> [
                            "TABLE_NAME"=> $TABLE_NAME,
                            "url"=> "admin/table/delete"                               
                        ],
                        "priv"=> [
                            "role"=> [],
                            "dept"=> [],
                            "user"=> []
                        ]
                    ]]
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
                $json['form']['field']=$fields[0]->value;
                $json['table']['header']['fields']=$fields;
                $json['table']['header']['column']=$fields;
                $json['table']['defaultSort']=array('prop'=>$fields[0]->value,'sort'=>'ascending');
            }
            $data=$json;
        }else{
            $data=['msg'=>'表名不存在'];
        }
        return response()->json($data);
    }
    //添加编辑视图的默认配置
    public function addView(Request $request){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        if($TABLE_NAME){
            $fields=$this->field($request)->original['data'];
            $data=['data'=>[
                'fields'=>[],
                "tools"=> [[
                    "type"=> "elementButton",
                    "text"=> "取消",
                    "buttonType"=> "",
                    "name"=> "button",
                    "title"=> "取消",
                    "operator"=> "elementSelect",
                    "query"=> [],
                    "script"=> "this.cancel(event,config);"
                ],[
                    "type"=> "elementButton",
                    "text"=> "保存并关闭",
                    "buttonType"=> "primary",
                    "name"=> "button",
                    "title"=> "保存并关闭",
                    "operator"=> "elementSelect",
                    "query"=> [
                        "url"=> "admin/table/save"
                    ],
                    "script"=> "this.save(event,config,true);"
                ]]
            ]];
            foreach($fields as $key=>$val){
                if($val->name=='id'){
                    continue;
                }
                $data['data']['fields'][$key]=[
                    "type"=> "elementText",
                    "label"=> $val->label,
                    "name"=> $val->name,
                    "rules"=> [
                        [
                            "required"=> false,
                            "trigger"=> [
                                "blur"
                            ],
                            "message"=> "字段不能为空"
                        ],
                        [
                            "type"=> "",
                            "trigger"=> [],
                            "message"=> ""
                        ],
                        [
                            "custom"=> "pattern",
                            "trigger"=> [],
                            "message"=> ""
                        ]
                    ]                    
                ];
            }
        }else{
            $data=['msg'=>'表名不存在'];
        }
        return response()->json($data);
    } 
    //查看视图的默认配置 
    public function viewView(Request $request){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        if($TABLE_NAME){
            $fields=$this->field($request)->original['data'];
            $data=['data'=>[
                'fields'=>[],
                "tools"=> [[
                    "type"=> "elementButton",
                    "text"=> "编辑",
                    "buttonType"=> "primary",
                    "name"=> "button",
                    "title"=> "编辑",
                    "operator"=> "elementSelect",                    
                    "query"=> [
                        "url"=> "admin/table/addView",
                        "TABLE_NAME"=>$TABLE_NAME,
                        "view_name"=>"数据-编辑"
                    ],
                    "script"=> "this.edit(event,config);"
                ]]
            ]];
            foreach($fields as $key=>$val){
                $data['data']['fields'][$key]=[
                    "type"=> "elementText",
                    "label"=> $val->label,
                    "name"=> $val->name,
                    "readonly"=>true                    
                ];
            }
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
    //删除记录
    public function delete(Request $request){
        $TABLE_NAME=$request->input('TABLE_NAME','');
        $form=$request->input('row',[]);
        if($TABLE_NAME&&$form){
            if(isset($form['id']) && $form['id']){
                //更新
                $id=$form['id'];
                $table=DB::table($TABLE_NAME)->where('id',$id)->delete();
                $data=['data'=>$table];
            }else{
                $data=['msg'=>_('id不存在')];
            }
             $data=['data'=>$table];
        }else{
            $data=['msg'=>$TABLE_NAME?_('表单信息未提交'):_('表名不存在')];
        }
        return response()->json($data);
    }    
}
