<?php
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PartnerOrderController;
use App\Http\Controllers\admin\VendorController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\AttributeController;
use App\Http\Controllers\admin\VendorStoreController;
use App\Http\Controllers\admin\BlogCategoryController;
use App\Http\Controllers\admin\BlogController;
use App\Http\Controllers\admin\HomePageController;
use App\Http\Controllers\admin\CurrencyController;
use App\Http\Controllers\admin\SlidersController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\NewsletterController;
use App\Http\Controllers\admin\ProductImportController;
use App\Http\Controllers\admin\CjController;
use App\Http\Controllers\admin\CouponController;
use App\Http\Controllers\admin\LanguageController;
use App\Http\Controllers\admin\PaymentGatewayController;
use App\Http\Controllers\admin\ImportImageController;

use App\Http\Controllers\admin\TransactionController;
use App\Http\Controllers\admin\GlobalCommissionController;
use App\Http\Controllers\admin\ProductCommisionsController;
use App\Http\Controllers\admin\VendorCommissionController;
use App\Http\Controllers\admin\AutoDSController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\PartnerCampaignController;
use App\Http\Controllers\admin\PartnerProductController as AdminPartnerProductController;

use App\Http\Controllers\admin\ShippingPriceController;
use App\Http\Controllers\admin\ShippingOptionsController;

