<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use Auth;

class PermissionController extends Controller
{

    public function all(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'customer_all')){
                if($user = User::whereId($request['user_id'])->first()){
                    return response()->json($user->getAllPermissions());
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function allow(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'customer_allow')){
                if(Auth::user()->id != $request['user_id']){
                    if($user = User::whereId($request['user_id'])->first()){
                        if($user->role == 2){
                            @$user->givePermissionTo($request['permission_name']);
                            return response()->json(['status' => 200]);
                        }
                        return response()->json(['status' => 500]);
                    }
                    return response()->json(['status' => 404]);
                }
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function disallow(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'customer_disallow')){
                if(Auth::user()->id != $request['user_id']){
                    if($user = User::whereId($request['user_id'])->first()){
                        if($user->role == 2){
                            $user->revokePermissionTo($request['permission_name']);
                            return response()->json(['status' => 200]);
                        }
                        return response()->json(['status' => 500]);
                    }
                    return response()->json(['status' => 404]);
                }
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function update(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'customer_update')){
                if(Auth::user()->id != $request['user_id']){
                    if($user = User::whereId($request['user_id'])->first()){
                        if($user->role == 2){
                            foreach($request->except('token', 'user_id') as $key=>$value){
                                if($value == true){
                                    $user->givePermissionTo($key);
                                }
                                if($value == false){
                                    $user->revokePermissionTo($key);
                                }
                            }
                            return response()->json(['status' => 200]);
                        }
                        return response()->json(['status' => 500]);
                    }
                    return response()->json(['status' => 404]);
                }
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
