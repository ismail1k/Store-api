<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use Auth;

class ConfigController extends Controller
{
    private static function check($key){
        $key = lib::filter($key);
        if(count(Config::where('key', $key)->get())){
            return true;
        }
        return false;
    }
    
    public static function get($key){
        $key = lib::filter($key);
        $response = [];
        $select = Config::where('key', $key)->get();
        if(count($select)){
            $response = [
                'key' => $select[0]->key,
                'value' => $select[0]->value,
            ];
            if(Auth::check()){
                if(Auth::user()->role >= 3 || self::access(Auth::user()->id, 'config_view')){
                    $response += [
                        'created_by' => $select[0]->created_by,
                        'updated_by' => $select[0]->updated_by,
                    ];
                }
            }
            return $response;
        }
        return false;
    }

    public function all(){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access('config_all')){
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
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'config_new')){
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
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'config_edit')){
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
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'config_remove')){
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
