<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
class IndexController extends Controller
{
    public function index()
    {
        $data=DB::table('users')->pluck('email','name');
        return view('admin/index',$data);
    }
    public function save(Request $request){
        $config=$request->all();
        $path=public_path('/json');
        $data=['data'=>$config];
        $config=json_encode($config,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        $res=file_put_contents($path.'/hello.json',$config);
        if($res===false){
            $data['msg']='保存失败!';
        }
        return response()->json($data);
    }
}
