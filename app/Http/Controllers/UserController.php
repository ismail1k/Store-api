<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;
use Auth;

class UserController extends Controller
{
    public static function get($user_id){
        if($user = User::whereId($user_id)->first()){
            $permission = [];
            $menu = MenuController::get();
            foreach($menu as $key => $value){
                $permission[$key] = false;
            }
            foreach($user->getAllPermissions() as $permissions){
                $permission[$permissions->name] = true;
            }
            $response = [
                'admin' => $user->role >= 2 ? true : false,
                'owner' => $user->role >= 3 ? true : false,
                'id' => $user->id,
                'role' => $user->role,
                'active' => $user->active,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'permission' => $permission,
            ];
            return $response;
        }
        return false;
    }

    public function all(){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'customer_all')){
                $response = [];
                foreach(User::all() as $user){
                    $admin = $user->role >= 2 ? true : false;
                    $owner = $user->role >= 3 ? true : false;
                    array_push($response, [
                        'admin' => $admin,
                        'owner' => $owner,
                        'id' => $user->id,
                        'role' => $user->role,
                        'active' => $user->active,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'permission' => $user->getAllPermissions(),
                    ]);
                }
                return response()->json($response);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function select(Request $request){
        $user_id = lib::filter($request['user_id']);
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'customer_select')){
                if($user = User::whereId($user_id)->first()){
                    return response()->json(self::get($user->id));
                }
                return response()->json(['status' => 404]);
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
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'customer_edit')){
                $user_id = $request['user_id'];
                if(count(User::whereId($user_id)->get())){
                    User::whereId($user_id)->update($request->except('token', 'user_id', 'role'));
                    if(!empty($request['role'])){
                        if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'customer_edit_role') && (Auth::user()->id != $user_id)){
                            if($user_id != Auth::user()->id){
                                User::whereId($user_id)->update([
                                    'role' => is_numeric($request['role']) ? $request['role'] : 1,
                                ]);
                            }
                        } else {
                            return response()->json(['status' => 200, 'message' => 'Unauthorized to change role!']);
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

    public function ban(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'customer_ban')){
                if($user = User::whereId($request['user_id'])->first()){
                    if($user->id != Auth::user()->id){
                        User::whereId($user->id)->update(['active' => false]);
                        return response()->json(['status' => 200]);
                    }
                    return response()->json(['status' => 500]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function unban(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'customer_unban')){
                if($user = User::whereId($request['user_id'])->first()){
                    if($user->id != Auth::user()->id){
                        User::whereId($user->id)->update(['active' => true]);
                        return response()->json(['status' => 200]);
                    }
                    return response()->json(['status' => 500]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
