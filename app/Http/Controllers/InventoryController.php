<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sku;
use Auth;
use DB;

class InventoryController extends Controller
{
    private static function check($inventory_id){
        if(Inventory::whereId(lib::filter($inventory_id))->first()){
            return true;
        }
        return false;
    }

    private static function checkvalue($inventory_id, $value){
        $inventory_id = lib::filter($inventory_id);
        $value = lib::filter($value);
        if(self::check($inventory_id)){
            $sku = Sku::where('inventory_id', $inventory_id)->where('value', $value)->first();
            if($sku){
                return true;
            }
        }
        return false;
    }

    public static function get($inventory_id){
        if(self::check($inventory_id)){
            $inventory = Inventory::whereId($inventory_id)->first();
            $quantity = $inventory->quantity;
            if($inventory->digital){
                $quantity = 0;
                foreach($inventory->items as $item){
                    if($item->valid == 1){
                        $quantity++;
                    }
                }
            }
            $inv = [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'quantity' => $quantity,
                'digital' => $inventory->digital,
            ];
            if(Auth::check()){
                if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'inventory_select')){
                    $inv += ['product' => count(Product::where('inventory_id', $inv['id'])->get())];
                    if($inventory->digital){
                        $inv += ['items' => $inventory->items];
                    }
                }
            }
            return $inv;
        }
        return false;
    }

    public function all(Request $request){
        $response = [];
        foreach(Inventory::all() as $inventory){
            array_push($response, self::get($inventory->id));
        }
        return response()->json($response);
    }

    public function view(Request $request){
        $inventory_id = lib::filter($request['inventory_id']);
        if(self::check($inventory_id)){
            return response()->json(self::get($inventory_id));
        }
        return response()->json(['status' => 404]);
    }

    public function new(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'inventory_new')){
                $name = lib::filter($request['name']);
                if(!Inventory::where('name', $name)->first()){
                    $digital = $request['type'] == 2?1:0;
                    $inventory_id = Inventory::create([
                        'name' => $name,
                        'digital' => $digital,
                        'quantity' => 0,
                    ])->id;
                    return response()->json(['status' => 200, 'inventory_id' => (integer) $inventory_id]);
                }
                return response()->json(['status' => 500, 'message' => 'Already exist']);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function edit(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'inventory_edit')){
                $inventory_id = $request['inventory_id'];
                $new_name = $request['new_name'];
                if(self::check($inventory_id)){
                    if(count(Inventory::where('name', $new_name)->get()) || empty($new_name)){
                        return response()->json(['status' => 500, 'message' => 'Bad name']);    
                    }
                    Inventory::whereId($inventory_id)->update([
                        'name' => $new_name,
                    ]);
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function increment(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'inventory_increment')){
                $inventory_id = lib::filter($request['inventory_id']);
                $value = lib::filter($request['value']);
                $quantity = lib::filter($request['quantity']);
                if(empty($inventory_id)){
                    return response()->json(['status' => 500]);
                }
                $inventory = Inventory::whereId($inventory_id)->first();
                if($inventory){
                    if(!$inventory->digital){
                        Inventory::whereId($inventory_id)->update([
                            'quantity' => $quantity,
                        ]);
                    }
                    if($inventory->digital){
                        if(empty($value)){
                            return response()->json(['status' => 500]);

                        }
                        if(self::checkValue($inventory_id, $value)){
                            return response()->json(['status' => 500, 'message' => 'Already exist']);
                        }
                        Sku::create([
                            'inventory_id' => $inventory_id,
                            'value' => $value,
                            'valid' => true,
                        ]);
                    }
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }

    public function descrement(Request $request){
        if(Auth::check()){
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'inventory_descrement')){
                $sku_id = lib::filter($request['sku_id']);
                $sku = Sku::whereId($sku_id)->first();
                if($sku){
                    Sku::whereId($sku_id)->delete();
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
            if(Auth::user()->role >= 3 || lib::access(Auth::user()->id, 'inventory_remove')){
                $inventory_id = lib::filter($request['inventory_id']);
                if(self::check($inventory_id)){
                    Sku::where('inventory_id', $inventory_id)->delete();
                    Inventory::whereId($inventory_id)->delete();
                    return response()->json(['status' => 200]);
                }
                return response()->json(['status' => 404]);
            }
            return response()->json(['status' => 403]);
        }
        return response()->json(['status' => 401]);
    }
}
