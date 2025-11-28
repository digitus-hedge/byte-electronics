<?php

use App\Http\Controllers\BannersController;
use App\Http\Controllers\BlogController as BackendBlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController as BackendCartController;
use App\Http\Controllers\Frontend\FrontendBrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\FrontendCategoryController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\ProductAttrDocumentController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\FrontendProfileController;
use App\Http\Controllers\ProductImportController;
use App\Events\DetailedTestMessage;
use App\Http\Controllers\ProductHeaderController;
use App\Http\Controllers\OrderController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Events\TestEvent;
use App\Http\Controllers\Frontend\FrontendCheckoutController;
use App\Http\Controllers\Frontend\FrontendOrderController;
use App\Http\Controllers\EmailVerificationController;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });
// Route::get('/', function () {
//     return Inertia::render('Home');
// })->name('home');
Route::get('/', [HomeController::class, 'Home'])->name('home');
// Route::get('/product-details', function () {
//     return Inertia::render('Products/ProductDetails');
// })->name('product-details');
Route::get('/dashboard', function () {
    return redirect(url('/'));
    // return Inertia::render('Dashboard', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,f
    //     'phpVersion' => PHP_VERSION,
    // ]);
})->name('dashboard');
// Route::get('/products/list', function () {
//     return Inertia::render('Products');
// })->name('products.list');
// Route::redirect('/dashboard', '/');
// Route::redirect('/home', '/');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

});

