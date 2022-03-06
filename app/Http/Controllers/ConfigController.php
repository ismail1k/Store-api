<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use Auth;

class ConfigController extends Controller
{
    private static function check($key){
        $key = lib::filter($key);
        if(Config::where('key', $key)->first()){
            return true;
        }
        return false;
    }
    
    public static function get($key){
        $response = [];
        $key = lib::filter($key);
        $select = Config::where('key', $key)->first();
        if($select){
            $response = [
                'key' => $select->key,
                'value' => $select->value,
            ];
            if(Auth::check()){
                if(Auth::user()->role >= 2){
                    $response += [
                        'created_by' => $select->created_by,
                        'updated_by' => $select->updated_by,
                    ];
                }
            }
            return $response;
        }
        return false;
    }

    public function all(){
        if(Auth::check()){
            if(Auth::user()->role >= 2){
                $all = Config::all();
                $response = [];
                if(count($all)){
                    foreach($all as $config){
                        array_push($response, [
                            'id' => $config->id,
                            'key' => $config->key,
                            'value' => $config->value,
                        ]);
                    }
                }
                return response()->json($response);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function load(){
        $response = [
            'currency' => 'MAD',
        ];
        return response()->json($response);
    }

    public function new(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'settings_new')){
                $key = lib::filter($request['key']);
                $value = lib::filter($request['value']);
                if(empty($key) || count(Config::where('key', $key)->get())){
                    return response()->json(['status' => 500]);
                }
                $id = Config::create([
                    'key' => $key,
                    'value' => $value,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ])->id;
                return response()->json(['status' => 200, 'id' => $id]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function view(Request $request){
        $key = lib::filter($request['key']);
        if(empty($key)){
            return response()->json(['status' => 500, 'message' => 'Bad credentials']);
        }
        if(self::check($key)){
            return response()->json(self::get($key));
        }
        return response()->json(['status' => 404]);
    }

    public function edit(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'settings_edit')){
                $config_id = lib::filter($request['config_id']);
                $value = lib::filter($request['value']);
                if(empty($config_id)){
                    return response()->json(['status' => 500, 'message' => 'Bad credentials']);
                }
                Config::whereId($config_id)->update([
                    'value' => $value,
                    'updated_by' => Auth::user()->id,
                ]);
                return response()->json(['status' => 200]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function remove(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'settings_remove')){
                $config_id = lib::filter($request['config_id']);
                if(empty($config_id)){
                    return response()->json(['status' => 500, 'message' => 'Bad credentials']);
                }
                if(count(Config::whereId($config_id)->get())){
                    Config::whereId($config_id)->delete();
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
