<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use Auth;

class PermissionController extends Controller
{
    
    private static function exist($user_id, $name){
        //Check if permission is exist, Return boolean.
        $exist = false;
        $permissions = Permission::where('user_id', $user_id)->get();
        foreach($permissions as $permission){
            if(($permission->name == $name) && ($permission->access == true)){
                $exist = true;
            }
        }
        return $exist;
    }

    public function all(){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'permission_all')){
                return response()->json(Permission::select('id', 'user_id', 'name', 'access')->get());
            }
            if(Auth::user()->role >= 1){
                return response()->json(Permission::select('id', 'user_id', 'name', 'access')->where('user_id', Auth::user()->id)->get());
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function new(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'permission_new')){
                $user_id = lib::filter($request['user_id']);
                $name = lib::filter($request['name']);
                if(!empty($user_id) && !empty($name)){
                    if(!self::exist($user_id, $name)){
                        Permission::create([
                            'user_id' => $user_id,
                            'name' => $name,
                            'access' => true,
                        ]);
                        return response()->json(['status' => 200]);
                    }
                    return response()->json(['status' => 500, 'message' => 'Already exist']);
                }
                return response()->json(['status' => 500, 'message' => 'Bad credentials']);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function allow(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'permission_allow')){
                $permission_id = lib::filter($request['permission_id']);
                Permission::whereId($permission_id)->update(['access' => true]);
                return response()->json(['status' => 200]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function disallow(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'permission_disallow')){
                $permission_id = lib::filter($request['permission_id']);
                Permission::whereId($permission_id)->update(['access' => false]);
                return response()->json(['status' => 200]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function remove(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'permission_allow')){
                $permission_id = lib::filter($request['permission_id']);
                if(!empty($permission_id)){
                    if(count(Permission::whereId($permission_id)->get())){
                        Permission::whereId($permission_id)->delete();
                        return response()->json(['status' => 200]);
                    }
                    return response()->json(['status' => 404]);
                }
                return response()->json(['status' => 500, 'message' => 'Bad credential']);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
