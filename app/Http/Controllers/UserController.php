<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use Auth;

class UserController extends Controller
{
    public function select(Request $request){
        $user_id = lib::filter($request['user_id']);
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'user_select')){
                $users = User::whereId($user_id)->get();
                if(count($users)){
                    $user = [
                        'admin' => $users[0]->role >= 2 ? true : false,
                        'owner' => $users[0]->role >= 3 ? true : false,
                        'id' => $users[0]->id,
                        'role' => $users[0]->role,
                        'avatar' => $users[0]->avatar,
                        'name' => $users[0]->name,
                        'email' => $users[0]->email,
                        'phone' => $users[0]->phone,
                        'permission' => Permission::select('id', 'name', 'access')->where('user_id', $users[0]->id)->get(),
                    ];
                    return response()->json($user);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function all(){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'user_all')){
                $response = [];
                foreach(User::all() as $user){
                    $admin = $user->role >= 2 ? true : false;
                    $owner = $user->role >= 3 ? true : false;
                    array_push($response, [
                        'admin' => $admin,
                        'owner' => $owner,
                        'id' => $user->id,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'permission' => Permission::select('id', 'name', 'access')->where('user_id', $user->id)->get(),
                    ]);
                }
                return response()->json($response);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function new(Request $request){
        $name = lib::filter($request['name']);
        $email = lib::filter($request['email']);
        $password = lib::filter($request['password']);
        $phone = lib::filter($request['phone']);
        $role = 1;
        if(count(User::where('email', $email)->get())){
            return response()->json(['status' => 500, 'message' => 'Email already exist!']);
        }
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone' => $phone,
            'role' => $role,
        ]);
        return response()->json(['status' => 200]);
    }

    public function edit(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'user_edit')){
                $user_id = lib::filter($request['user_id']);
                $name = lib::filter($request['name']);
                $email = lib::filter($request['email']);
                $phone = lib::filter($request['phone']);
                $role = lib::filter($request['role']);
                $role = lib::filter($request['role']);
                if(count(User::whereId($user_id)->get())){
                    User::whereId($user_id)->update([
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                    ]);
                    if(!empty($role)){
                        if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'user_edit_role') && (Auth::user()->id != $user_id)){
                            User::whereId($user_id)->update([
                                'role' => is_numeric($role) ? $role : 1,
                            ]);
                        }
                    }
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function remove(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'user_remove')){
                $user_id = lib::filter($request['user_id']);
                if(!count(User::where('id', $user_id)->get())){
                    return response()->json(['status' => 404]);
                }
                User::whereId($user_id)->delete();
                return response()->json(['status' => 200]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
