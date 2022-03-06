<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        $email = lib::filter($request['email']);
        $password = lib::filter($request['password']);
        if(!empty($email) && !empty($password)){
            $token = auth()->attempt([
                'email' => $email,
                'password' => $password,
            ]);
            if($token){
                $user = (object)UserController::get(auth()->user()->id);
                return response()->json([
                    'auth' => true,
                    'active' => $user->active,
                    'admin' => $user->admin,
                    'owner' => $user->owner,
                    'token' => $token,
                    'permission' => $user->permission,
                ]);
            }
        }
        return response()->json([
            'auth' => false,
            'message' => "Email or Password is not valid",
        ]);
    }

    public function logout(){
        auth()->logout();
        return response()->json(['status' => 200]);
    }

    public function me(Request $request){
        if(auth()->check()){
            if(!Auth()->user()->active){
                return response()->json(['active'=>false]);
            }
            return response()->json([
                'id' => auth()->user()->id,
                'active' => auth()->user()->active,
                'auth' => true,
                'admin' => auth()->user()->role >= 2 ? true : false,
                'owner' => auth()->user()->role >= 3 ? true : false,
                'role' => auth()->user()->role,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'permission' => auth()->user()->getAllPermissions(),
            ]);
        }
        return response()->json(['auth' => false]);
    }

    public function changepassword(Request $request){
        if(auth()->check()){
            if(auth()->user()->role >= 1){
                $current = lib::filter($request['current']);
                $new = lib::filter($request['new']);
                if(auth()->attempt(['email'=>auth()->user()->email, 'password'=>$current])){
                    User::whereId(auth()->user()->id)->update([
                        'password' => Hash::make($new),
                    ]);
                    return response()->json(['status' => 200, 'message' => 'Password changed']);
                }
                return response()->json(['status' => 500, 'message' => 'Wrong current password']);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
