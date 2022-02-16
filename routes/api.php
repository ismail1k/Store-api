<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix'=>'search', 'as'=>'search'], function(){
    Route::get('/product', [App\Http\Controllers\SearchController::class, 'product'])->name('product');
});
Route::group(['prefix'=>'contact', 'as'=>'contact'], function(){
    Route::post('/send', [App\Http\Controllers\ContactController::class, 'send'])->name('send');
});
Route::group(['prefix'=>'auth'], function(){
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::get('/me', [App\Http\Controllers\AuthController::class, 'me'])->name('me');
    Route::post('/changepassword', [App\Http\Controllers\AuthController::class, 'changepassword'])->name('changepassword');
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
});
Route::group(['prefix'=>'config', 'as'=>'config'], function(){
    Route::get('/', [App\Http\Controllers\ConfigController::class, 'all'])->name('all');
    Route::get('/load', [App\Http\Controllers\ConfigController::class, 'load'])->name('load');
    Route::post('/new', [App\Http\Controllers\ConfigController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\ConfigController::class, 'edit'])->name('edit');
    Route::get('/view', [App\Http\Controllers\ConfigController::class, 'view'])->name('view');
    Route::get('/remove', [App\Http\Controllers\ConfigController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'user', 'as'=>'user'], function(){
    Route::get('/', [App\Http\Controllers\UserController::class, 'select'])->name('select');
    Route::get('/all', [App\Http\Controllers\UserController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\UserController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
    Route::get('/remove', [App\Http\Controllers\UserController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'category', 'as'=>'category'], function(){
    Route::get('/', [App\Http\Controllers\CategoryController::class, 'all'])->name('all');
    Route::post('/edit', [App\Http\Controllers\CategoryController::class, 'edit'])->name('edit');
    Route::post('/remove', [App\Http\Controllers\CategoryController::class, 'remove'])->name('remove');
    Route::post('/new', [App\Http\Controllers\CategoryController::class, 'new'])->name('new');
});
Route::group(['prefix'=>'inventory', 'as'=>'invetory'], function(){
    Route::get('/', [App\Http\Controllers\InventoryController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\InventoryController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\InventoryController::class, 'edit'])->name('edit');
    Route::get('/view', [App\Http\Controllers\InventoryController::class, 'view'])->name('view');
    Route::post('/increment', [App\Http\Controllers\InventoryController::class, 'increment'])->name('increment');
    Route::post('/descrement', [App\Http\Controllers\InventoryController::class, 'descrement'])->name('descrement');
    Route::get('/remove', [App\Http\Controllers\InventoryController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'permission', 'as'=>'permission'], function(){
    Route::get('/', [App\Http\Controllers\PermissionController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\PermissionController::class, 'new'])->name('new');
    Route::get('/remove', [App\Http\Controllers\PermissionController::class, 'remove'])->name('remove');
    Route::get('/allow', [App\Http\Controllers\PermissionController::class, 'allow'])->name('allow');
    Route::get('/disallow', [App\Http\Controllers\PermissionController::class, 'disallow'])->name('disallow');
});
Route::group(['prefix'=>'media', 'as'=>'media'], function(){
    Route::get('/', [App\Http\Controllers\MediaController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\MediaController::class, 'new'])->name('new');
    Route::get('/view', [App\Http\Controllers\MediaController::class, 'view'])->name('view');
    Route::get('/remove', [App\Http\Controllers\MediaController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'product', 'as'=>'product'], function(){
    Route::get('/', [App\Http\Controllers\ProductController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\ProductController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('edit');
    Route::get('/view', [App\Http\Controllers\ProductController::class, 'view'])->name('view');
    Route::get('/remove', [App\Http\Controllers\ProductController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'cart', 'as'=>'cart'], function(){
    Route::get('/', [App\Http\Controllers\CartController::class, 'all'])->name('all');
    Route::post('/create', [App\Http\Controllers\CartController::class, 'create'])->name('create');
    Route::post('/addToCart', [App\Http\Controllers\CartController::class, 'addToCart'])->name('addToCart');
    Route::post('/removeFromCart', [App\Http\Controllers\CartController::class, 'removeFromCart'])->name('removeFromCart');
    Route::get('/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('clear');
});
Route::group(['prefix'=>'data', 'as'=>'data'], function(){
    Route::get('/menu', [App\Http\Controllers\DataController::class, 'menu'])->name('menu');
});
// Route::group(['prefix'=>'checkout', 'as'=>'checkout'], function(){
//     Route::post('', [App\Http\Controllers\Checkout\CheckoutController::class, 'store'])->name('checkout.store');
//     Route::get('{checkout}', [App\Http\Controllers\Checkout\CheckoutController::class, 'show'])->name('checkout.show');
//     Route::put('{checkout}', [App\Http\Controllers\Checkout\CheckoutController::class, 'update'])->name('checkout.update');
//     Route::delete('{checkout}', [App\Http\Controllers\Checkout\CheckoutController::class, 'destroy'])->name('checkout.destroy');
//     Route::post('{checkout}/items', [App\Http\Controllers\Checkout\CheckoutItemController::class, 'store'])->name('checkout.items.store');
//     Route::put('{checkout}/items/{itemId}', [App\Http\Controllers\Checkout\CheckoutItemController::class, 'update'])->name('checkout.items.update');
//     Route::delete('{checkout}/items/{itemId}', [App\Http\Controllers\Checkout\CheckoutItemController::class, 'destroy'])->name('checkout.items.destroy');
//     Route::post('{checkout}/discount', [App\Http\Controllers\Checkout\CheckoutDiscountController::class, 'store'])->name('checkout.discount');
// });
Route::group(['prefix'=>'order', 'as'=>'order'], function(){
    // Route::get('/', [App\Http\Controllers\OrderController::class, 'all'])->name('all');
    // Route::post('/new', [App\Http\Controllers\OrderController::class, 'new'])->name('new');
    // Route::post('/edit', [App\Http\Controllers\OrderController::class, 'edit'])->name('edit');
    // Route::get('/view', [App\Http\Controllers\OrderController::class, 'view'])->name('view');
    // Route::get('/remove', [App\Http\Controllers\OrderController::class, 'remove'])->name('remove');
});
