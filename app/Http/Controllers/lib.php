<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
        $table = explode('_', $table);
        $user = User::whereId($user_id)->first();
        if($user->role != 2){
            return false;
        }
        if(!$user->active){
            return false;
        }
        if($user->hasPermissionTo($table[0])){
            return true;
        }
        return false;
    }
    
    public static function time(){
        $mytime = Carbon::now();
        return $mytime->toDateTimeString();
    }
}
