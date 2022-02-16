<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function product(Request $request){
        if($request['q']){
            $keyword = lib::filter($request['q']);
            $result = [];
            foreach(Product::select('id')->where('name', 'like', "%$keyword%")->orWhere('tags', 'like', "%$keyword%")->get() as $product){
                array_push($result, ProductController::get($product->id));
            }
            return response()->json($result);
        }
        if($request['category_id']){
            $category_id = lib::filter($request['category_id']);
            $result = [];
            foreach(Product::select('id')->where('category_id', $category_id)->get() as $product){
                array_push($result, ProductController::get($product->id));
            }
            return response()->json($result);
        }
        return response()->json(['status' => 404]);
    }
}