// Admin routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    // 'admin' // You might want to create this middleware
])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', 'dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/order-details/{orderDetail}/status', [OrderController::class, 'updateItemStatus'])->name('order-details.update-status');
    Route::get('/profile/edit/{userId}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');                                                     // List users
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');                                            // Create user form
    Route::post('/users', [UserController::class, 'store'])->name('users.store');                                                    // Save new user
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');                                           // Edit user permissions
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/assign-permissions', [UserController::class, 'assignPermissions'])->name('users.assign-permissions'); // Assign permissions
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');                                       // Delete user
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');                                   // Restore deleted user
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');                   // Permanently delete user

    Route::get('/banners', [BannersController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannersController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannersController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [BannersController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannersController::class, 'update'])->name('banners.update');
    Route::get('/banners/show/{id}', [BannersController::class, 'show'])->name('banners.show');
    Route::delete('/banners/{banner}', [BannersController::class, 'destroy'])->name('banners.destroy');

    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::get('/categories/show/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
    Route::post('/categories/add-featured', [CategoryController::class, 'addFeatured'])->name('categories.addFeatured');
    Route::post('/categories/remove-featured', [CategoryController::class, 'removeFeatured'])->name('categories.removeFeatured');

    Route::get('/subcategories/create', [SubCategoryController::class, 'create'])->name('subcategories.create');
    Route::get('/subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');
    Route::post('/subcategories', [SubCategoryController::class, 'store'])->name('subcategories.store');
    Route::get('/subcategories/{subcategory}/edit', [SubCategoryController::class, 'edit'])->name('subcategories.edit');
    // Route::put('/subcategories/update',[SubCategoryController::class,'update'])->name('subcategories.update');
    Route::put('subcategories/{subcategory}', [SubCategoryController::class, 'update'])->name('subcategories.update');
    Route::get('/subcategories/show/{id}', [SubCategoryController::class, 'show'])->name('subcategories.show');
    Route::delete('/subcategories/{subcategory}', [SubCategoryController::class, 'destroy'])->name('subcategories.destroy');
    Route::get('/subcategories/by-category/{categoryId}/{currentSubcategoryId}', [SubCategoryController::class, 'getSubcategoriesByCategory']);
    Route::get('/get-subcategories/{categoryId}', [CategoryController::class, 'getSubcategories'])->name('get.subcategories');

    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::get('/brands/show/{id}', [BrandController::class, 'show'])->name('brands.show');
    Route::delete('/brands', [BrandController::class, 'destroy'])->name('brands.destroy');
    Route::post('/brands/add-featured', [BrandController::class, 'addFeatured'])->name('brands.addFeatured');
    Route::post('/brands/remove-featured', [BrandController::class, 'removeFeatured'])->name('brands.removeFeatured');
 
   
    Route::get('/products/create', [ProductsController::class, 'create'])->name('productManage.create');
    Route::get('/products', [ProductsController::class, 'index'])->name('productManage.index');
    Route::get('/products/edit/{product}', [ProductsController::class, 'edit'])->name('productManage.edit');
    Route::post('/products', [ProductsController::class, 'store'])->name('productManage.store');
    Route::put('/products/{product}', [ProductsController::class, 'update'])->name('productManage.update');
    Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('productManage.destroy');
    Route::put('/addFeatured', [ProductsController::class, 'addFeatured'])->name('productManage.addFeatured');
    Route::put('/removeFeatured', [ProductsController::class, 'removeFeatured'])->name('productManage.removeFeatured');
    Route::put('/addBestSeller', [ProductsController::class, 'addBestSeller'])->name('productManage.addBestSeller');
    Route::put('/removeBestSeller', [ProductsController::class, 'removeBestSeller'])->name('productManage.removeBestSeller');
    //product sample excel download
    Route::get('products/bulk-import', [ProductsController::class, 'productBulkImport'])->name('productManage.bulk-import');
    Route::post('/products/import', [ProductsController::class, 'productBulkImportStore'])->name('productManage.import');
    Route::post('/products/price_import',[ProductsController::class, 'productPriceImport'])->name('productManage.price_import');
    Route::post('/products/specification_import', [ProductsController::class, 'productSpecificationImportStore'])->name('productManage.specification_import_store');
    Route::post('/products/more_info_import', [ProductsController::class, 'productMoreInfoImportStore'])->name('productManage.more_info_import');

    //product search
    Route::get('/products/search', [ProductsController::class, 'search'])->name('productManage.search');
    // Request for Quote
    Route::get('/products/rfq', [ProductsController::class, 'requestQuote'])->name('productManage.requestQuote');


    

    Route::get('/get-attributes/{headingId}', [ProductHeaderController::class, 'getAttributes']);
    Route::post('/add-heading', [ProductHeaderController::class, 'addHeading']);
    Route::post('/add-attribute', [ProductAttributeController::class, 'addAttribute']);

    Route::get('/product-documents', [ProductAttrDocumentController::class, 'index'])->name('product-documents.index');
    Route::get('/product-documents/create', [ProductAttrDocumentController::class, 'create'])->name('product-documents.create');
    Route::post('/product-documents', [ProductAttrDocumentController::class, 'store'])->name('product-documents.store');
    Route::get('/product-documents/{document}/edit', [ProductAttrDocumentController::class, 'edit'])->name('product-documents.edit');
    Route::put('/product-documents/{document}', [ProductAttrDocumentController::class, 'update'])->name('product-documents.update');
    Route::delete('/product-documents/{document}', [ProductAttrDocumentController::class, 'destroy'])->name('product-documents.destroy');

    Route::resource('/blogs', BackendBlogController::class)->except(['show']);

    Route::get('/attributes', [ProductAttributeController::class, 'index'])->name('attributes.index');
    Route::get('/attributes/create', [ProductAttributeController::class, 'create'])->name('attributes.create');
    Route::post('/attributes', [ProductAttributeController::class, 'store'])->name('attributes.store');
    Route::get('/attributes/{id}', [ProductAttributeController::class, 'show'])->name('attributes.show');
    Route::get('/attributes/{id}/edit', [ProductAttributeController::class, 'edit'])->name('attributes.edit');
    Route::put('/attributes/{id}', [ProductAttributeController::class, 'update'])->name('attributes.update');
    Route::delete('/attributes/{id}', [ProductAttributeController::class, 'destroy'])->name('attributes.destroy');

    // Additional Routes
    Route::post('attributes/{id}/restore', [ProductAttributeController::class, 'restore'])->name('attributes.restore');
    Route::delete('attributes/{id}/force-delete', [ProductAttributeController::class, 'forceDelete'])->name('attributes.forceDelete');

    Route::get('/product_images', [ProductImageController::class, 'index'])->name('product_images.index');

    Route::get('/get-subcategories/{categoryId}', [ProductsController::class, 'getSubCategories']);

    Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.index');
    Route::get('/currencies/create', [CurrencyController::class, 'create'])->name('currencies.create');
    Route::post('/currencies', [CurrencyController::class, 'store'])->name('currencies.store');
    Route::get('/currencies/{currency}/edit', [CurrencyController::class, 'edit'])->name('currencies.edit');
    Route::put('/currencies/{currency}', [CurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{currency}', [CurrencyController::class, 'destroy'])->name('currencies.destroy');
    Route::post('/currencies/{id}/restore', [CurrencyController::class, 'restore'])->name('currencies.restore');
    Route::get('/getTopCurrencies/{currencyId}', [CurrencyController::class, 'getTopCurrencies'])->name('getTopCurrencies');
    Route::post('/updateConversionRates', [CurrencyController::class, 'updateConversionRates'])->name('updateConversionRates');

    // Route::delete('/currencies/{id}/force-delete', [CurrencyController::class, 'forceDelete'])->name('currencies.forceDelete');
    // Route::resource('/currencies', CurrencyController::class)->except(['show']);
});
Route::group(['prefix' => 'products', 'as' => 'product.'], function () {
    Route::get('/list', [ProductController::class, 'index'])->name('index');
    // Route::get('/details', [ProductController::class, 'details'])->name('product.details'); //this should be removed or commented after testing
    Route::get('/details/{slug}', [ProductController::class, 'details'])->name('product.details');
    Route::get('/filter', [ProductController::class, 'filter'])->name('product.filter');
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/similar-count', [ProductController::class, 'getSimilarProductsCount'])->name('products.similar-count');
    Route::post('/request-quote/{productId}', [ProductController::class, 'requestQuote'])
    ->name('productManage.requestQuote');
});

Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function () {
    Route::get('/', [BlogController::class, 'index'])->name('blogs.index');
    // Route::get('details', [BlogController::class, 'show'])->name('blogs.show');
    Route::get('details/{slug}', [BlogController::class, 'show'])->name('blogs.show');
});


Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
    Route::get('/list', [FrontendCategoryController::class, 'index'])->name('categories.index');
    // Route::get('/details/{slug}', [FrontendCategoryController::class, 'view'])->name('categories.details');
    Route::get('/details/{any}', [FrontendCategoryController::class,'show'])->where('any', '.*')->name('category.details');
    Route::post('/category-action', [FrontendCategoryController::class, 'handleAction']);
});
Route::group(['prefix'=>'brands','as'=>'brands.'],function(){
    Route::get('/list',[FrontendBrandController::class,'index'])->name('list');
    Route::get('/details/{slug}',[FrontendBrandController::class,'details'])->name('details');
});
// Route::group(['prefix'=>'cart','as'=>'cart.'],function(){
//     Route::get('/',[CartController::class,'index'])->name('cart');
// });
// Route::group(['prefix'=>'orders','as'=>'orders.'],function(){
//     Route::get('/',[CartController::class,'orders'])->name('orders');
// });
Route::get('/get-attributes/{headingId}', [ProductHeaderController::class, 'getAttributes']);
Route::post('/add-heading', [ProductHeaderController::class, 'addHeading']);
Route::post('/add-attribute', [ProductAttributeController::class, 'addAttribute']);


Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/terms-conditions', [HomeController::class, 'termsConditions'])->name('terms-conditions');

