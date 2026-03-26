<?php
use App\Http\Controllers\vendor\VendorController;
use App\Http\Controllers\vendor\CategoryController;
use App\Http\Controllers\vendor\ProductController;
use App\Http\Controllers\vendor\AttributeController;
use App\Http\Controllers\vendor\VendorStoreController;
use App\Http\Controllers\vendor\BrandController;
use App\Http\Controllers\vendor\CurrencyController;
use App\Http\Controllers\vendor\OrderController;
use App\Http\Controllers\vendor\PartnerOrderController;
use App\Http\Controllers\vendor\AlibabaProductController;
use App\Http\Controllers\vendor\ProductImportController;
use App\Http\Controllers\vendor\CjController;
use App\Http\Controllers\vendor\CouponController;
use App\Http\Controllers\vendor\LanguageController;
use App\Http\Controllers\vendor\StoreCategoryController;
use App\Http\Controllers\vendor\ImportImageController;
use App\Http\Controllers\vendor\DobaController;
use App\Http\Controllers\vendor\AutoDSController;

use App\Http\Controllers\vendor\PartnerProductController;

Route::group(['prefix' => 'vendor','middleware' => ['auth','vendor']],function(){
	
	#PARTNERS COMPGAIN
	Route::get('partner-products/upload-template', [PartnerProductController::class, 'downloadTemplate'])->name('vendor.partner-products.template');
    Route::post('partner-products/upload', [PartnerProductController::class, 'upload'])->name('vendor.partner-products.upload');
    Route::get('partner-products', [PartnerProductController::class, 'index'])->name('vendor.partner-products.index');
	Route::get('partner-products/import-errors', [PartnerProductController::class,'importErrors'])->name('vendor.partner-products.import_errors');
Route::post('partner-products/clear-import-errors', [PartnerProductController::class,'clearImportErrors'])->name('vendor.partner-products.clear_import_errors');
	
	
	#IMPORT IMAGE
	Route::get('/upload-images', [ImportImageController::class, 'showForm'])->name('images.upload.form');
	Route::post('/upload-images', [ImportImageController::class, 'upload'])->name('images.upload');
	
	#LANGUAGE
	Route::resource('vendor-languages', LanguageController::class);
	
	#DASHBOARD
    Route::get('/dashboard', [VendorController::class, 'index'])->name('vendor.dashboard');
    
	#CHANGE PASSWORD
	Route::get('/change-password', [VendorController::class, 'change_password'])->name('vendor.change_password');
    Route::post('/change_password_action', [VendorController::class, 'change_password_action'])->name('vendor.change_password_action');
	
	#PROFILE
	Route::get('/profile', [VendorController::class, 'profile'])->name('vendor.profile');
    Route::post('/update_profile_action', [VendorController::class, 'update_profile_action'])->name('vendor.update_profile_action');
	
	#CATEGORY
    //Route::resource('categories', CategoryController::class);
    Route::resource('category', CategoryController::class);
    Route::post('/category/update-status', [CategoryController::class, 'updateStatus'])->name('category.updateStatus');
	
	#PRODUCTS
	Route::resource('products', ProductController::class)->except(['show']);
	Route::post('/products/update-status', [ProductController::class, 'updateStatus'])->name('products.updateStatus');
	Route::get('/product-gallery/delete/{id}', [ProductController::class, 'delete'])->name('gallery.delete');
	
	#PRODUCTS EXPORT
	Route::get('/products/export', [ProductController::class, 'exportProducts'])->name('vendor.exportProducts');
	
	#PRODUCTS IMPORT
	Route::post('/products/import', [ProductController::class, 'importProducts'])->name('vendor.importProducts');
	
	#PRODUCT ATTRIBUTES
	Route::resource('attributes', AttributeController::class);
	
	#MULTI VENDOR STORE
	Route::get('/store', [VendorStoreController::class, 'show'])->name('vendor.vendorstore.show');
	Route::get('/store/add', [VendorStoreController::class, 'add'])->name('vendor.vendorstore.add');
	Route::post('/store/create', [VendorStoreController::class, 'create'])->name('vendor.vendorstore.create');
	Route::get('/store/edit/{id}', [VendorStoreController::class, 'edit'])->name('vendor.vendorstore.edit');
    Route::put('/store/update/{id}', [VendorStoreController::class, 'update'])->name('vendor.vendorstore.update');
    Route::get('/store/{id}', [VendorStoreController::class, 'destroy'])->name('vendor.vendorstore.destroy');
	
	#COUPONS
	Route::get('/vendor-coupon', [CouponController::class, 'index'])->name('vendor.coupon.show');
	Route::get('/vendor-coupon/create', [CouponController::class, 'create'])->name('vendor.coupon.create');
	Route::post('vendor-coupon/store', [CouponController::class, 'store'])->name('vendor.coupon.store');
	Route::get('/vendor-coupon/edit/{id}', [CouponController::class, 'edit'])->name('vendor.coupon.edit');
	Route::put('vendor-coupon/{id}', [CouponController::class, 'update'])->name('vendor.coupon.update');
	Route::delete('vendor-coupon/{id}', [CouponController::class, 'destroy'])->name('vendor.coupon.destroy');
	
	#ASSIGN CATEGORY STORE WISE
	Route::get('/category-store', [StoreCategoryController::class, 'show'])->name('vendor.categorystore.show');
	Route::get('/category-store/add', [StoreCategoryController::class, 'add'])->name('vendor.categorystore.add');
	Route::post('/category-store/create', [StoreCategoryController::class, 'create'])->name('vendor.categorystore.create');
	Route::get('/category-store/edit/{id}', [StoreCategoryController::class, 'edit'])->name('vendor.categorystore.edit');
    Route::put('/category-store/update/{id}', [StoreCategoryController::class, 'update'])->name('vendor.categorystore.update');
    Route::get('/category-store/{id}', [StoreCategoryController::class, 'destroy'])->name('vendor.categorystore.destroy');
	
	#CURRENCY
	Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');
	Route::get('/currency/add-currency', [CurrencyController::class, 'add_currency'])->name('currency.add_currency');
	Route::post('/currency/add_currency_action', [CurrencyController::class, 'add_currency_action'])->name('currency.add_currency_action');
	Route::get('/currency/edit/{id}', [CurrencyController::class, 'edit_currency'])->name('currency.edit_currency');
	Route::post('/currency/edit_currency_action', [CurrencyController::class, 'edit_currency_action'])->name('currency.edit_currency_action');
	Route::get('/currency/delete/{id}', [CurrencyController::class, 'delete_currency'])->name('currency.delete_currency');
	
	#BRANDS
    Route::resource('product-brands', BrandController::class);
    Route::post('/product-brands/update-status', [BrandController::class, 'updateStatus'])->name('product.brands.updateStatus');
	
	
	#ORDERS
    Route::resource('product-orders', OrderController::class);
	Route::get('/order/product-order-details/{id}', [OrderController::class, 'orderDetails'])->name('product-orders.orderDetails');
	Route::get('/product-order/{id}/invoice', [OrderController::class, 'downloadInvoice'])->name('vendor.order.invoice.download');
    Route::post('/product-orders/update-status', [OrderController::class, 'updateorderStatus'])->name('product-orders.updateorderStatus');
    Route::post('/product-orders/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('product-orders.updatePaymentStatus');
	
	#CAMPGAIN ORDERS
    Route::resource('partner-product-orders', PartnerOrderController::class);
	Route::get('/partner-order/product-order-details/{id}', [PartnerOrderController::class, 'orderDetails'])->name('partner-product-orders.orderDetails');
	Route::get('/partner-product-order/{id}/invoice', [PartnerOrderController::class, 'downloadInvoice'])->name('vendor.partner.order.invoice.download');
    Route::post('/partner-product-orders/update-status', [PartnerOrderController::class, 'updateorderStatus'])->name('partner-product-orders.updateorderStatus');
    Route::post('/partner-product-orders/payment-status', [PartnerOrderController::class, 'updatePaymentStatus'])->name('partner-product-orders.updatePaymentStatus');
	
	Route::get('/run-migrations', function () {
        Artisan::call('migrate', [
            '--force' => true,
        ]);
    
        return 'Migrations executed successfully!';
    });
	
	#ALI BABA PRODUCT IMPORT
	//Route::post('/alibaba/import', [AlibabaProductController::class, 'import']);

	Route::get('/alibaba/import-product', [AlibabaProductController::class, 'importProduct']);
	Route::post('/alibaba/import', [AlibabaProductController::class, 'import']);
	
	#PRODUCT IMPORT BY WHOLESALE2 XML FEED URL
	Route::get('/wholesale2b/products/import', [ProductImportController::class, 'wholesale2bImport']);
	Route::post('/wholesale2b-products/import', [ProductImportController::class, 'import'])->name('products.import');
	
	#CJ DROPSHIPING
	Route::get('cj/token', [CjController::class, 'getToken'])->name('vendor.cj.token');
    Route::post('cj/import', [CjController::class, 'store'])->name('vendor.cj.import');
	
	#DOBA DROPSHIPPING
	Route::get('doba/import', [DobaController::class, 'import'])->name('vendor.doba.import');
	
	#AUTO-DS DROPSHIPING
    Route::post('vendor/autods/import', [AutoDSController::class, 'store'])->name('vendor.autods.import');
	
	

});