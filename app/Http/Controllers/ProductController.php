<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Media;
use Storage;
use Auth;

class ProductController extends Controller
{
    private static function editProduct($product_id, $record){
        if(Auth::check()){
            if(self::check($product_id) && is_array($record)){
                $product = Product::whereId($product_id)->first();
                foreach($record as $key => $value){
                    Product::whereId($product_id)->update([$key => $value, 'updated_by' => Auth::user()->id]);
                }
                return true;
            }
        }
        return false;
    }

    private static function check($product_id){
        $product = Product::whereId($product_id)->get();
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'product_view')){
                if(count($product)){
                    return true;
                }
            }
        }
        if(count($product) && $product[0]->available == 1){
            return true;
        }
        return false;
    }

    public static function get($product_id){
        $product = Product::whereId($product_id)->first();
        if($product){
            $media = [
                'primary' => '',
                'image' => [],
                'video' => [],
            ];
            foreach($product->media as $file){
                $mime_type = strstr(mime_content_type(Storage::path('public/'.$file->path)), '/', true);
                if($file->primary != 1){
                    if($mime_type == 'image'){
                        array_push($media['image'], [
                            'id' => $file->id,
                            'path' => Storage::url('public/'.$file->path),
                        ]);
                    }
                    if($mime_type == 'video'){
                        array_push($media['video'], [
                            'id' => $file->id,
                            'path' => Storage::url('public/'.$file->path),
                        ]);
                    }
                    continue;
                }
                $media['primary'] = [
                    'id' => $file->id,
                    'path' => Storage::url('public/'.$file->path),
                ];
            }
            $response = [
                'id' => $product->id,
                'name' => (string) $product->name,
                'short_description' => (string) $product->short_description,
                'description' => (string) $product->description,
                'tags' => explode(',', $product->tags),
                'url' => $product->name,
                'media' => $media,
                'category' => $product->category,
                'inventory' => InventoryController::get($product->inventory_id),
                'price' => round($product->price, 2),
                'discount' => round($product->discount, 2),
            ];
            if(Auth::check()){
                if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'product_view')){
                    $response += [
                        'availability' => (boolean)$product->available,
                        'created_by' => $product->created_by,
                        'updated_by' => $product->updated_by,
                    ];
                }
            }
            return (object) $response;
        }
        return false;
    }

    public function all(Request $request){
        $response = [];
        $limit = $request['limit'] == 0?null:$request['limit'];
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'product_all')){
                foreach(Product::latest()->take($limit)->get() as $product){
                    array_push($response, self::get($product->id));
                }
                return response()->json($response);
            }
        }
        foreach(Product::latest()->where('available', '1')->take($limit)->get() as $product){
            array_push($response, self::get($product->id));
        }
        return response()->json($response);
    }

    public function new(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'product_create')){
                $product_id = Product::create([
                    'name' => lib::filter($request['name']),
                    'short_description' => $request['short_description'],
                    'description' => $request['description'],
                    'tags' => lib::filter($request['tags']),
                    'category_id' => $request['category_id']>0?$request['category_id']:null,
                    'inventory_id' => lib::filter($request['inventory_id']),
                    'price' => $request['price'],
                    'discount' => $request['discount'],
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ])->id;
                return response()->json(['status' => 200, 'product_id' => $product_id]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function view(Request $request){
        $product_id = lib::filter($request['product_id']);

        $product_name = $request['product_name'];
        if($product_name){
            $product = Product::where('name', $product_name)->first();
            if($product){
                $product_id = $product->id;
            }
        }
        
        if(self::check($product_id)){
            $product = (array) self::get($product_id);
            $product += [
                'related' => [],
            ];
            if($product['category'] == null){
                foreach(Product::latest()->get()->except($product_id)->take(8) as $related){
                    array_push($product['related'], self::get($related->id));
                }
            }else if($product['category']['id'] != 0){
                foreach(Product::whereId($product_id)->first()->category->products->except($product_id)->take(8) as $related){
                    array_push($product['related'], self::get($related->id));
                }
            }
            return response()->json($product);
        }
        return response()->json(['status' => '404']);
    }

    public function edit(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >=3 || lib::access(Auth::user()->id, 'product_edit')){
                self::editProduct($request['product_id'], $request->except(['product_id','token']));
                return response()->json(['status' => 200]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function remove(Request $request){
        $product_id = lib::filter($request['product_id']);
        if(Auth::check()){
            if(Auth::user()->role || lib::access(Auth::user()->id, 'product_remove')){
                if(self::check($product_id)){
                    Product::whereId($product_id)->delete();
                    $media = Media::where('product_id', $product_id)->get();
                    foreach($media as $m){
                        $url = $m->path;
                        $file = Storage::path('public\\'.$url);
                        @unlink($file);
                        Media::whereId($m->id)->delete();
                    }
                    return response()->json(['status' => 200]);            
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