Route::group(['prefix' => 'admin','middleware' => ['auth','admin']],function(){
    
	#SHIPPING OPTIONS
	Route::resource('shipping-options', ShippingOptionsController::class);
	Route::resource('shipping-prices', ShippingPriceController::class);
	Route::post('/shipping-prices/{id}/toggle-status',[ShippingPriceController::class, 'toggleStatus'])->name('shipping-prices.toggle-status');
	Route::post('shipping-prices/{id}/set-default',[ShippingPriceController::class, 'setDefault']);
	Route::post('shipping-prices/{id}/update-order',[ShippingPriceController::class, 'updateOrder']);
	
	#PARTNERS COMPGAIN
	Route::resource('partner-campaigns', PartnerCampaignController::class);
	Route::get('partner-products', [AdminPartnerProductController::class, 'index'])->name('admin.partner-products.index');
	Route::post('partner-products/{id}/approve', [AdminPartnerProductController::class, 'approve'])->name('admin.partner-products.approve');
    Route::post('partner-products/{id}/reject', [AdminPartnerProductController::class, 'reject'])->name('admin.partner-products.reject');
	
	
	#TRANSACTION COMMISSIONS
	Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('transactions/{id}/settle', [TransactionController::class, 'settle'])->name('transactions.settle');
    Route::post('transactions/{id}/refund', [TransactionController::class, 'refund'])->name('admin.transactions.refund');
	
	#Global Commission
    Route::get('commission/global', [GlobalCommissionController::class, 'showForm'])->name('admin.global.commission');
    Route::post('commission/global', [GlobalCommissionController::class, 'save'])->name('admin.global.commission.save');

    #Vendors Commission
    Route::get('vendor-commissions', [VendorCommissionController::class, 'indexBlade'])->name('admin.vendor_commissions.index');
    Route::get('vendor-commissions/create', [VendorCommissionController::class, 'create'])->name('admin.vendor_commissions.create');
    Route::post('vendor-commissions', [VendorCommissionController::class, 'storeBlade'])->name('admin.vendor_commissions.store');
    Route::get('vendor-commissions/{id}/edit', [VendorCommissionController::class, 'edit'])->name('admin.vendor_commissions.edit');
    Route::put('vendor-commissions/{id}', [VendorCommissionController::class, 'updateBlade'])->name('admin.vendor_commissions.update');
    Route::delete('vendor-commissions/{id}', [VendorCommissionController::class, 'destroyBlade'])->name('admin.vendor_commissions.destroy');

    #Products Commission
    Route::get('product-commissions', [ProductCommisionsController::class, 'indexBlade'])->name('admin.product_commissions.index');
    Route::get('product-commissions/create', [ProductCommisionsController::class, 'create'])->name('admin.product_commissions.create');
    Route::post('product-commissions', [ProductCommisionsController::class, 'storeBlade'])->name('admin.product_commissions.store');
    Route::get('product-commissions/{id}/edit', [ProductCommisionsController::class, 'edit'])->name('admin.product_commissions.edit');
    Route::put('product-commissions/{id}', [ProductCommisionsController::class, 'updateBlade'])->name('admin.product_commissions.update');
    Route::delete('product-commissions/{id}', [ProductCommisionsController::class, 'destroyBlade'])->name('admin.product_commissions.destroy');
	
	Route::get('vendor-products/{vendor_id}', [ProductCommisionsController::class, 'getVendorProducts']);
	

	
	
	#IMPORT IMAGE
	Route::get('/upload-images', [ImportImageController::class, 'showForm'])->name('adminimages.upload.form');
	Route::post('/upload-images', [ImportImageController::class, 'upload'])->name('adminimages.upload');
	
	#LANGUAGE
	Route::resource('languages', LanguageController::class);
	
	#PAYMENT GATEWAY
	Route::get('payment-gateways', [PaymentGatewayController::class, 'index'])->name('admin.payment_gateways.index');
    Route::put('payment-gateways/{id}', [PaymentGatewayController::class, 'update'])->name('admin.payment_gateways.update');
	
	
	#dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/setting', [AdminController::class, 'setting'])->name('admin.setting');
    Route::post('/setting_action', [AdminController::class, 'setting_action'])->name('admin.setting_action');
    Route::get('/change-password', [AdminController::class, 'change_password'])->name('admin.change_password');
    Route::post('/change_password_action', [AdminController::class, 'change_password_action'])->name('admin.change_password_action');
	
	#ACTIVITY LOG HISTORY
    Route::get('/activity-log-history', [AdminController::class, 'activityLlogHistory'])->name('admin.activityLlogHistory');
	
    #Users
    Route::resource('users', UserController::class);
    Route::post('/users/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::get('/users/data', [UserController::class, 'getData'])->name('users.data');
    
    #Vendor
    Route::resource('vendor', VendorController::class);
    Route::post('/vendor/update-status', [VendorController::class, 'updateStatus'])->name('vendor.updateStatus');

    #CATEGORY
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/update-status', [CategoryController::class, 'updateStatus'])->name('categories.updateStatus');
	
	#NEWSLETTER
    Route::resource('newsletters', NewsletterController::class);

    #Theme Setting
    Route::get('/theme-setting', [AdminController::class, 'theme_setting'])->name('admin.theme_setting');
    Route::post('/theme-setting', [AdminController::class, 'theme_setting_action'])->name('admin.theme_setting_action');

    #Mail Setting
    Route::get('/mail-setting', [AdminController::class, 'mail_setting'])->name('admin.mail_setting');
    Route::post('/mail-setting', [AdminController::class, 'mail_setting_action'])->name('admin.mail_setting_action');
    
	#Order
    Route::resource('orders', OrderController::class);
    Route::post('/orders/update-status', [OrderController::class, 'updateorderStatus'])->name('orders.updateorderStatus');
	Route::post('/orders/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
	Route::get('/order/order-details/{id}', [OrderController::class, 'orderDetails'])->name('product-orders.orderDetails');
	Route::get('/order/{id}/invoice', [OrderController::class, 'downloadInvoice'])->name('order.invoice.download');
	
	#Campgain Order
    Route::resource('partner-orders', PartnerOrderController::class);
    Route::post('/partner-orders/update-status', [PartnerOrderController::class, 'updateorderStatus'])->name('partner.orders.updateorderStatus');
	Route::post('/partner-orders/payment-status', [PartnerOrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
	Route::get('/partner-order/order-details/{id}', [PartnerOrderController::class, 'orderDetails'])->name('partner.product-orders.orderDetails');
	Route::get('/partner-order/{id}/invoice', [PartnerOrderController::class, 'downloadInvoice'])->name('partner.order.invoice.download');
	
    #PRODUCTS
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/products/update-status', [ProductController::class, 'updateStatus'])->name('admin.products.updateStatus');
    Route::get('/product-gallery/delete/{id}', [ProductController::class, 'delete'])->name('gallery.delete');
	
	#VENDOR WISE STORE
	Route::get('/vendor-wise-store/{id}', [ProductController::class, 'getVendorStores'])->name('admin.getVendorStores');

    #Attributes
    Route::get('attributes', [AttributeController::class, 'index'])->name('admin.attributes.index');
    Route::get('attributes/create', [AttributeController::class, 'create'])->name('admin.attributes.create');
    Route::post('attributes', [AttributeController::class, 'store'])->name('admin.attributes.store');
    Route::get('attributes/{id}/edit', [AttributeController::class, 'edit'])->name('admin.attributes.edit');
    Route::put('attributes/{id}', [AttributeController::class, 'update'])->name('admin.attributes.update');
    Route::delete('attributes/{id}', [AttributeController::class, 'destroy'])->name('admin.attributes.destroy');

	#VENDOR STORE
	Route::get('/store', [VendorStoreController::class, 'show'])->name('admin.vendorstore.show');
	Route::get('/store/add', [VendorStoreController::class, 'add'])->name('admin.vendorstore.add');
	Route::post('/store/create', [VendorStoreController::class, 'create'])->name('admin.vendorstore.create');
	Route::get('/store/edit/{id}', [VendorStoreController::class, 'edit'])->name('admin.vendorstore.edit');
    Route::put('/store/update/{id}', [VendorStoreController::class, 'update'])->name('admin.vendorstore.update');
    Route::get('/store/{id}', [VendorStoreController::class, 'destroy'])->name('admin.vendorstore.destroy');
	
	#COUPONS
	Route::get('/coupon', [CouponController::class, 'index'])->name('admin.coupon.show');
	Route::get('/coupon/create', [CouponController::class, 'create'])->name('admin.coupon.create');
	Route::post('coupon/store', [CouponController::class, 'store'])->name('admin.coupon.store');
	Route::get('/coupon/edit/{id}', [CouponController::class, 'edit'])->name('admin.coupon.edit');
	Route::put('coupon/{id}', [CouponController::class, 'update'])->name('admin.coupon.update');
	Route::delete('coupon/{id}', [CouponController::class, 'destroy'])->name('admin.coupon.destroy');

    #Wallet
    Route::get('/wallet', [AdminController::class, 'wallet_balance'])->name('admin.wallet_balance');
    Route::get('/wallet/history/{userId}', [AdminController::class, 'wallet_history'])->name('admin.wallet_history');

    #Blog Category
    Route::resource('blog-category', BlogCategoryController::class);
    Route::post('/blog-category/update-status', [BlogCategoryController::class, 'updateStatus'])->name('blog-category.updateStatus');

    #Blog 
    Route::resource('blog', BlogController::class);
    Route::post('/blog/update-status', [BlogController::class, 'updateStatus'])->name('blog.updateStatus');

	#PRODUCTS EXPORT
	Route::get('/products/export', [ProductController::class, 'exportProducts'])->name('admin.exportProducts');
	
	#PRODUCTS IMPORT
	Route::post('/products/import', [ProductController::class, 'importProducts'])->name('admin.importProducts');
	
    #Home Page
    Route::prefix('page-settings')->group(function () {
        // Hero Section
        Route::get('/hero', [HomePageController::class, 'hero_section'])->name('admin.homepage.hero_section');
        Route::post('/hero', [HomePageController::class, 'hero_section_update'])->name('admin.homepage.hero_section_update');

        // Sections Heading
        Route::get('/sections-heading', [HomePageController::class, 'sections_heading'])->name('admin.homepage.sections_heading');
        Route::post('/sections-heading', [HomePageController::class, 'sections_heading_update'])->name('admin.homepage.sections_heading_update');
    });

    #CURRENCY
	Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');
	Route::get('/currency/add-currency', [CurrencyController::class, 'add_currency'])->name('currency.add_currency');
	Route::post('/currency/add_currency_action', [CurrencyController::class, 'add_currency_action'])->name('currency.add_currency_action');
	Route::get('/currency/edit/{id}', [CurrencyController::class, 'edit_currency'])->name('currency.edit_currency');
	Route::post('/currency/edit_currency_action', [CurrencyController::class, 'edit_currency_action'])->name('currency.edit_currency_action');
	Route::get('/currency/delete/{id}', [CurrencyController::class, 'delete_currency'])->name('currency.delete_currency');

    #sliders
    Route::resource('sliders', SlidersController::class);
    Route::post('/sliders/update-status', [SlidersController::class, 'updateStatus'])->name('sliders.updateStatus');

    #Brands
    Route::resource('brands', BrandController::class);
    Route::post('/brands/update-status', [BrandController::class, 'updateStatus'])->name('brands.updateStatus');
	
	#PRODUCT IMPORT BY WHOLESALE2 XML FEED URL
	Route::get('/wholesale2b/products/import', [ProductImportController::class, 'wholesale2bImport']);
	Route::post('/wholesale2b-products/import', [ProductImportController::class, 'import'])->name('products.import');
	
	#CJ DROPSHIPING
	Route::get('cj/token', [CjController::class, 'getToken'])->name('admin.cj.token');
    Route::post('cj/import', [CjController::class, 'store'])->name('admin.cj.import');
	
	#AUTO-DS DROPSHIPING
    Route::post('autods/import', [AutoDSController::class, 'store'])->name('admin.autods.import');

});