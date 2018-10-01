<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * 父级角色.
     *
     * @param  array    $row
     * @param  boolean  $self
     * @return array
     */    
    public function parents($row,$self=false){
        $parent=array();
        if(isset($row['parent'])){
            $parent=$this->where('id',$row['parent'])->get()->toArray();
            !$parent?:$parent=array_merge($parent,$this->parents($parent[0]));
        }
        !$self?:array_push($parent,$row);         
        return $parent;
    }
}
