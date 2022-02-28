<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Auth;

class CategoryController extends Controller
{
    public function all(){
        $response = [];
        $categories = Category::select('id')->orderBy('name', 'ASC')->get();
        foreach($categories as $category){
            $category = Category::whereId($category->id)->first();
            $products = Product::where('category_id', $category->id)->get();
            array_push($response, [
                'id' => $category->id,
                'product' => count($products),
                'name' => $category->name,
            ]);
        }
        return response()->json($response);
    }

    public function new(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'category_new')){
                $category_name = lib::filter($request['category_name']);
                if(!empty($category_name)){
                    $category = Category::where('name', $category_name)->first();
                    $category_id = @$category->id;
                    if(!$category){
                        $category_id = Category::create(['name' => $category_name])->id;
                    }
                    return response()->json(['status' => 200, 'category_id' => (integer) $category_id]);
                }
                return response()->json(['status' => 400]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function edit(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'category_edit')){
                $category_id = lib::filter($request['category_id']);
                $category_name = lib::filter($request['category_name']);
                if(!empty($category_id) && !empty($category_name)){
                    Category::whereId($category_id)->update(['name'=>$category_name]);
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 400]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function remove(Request $request){
        if(Auth::check()){
            if((Auth::user()->role >= 3) || lib::access(Auth::user()->id, 'category_remove')){
                $category_id = lib::filter($request['category_id']);
                if(!empty($category_id)){
                    Category::whereId($category_id)->delete();
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 400]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
