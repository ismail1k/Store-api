<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Media;
use Storage;
use Auth;

class ProductController extends Controller
{
    private static function editProduct($product_id, $record){
        if(Auth::check()){
            if(self::check($product_id) && is_array($record)){
                $product = Product::whereId($product_id)->get();
                foreach($record as $key => $value){
                    $key = lib::filter($key);
                    $value = lib::filter($value);
                    if(empty($value)){
                        if(in_array($key, ['name', 'short_description', 'description', 'tags', 'category_id', 'inventory_id', 'price'])){
                            continue;
                        }
                    }
                    if($product[0]->$key != $value){
                        Product::whereId($product_id)->update([$key => $value, 'updated_by' => Auth::user()->id]);
                    }
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
        $product = Product::whereId($product_id)->get();
        if(count($product)){
            $media = [
                'primary' => '',
                'image' => [],
                'video' => [],
            ];
            foreach(Media::select('id', 'for', 'primary', 'path')->where('for', $product[0]->media_id)->get() as $file){
                $mime_type = strstr(mime_content_type(Storage::path('public\\'.$file->path)), '/', true);
                if($file->primary != 1){
                    if($mime_type == 'image'){
                        array_push($media['image'], Storage::url('public/'.$file->path));
                    }
                    if($mime_type == 'video'){
                        array_push($media['video'], Storage::url('public/'.$file->path));
                    }
                    continue;
                }
                $media['primary'] = Storage::url('public/'.$file->path);
            }
            $response = [
                'id' => $product[0]->id,
                'name' => (string) $product[0]->name,
                'short_description' => (string) $product[0]->short_description,
                'description' => (string) $product[0]->description,
                'tags' => explode(',', $product[0]->tags),
                'url' => $product[0]->name,
                'media' => $media,
                'category' => Category::select('id', 'name')->whereId($product[0]->category_id)->first(),
                'inventory' => InventoryController::get($product[0]->inventory_id),
                'price' => round($product[0]->price, 2),
                'discount' => round($product[0]->discount, 2),
                'virtual' => (boolean) $product[0]->virtual,
            ];
            if(Auth::check()){
                if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'product_view')){
                    $response += [
                        'created_by' => $product[0]->created_by,
                        'updated_by' => $product[0]->updated_by,
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
                    'short_description' => lib::filter($request['short_description']),
                    'description' => lib::filter($request['description']),
                    'tags' => lib::filter($request['tags']),
                    'media_id' => lib::filter($request['media']),
                    'category_id' => lib::filter($request['category_id']),
                    'inventory_id' => lib::filter($request['inventory_id']),
                    'price' => lib::filter($request['price']),
                    'discount' => lib::filter($request['discount']),
                    'virtual' => lib::filter($request['virtual']),
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
            $product = Product::where('name', $product_name)->get();
            if(count($product)){
                $product_id = $product[0]->id;
            }
        }
        if(self::check($product_id)){
            $product = (array) self::get($product_id);
            $product += [
                'related' => [],
            ];
            if($product['category'] == null){
                foreach(Product::latest()->take(8)->get() as $related){
                    array_push($product['related'], self::get($related->id));
                }
            }else if($product['category']['id'] != 0){
                foreach(Product::where('category_id', self::get($product_id)->category->id)->get()->except($product_id)->take(8) as $related){
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
                @self::editProduct(lib::filter($request['product_id']), [
                    'name' => lib::filter($request['name']),
                    'short_description' => lib::filter($request['short_description']),
                    'description' => lib::filter($request['description']),
                    'tags' => lib::filter($request['tags']),
                    'category_id' => lib::filter($request['category_id']),
                    'inventory_id' => lib::filter($request['inventory_id']),
                    'price' => (double) round(lib::filter($request['price']), 2),
                    'discount' => (double) round(lib::filter($request['discount']), 2),
                    'virtual' => (string) lib::filter($request['virtual']) == 1 ? '1' : '0',
                    'available' => (string) lib::filter($request['available']) == 1 ? '1' : '0',
                ]);
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
                    return response()->json(['status' => 200]);            
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
