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
                return response()->json([
                    'auth' => true,
                    'token' => $token,
                    'admin' => auth()->user()->role > 1?true:false,
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
            return response()->json([
                'auth' => true,
                'admin' => auth()->user()->role >= 2 ? true : false,
                'owner' => auth()->user()->role >= 3 ? true : false,
                'id' => auth()->user()->id,
                'avatar' => auth()->user()->avatar,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'permission' => Permission::select('name', 'access')->where('user_id', auth()->user()->id)->get(),
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