Route::get('/contact-us', [HomeController::class, 'contactUs'])->name('contactUs');
Route::post('/contact-us/submitMail', [HomeController::class, 'submitMail'])->name('submitMail');
Route::get('/about-us', [HomeController::class, 'about'])->name('aboutUs');
// Route::get('/my-account',[FrontendProfileController::class,'index'])->name('profile');

// Cart routes
Route::post('/cart/add/{productId}', [BackendCartController::class, 'addToCart'])->name('cart.add');
Route::put('/cart/update/{itemId}', [BackendCartController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/remove/{itemId}', [BackendCartController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart/count', [BackendCartController::class, 'count'])->name('count');
Route::get('/cart',[BackendCartController::class,'index'])->name('cart');
// Route::get('/cart', [BackendCartController::class, 'viewCart'])->name('cart.view');

//Profile
Route::get('/profile',[FrontendProfileController::class,'index'])->name('profile');
Route::post('/profile/address', [FrontendProfileController::class, 'storeAddress'])->name('profile.address.store');
Route::put('/profile/address/{id}', [FrontendProfileController::class, 'updateAddress'])->name('profile.address.update');
Route::delete('/profile/address/{id}', [FrontendProfileController::class, 'deleteAddress'])->name('profile.address.delete');
Route::get('/checkout-page',[FrontendCheckoutController::class,'checkoutPage'])->name('checkout.page');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');


// Route::get('/send-detailed-test', function () {
//     $message = 'Test message from Byte-backend';
//     $sender = 'Byte Admin';
//     Log::info('Broadcasting DetailedTestMessage', ['message' => $message, 'sender' => $sender]);
//     event(new DetailedTestMessage($message, $sender));
//     return 'Detailed test message sent!';
// });f

Route::get('/my-orders', [FrontendOrderController::class, 'myOrders'])->name('my-orders');

Route::get('/send-detailed-test', function () {
    $message = 'Test message from Byte-backend';
    $sender = 'Byte Admin';
    Log::info('Broadcasting DetailedTestMessage', ['message' => $message, 'sender' => $sender]);
    broadcast(new DetailedTestMessage($message, $sender));
    return 'Detailed test message sent!';
});

Route::get('/send-test', function () {
    $event = new TestEvent('Message from Byte-new');
    event($event);
    Log::info('TestEvent dispatched', ['message' => $event->message]);
    return 'Event sent';
});

Route::get('/product-line', [FrontendBrandController::class, 'prodductLine'])->name('product.line');


Route::post('/import-products', [ProductImportController::class, 'importProducts']);

Route::get('get-orders/{id}', [FrontendOrderController::class, 'getOrders'])->name('getOrders');
// Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
// Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware(['signed']);
// Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])->name('verification.send');
// Route::post('/products/dynamic-attributes', [ProductController::class, 'dynamicAttributes']);


