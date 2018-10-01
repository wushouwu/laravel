<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Role;
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            //父级角色session信息
            $row=$request->user()->role()->first();
            $row=$row?$row->ToArray():['id'=>0];
            $role=new Role();
            $parents=array_column($role->parents($row,true),'id');
            $request->session()->put('role_parents',$parents);
            return redirect('/');
        }

        return $next($request);
    }
}
