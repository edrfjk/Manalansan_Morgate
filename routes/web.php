<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ShipperController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\BookQueryController;

/*
|--------------------------------------------------------------------------
| Class activities
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/person', function () {
    return view('person');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
});

Route::get('/p1', [MovieController::class, 'index']);
Route::get('/movie-form', [MovieController::class, 'show_add_form']);
Route::post('/add-movie', [MovieController::class, 'do_add']);
Route::get('/edit-movie/{id}', [MovieController::class, 'show_edit_form']);
Route::post('/update-movie', [MovieController::class, 'do_update']);
Route::get('/delete-movie/{id}', [MovieController::class, 'do_delete']);

Route::get('/p2', [BookController::class, 'index']);
Route::get('/book-form', [BookController::class, 'show_add_form']);
Route::post('/add-book', [BookController::class, 'do_add']);
Route::get('/edit-book/{id}', [BookController::class, 'show_edit_form']);
Route::post('/update-book', [BookController::class, 'do_update']);
Route::get('/delete-book/{id}', [BookController::class, 'do_delete']);

Route::get('/p3', fn () => view('pages.placeholder', ['heading' => 'Page 3']));
Route::get('/p4', fn () => view('pages.placeholder', ['heading' => 'Page 4']));


/*
|--------------------------------------------------------------------------
| Avenza — authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/auth', [AuthController::class, 'show']);
    Route::post('/login', [AuthController::class, 'do_login']);
    Route::post('/register', [AuthController::class, 'do_register']);
});

Route::post('/logout', [AuthController::class, 'do_logout'])->middleware('auth');


/*
|--------------------------------------------------------------------------
| Avenza — customer side (still placeholders, built in a later step)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'customer'])->prefix('avenza')->group(function () {
    Route::get('/', [ShopController::class, 'home']);
    Route::get('/shop', [ShopController::class, 'index']);
    Route::get('/shop/{id}', [ShopController::class, 'show'])->whereNumber('id');

    // still placeholders
    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'do_add']);
    Route::patch('/cart/update', [CartController::class, 'do_update']);
    Route::delete('/cart/{productId}', [CartController::class, 'do_remove'])->whereNumber('productId');

    // Checkout
    Route::get('/checkout', [CartController::class, 'checkout']);
    Route::post('/checkout', [CartController::class, 'do_place_order']);
        // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index']);
    Route::get('/orders/{id}', [CustomerOrderController::class, 'show'])->whereNumber('id');
    Route::patch('/orders/{id}/cancel', [CustomerOrderController::class, 'do_cancel'])->whereNumber('id');

    // Profile
    Route::get('/profile', [CustomerOrderController::class, 'edit']);
    Route::patch('/profile', [CustomerOrderController::class, 'do_update']);
});


/*
|--------------------------------------------------------------------------
| Avenza — admin side
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('avenza/admin')->group(function () {

    Route::get('/', [AdminController::class, 'dashboard']);

    // Manage Product
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/create', [ProductController::class, 'show_add_form']);
    Route::post('/products', [ProductController::class, 'do_add']);
    Route::get('/products/{id}/edit', [ProductController::class, 'show_edit_form'])->whereNumber('id');
    Route::post('/products/update', [ProductController::class, 'do_update']);
    Route::delete('/products/{id}', [ProductController::class, 'do_delete'])->whereNumber('id');

    // Manage Customer
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::patch('/customers/{id}/status', [CustomerController::class, 'update_status'])->whereNumber('id');

    // Manage Cart or Order
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show'])->whereNumber('id');
    Route::patch('/orders/{id}/update', [OrderController::class, 'do_update'])->whereNumber('id');
    Route::delete('/orders/{orderId}/lines/{lineId}', [OrderController::class, 'delete_line'])->whereNumber(['orderId', 'lineId']);
    Route::delete('/orders/{id}', [OrderController::class, 'do_delete'])->whereNumber('id');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/create', [CategoryController::class, 'show_add_form']);
    Route::post('/categories', [CategoryController::class, 'do_add']);
    Route::get('/categories/{id}/edit', [CategoryController::class, 'show_edit_form'])->whereNumber('id');
    Route::post('/categories/update', [CategoryController::class, 'do_update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'do_delete'])->whereNumber('id');

    // Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/suppliers/create', [SupplierController::class, 'show_add_form']);
    Route::post('/suppliers', [SupplierController::class, 'do_add']);
    Route::get('/suppliers/{id}/edit', [SupplierController::class, 'show_edit_form'])->whereNumber('id');
    Route::post('/suppliers/update', [SupplierController::class, 'do_update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'do_delete'])->whereNumber('id');

    // Shippers
    Route::get('/shippers', [ShipperController::class, 'index']);
    Route::get('/shippers/create', [ShipperController::class, 'show_add_form']);
    Route::post('/shippers', [ShipperController::class, 'do_add']);
    Route::get('/shippers/{id}/edit', [ShipperController::class, 'show_edit_form'])->whereNumber('id');
    Route::post('/shippers/update', [ShipperController::class, 'do_update']);
    Route::delete('/shippers/{id}', [ShipperController::class, 'do_delete'])->whereNumber('id');
});



/*
|--------------------------------------------------------------------------
| Activity Story
|--------------------------------------------------------------------------
*/

Route::get('/story', function () {
    return view('pages.story');
});

Route::get('/book-query-comparison', [BookQueryController::class, 'index']);