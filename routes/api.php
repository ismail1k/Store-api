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


Route::group(['prefix'=>'search', 'as'=>'search.'], function(){
    Route::get('/product', [App\Http\Controllers\SearchController::class, 'product'])->name('product');
});
Route::group(['prefix'=>'data', 'as'=>'data.'], function(){
    Route::get('/menu', [App\Http\Controllers\DataController::class, 'menu'])->name('menu');
});
Route::group(['prefix'=>'contact', 'as'=>'contact.'], function(){
    Route::get('/', [App\Http\Controllers\ContactController::class, 'all'])->name('all');
    Route::get('/view', [App\Http\Controllers\ContactController::class, 'view'])->name('view');
    Route::post('/send', [App\Http\Controllers\ContactController::class, 'send'])->name('send');
    Route::post('/remove', [App\Http\Controllers\ContactController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'auth'], function(){
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::get('/me', [App\Http\Controllers\AuthController::class, 'me'])->name('me');
    Route::post('/changepassword', [App\Http\Controllers\AuthController::class, 'changepassword'])->name('changepassword');
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
});
Route::group(['prefix'=>'config', 'as'=>'config.'], function(){
    Route::get('/', [App\Http\Controllers\ConfigController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\ConfigController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\ConfigController::class, 'edit'])->name('edit');
    Route::get('/view', [App\Http\Controllers\ConfigController::class, 'view'])->name('view');
    Route::get('/remove', [App\Http\Controllers\ConfigController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'user', 'as'=>'user.'], function(){
    Route::get('/', [App\Http\Controllers\UserController::class, 'select'])->name('select');
    Route::get('/all', [App\Http\Controllers\UserController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\UserController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
    Route::post('/ban', [App\Http\Controllers\UserController::class, 'ban'])->name('ban');
    Route::post('/unban', [App\Http\Controllers\UserController::class, 'unban'])->name('unban');
});
Route::group(['prefix'=>'category', 'as'=>'category.'], function(){
    Route::get('/', [App\Http\Controllers\CategoryController::class, 'all'])->name('all');
    Route::post('/edit', [App\Http\Controllers\CategoryController::class, 'edit'])->name('edit');
    Route::post('/remove', [App\Http\Controllers\CategoryController::class, 'remove'])->name('remove');
    Route::post('/new', [App\Http\Controllers\CategoryController::class, 'new'])->name('new');
});
Route::group(['prefix'=>'inventory', 'as'=>'invetory.'], function(){
    Route::get('/', [App\Http\Controllers\InventoryController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\InventoryController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\InventoryController::class, 'edit'])->name('edit');
    Route::get('/view', [App\Http\Controllers\InventoryController::class, 'view'])->name('view');
    Route::post('/increment', [App\Http\Controllers\InventoryController::class, 'increment'])->name('increment');
    Route::post('/descrement', [App\Http\Controllers\InventoryController::class, 'descrement'])->name('descrement');
    Route::get('/remove', [App\Http\Controllers\InventoryController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'permission', 'as'=>'permission.'], function(){
    Route::get('/', [App\Http\Controllers\PermissionController::class, 'all'])->name('all');
    Route::post('/allow', [App\Http\Controllers\PermissionController::class, 'allow'])->name('allow');
    Route::post('/disallow', [App\Http\Controllers\PermissionController::class, 'disallow'])->name('disallow');
    Route::post('/update', [App\Http\Controllers\PermissionController::class, 'update'])->name('update');
});
Route::group(['prefix'=>'media', 'as'=>'media.'], function(){
    Route::get('/', [App\Http\Controllers\MediaController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\MediaController::class, 'new'])->name('new');
    Route::get('/view', [App\Http\Controllers\MediaController::class, 'view'])->name('view');
    Route::get('/remove', [App\Http\Controllers\MediaController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'product', 'as'=>'product.'], function(){
    Route::get('/', [App\Http\Controllers\ProductController::class, 'all'])->name('all');
    Route::post('/new', [App\Http\Controllers\ProductController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('edit');
    Route::get('/view', [App\Http\Controllers\ProductController::class, 'view'])->name('view');
    Route::get('/remove', [App\Http\Controllers\ProductController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'cart', 'as'=>'cart.'], function(){
    Route::get('/', [App\Http\Controllers\CartController::class, 'view'])->name('view');
    Route::get('/create', [App\Http\Controllers\CartController::class, 'create'])->name('create');
    Route::post('/addToCart', [App\Http\Controllers\CartController::class, 'addToCart'])->name('addToCart');
    Route::post('/removeFromCart', [App\Http\Controllers\CartController::class, 'removeFromCart'])->name('removeFromCart');
    Route::get('/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('clear');
});
Route::group(['prefix'=>'order', 'as'=>'order.'], function(){
    Route::get('/', [App\Http\Controllers\OrderController::class, 'all'])->name('all');
    Route::get('/view', [App\Http\Controllers\OrderController::class, 'view'])->name('view');
    Route::post('/new', [App\Http\Controllers\OrderController::class, 'new'])->name('new');
    Route::post('/edit', [App\Http\Controllers\OrderController::class, 'edit'])->name('edit');
    Route::post('/remove', [App\Http\Controllers\OrderController::class, 'remove'])->name('remove');
});
Route::group(['prefix'=>'payment', 'as'=>'payment.'], function(){
    Route::group(['prefix'=>'paypal', 'as'=>'paypal.'], function(){
        Route::get('', [App\Http\Controllers\PaypalPaymentProcessor::class, 'init'])->name('init');
        Route::get('/return', [App\Http\Controllers\PaypalPaymentProcessor::class, 'return'])->name('return');
        Route::post('/return', [App\Http\Controllers\PaypalPaymentProcessor::class, 'execute'])->name('execute');
        Route::get('/cancel', [App\Http\Controllers\PaypalPaymentProcessor::class, 'cancel'])->name('cancel');
    });
    Route::group(['prefix'=>'cod', 'as'=>'cod'], function(){
        Route::get('', [App\Http\Controllers\CashondeliveryPaymentProcessor::class, 'init'])->name('init');
        Route::get('/execute', [App\Http\Controllers\CashondeliveryPaymentProcessor::class, 'execute'])->name('execute');
    });
});