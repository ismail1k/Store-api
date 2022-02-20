<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use Storage;
use Auth;
use Str;

class MediaController extends Controller
{
    private static function check($media_id){
        $media_id = lib::filter($media_id);
        if(count(Media::whereId($media_id)->get())){
            return true;
        }
        return false;
    }

    private static function get($media_id){
        if(self::check($media_id)){
            $media = Media::whereId($media_id)->get();
            $response = [
                'id' => $media[0]->id,
                'product_id' => $media[0]->product_id,
                'primary' => $media[0]->primary,
                'url' => Storage::url($media[0]->path),
            ];
            return $response;
        }
        return false;
    }

    public function all(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'media_all')){
                $response = [];
                foreach(Media::all() as $media){
                    array_push($response, self::get($media->id));
                }
                return response()->json($response);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function new(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'media_new')){
                $acceptable_format = ['video', 'image'];
                if(in_array(strstr($request['attachment']->getClientmimeType(), '/', true), $acceptable_format)){                
                    $media_id = [];
                    $product_id = lib::filter($request['product_id']);
                    $primary = $request['primary'] == 1 ? true : false;
                    $media = $request->file('attachment');
                    $file_name = strtolower(str_replace(':', '-', lib::time().' '.rand(000000, 111599)).'.'.$media->getClientOriginalExtension());
                    $temp = $media->move(storage_path('app\\public'), $file_name);
                    $media_id = Media::create([
                        'path' => $file_name,
                        'product_id' => $product_id,
                        'primary' => $primary,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ])->id;
                    return response()->json(['status' => 200, 'media_id' => $media_id]);
                }
                return response()->json(['status' => 500, 'message' => 'Bad format']);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function view(Request $request){
        $media_id = lib::filter($request['media_id']);
        if(self::check($media_id)){
            return response()->json(self::get($media_id));
        }
        return response()->json(['status' => 200]);
    }

    public function remove(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'media_remove')){
                $media_id = lib::filter($request['media_id']);
                if(self::check($media_id)){
                    $media = Media::whereId($media_id)->get();
                    $url = $media[0]->path;
                    $file = Storage::path('public\\'.$url);
                    @unlink($file);
                    Media::whereId($media_id)->delete();
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
