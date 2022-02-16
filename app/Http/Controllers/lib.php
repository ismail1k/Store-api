<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use Carbon\Carbon;

class lib extends Controller
{
    public static function filter($string){
        $string = trim($string);
        $string = str_replace("'", "\'", $string);
        $string = str_replace('"', '\"', $string);
        $string = str_replace('`', '\`', $string);
        return $string;
    }

    public static function access($user_id, $table){
        $permission = false;
        $user_id = lib::filter($user_id);
        $table = lib::filter($table);
        $perms = Permission::where('user_id', $user_id)->get();
        foreach($perms as $perm){
            if($perm->name == $table){
                if($perm->access == true){
                    $permission = true;
                }
            }
        }
        return $permission;
    }
    
    public static function time(){
        $mytime = Carbon::now();
        return $mytime->toDateTimeString();
    }
}
