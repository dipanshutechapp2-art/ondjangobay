<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Middleware\CorsMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SocialLoginController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\PayPalController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Api\FirebaseController;
use App\Http\Controllers\Api\BiometricController;
use App\Http\Controllers\Api\TrustedDeviceController; 
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\CjController;
use App\Http\Controllers\Api\ProductComparisonController;
use App\Http\Controllers\Api\PartnerCampaignApiController;
use App\Http\Controllers\Api\AutoDSController;
use App\Http\Controllers\Api\EmisPaymentController;
use App\Http\Controllers\Api\MyUSWebhookController;
use App\Imports\PartnerProductImport;

Route::middleware([CorsMiddleware::class])->group(function () {
	
	#MYUS ENDPOINT ROUTES
	//Route::post('/myus/push/order-status', [MyUSWebhookController::class, 'orderStatus']);
	//Route::post('/myus/push/shipment', [MyUSWebhookController::class, 'shipmentUpdate']);
	
	Route::prefix('myus/push')->group(function () {
		Route::post('/receive-package', [MyUSWebhookController::class, 'receivePackage']);
		Route::post('/maintain-shipment-status', [MyUSWebhookController::class, 'maintainShipmentStatus']);
		Route::post('/maintain-package-status', [MyUSWebhookController::class, 'maintainPackageStatus']);
	});

	
	Route::get('/testing', [ApiController::class, 'testing'])->name('testing');
	Route::get('/get-products-by-category', [ApiController::class, 'getProductsByCategory']);
	Route::get('/products/by-category', [ApiController::class, 'getProductByCategoryID']);
	Route::get('/brands', [ApiController::class, 'brands']);
	Route::post('/register', [ApiController::class, 'register'])->name('register');
	
	Route::post('/login', [ApiController::class, 'login'])->name('login');
	Route::post('/login-verify', [ApiController::class, 'verifyLoginOtp'])->name('verifyLoginOtp');
	
	Route::get('/categories', [ApiController::class, 'categories'])->name('categories');
	Route::get('/get-parent-categories', [ApiController::class, 'getParentCategories'])->name('getParentCategories');
	//Route::get('/products', [ApiController::class, 'products'])->name('products');
	Route::get('/product_details/{id}', [ApiController::class, 'product_details'])->name('product_details');
	Route::get('/stores', [ApiController::class, 'stores'])->name('stores');
	Route::get('/sliders', [ApiController::class, 'sliders'])->name('sliders');
	Route::get('/getCategories', [ApiController::class, 'getCategories']);	
	
	#GOOGLE & FACEBOOK LOGIN
	Route::post('/login/google', [SocialLoginController::class, 'loginWithGoogle']);
    Route::post('/login/facebook', [SocialLoginController::class, 'loginWithFacebook']);
    
    #FORGOT PASSWORD
    Route::post('/forgot-password', [ApiController::class, 'sendOtp']);
    Route::post('/reset-password', [ApiController::class, 'resetPasswordWithOtp']);
    
    #CURRENCY
    Route::get('/currencies', [ApiController::class, 'getCurrency']);
	
	#CONFIG API
	Route::get('/config', [ApiController::class, 'getConfigContent']);
	
	#MAGIC LINK
    Route::post('/magic-link', [MagicLinkController::class, 'requestApi'])->name('api.magic.link.send');
    Route::get('/magic-login', [MagicLinkController::class, 'handleApi'])->name('api.magic.login');
	
	#BIOMATRIC LOGIN
	Route::post('/biometric/login', [BiometricController::class, 'login']);
	
	#GET CONFIG CAPTCHA
	Route::get('/config/recaptcha', [ApiController::class, 'getConfigCaptcha']);
	Route::post('/validate/recaptcha', [ApiController::class, 'validateRecaptcha']);
	
	#SUBSCRIBE NEWSLETTER
	Route::post('/newsletter/subscribe', [ApiController::class, 'addSubscribe']);
	
	Route::group(['middleware' => ['auth:sanctum','logActivityApi']], function () {
	    
		#AUTO-DS
		Route::get('vendor/autods/connect', [AutoDSController::class, 'redirectApi'])->name('vendor.autods.connect');
		Route::get('vendor/autods/stores', [AutoDSController::class, 'getAutoDsStore']);
		Route::post('vendor/autods/product-import', [AutoDSController::class, 'autoDsProductImport']);
		
		#EMIS GATEWAY
		Route::post('/emis/pay', [EmisPaymentController::class, 'pay']);
		Route::post('/emis/callback', [EmisPaymentController::class, 'callback'])->name('emis.callback');

		
		
		#Partner Compaign API Routes
		Route::prefix('partner')->group(function () {
			Route::get('/campaigns', [PartnerCampaignApiController::class, 'index']);
			Route::get('/product/{product}', [PartnerCampaignApiController::class, 'show']);
			Route::post('/product/checkout/{product}', [PartnerCampaignApiController::class, 'process']);
			
			#PAYPAL
			Route::post('/paypal/create/{order}', [PayPalController::class, 'paypalCreateCompaignOrder']);
			Route::get('/paypal/success/{order}', [PayPalController::class, 'paypalSuccessCompaignOrder']);
			Route::get('/paypal/cancel/{order}', [PayPalController::class, 'paypalCancelCompaignOrder']);

			#STRIPE
			Route::post('/stripe/create/{order}', [StripeController::class, 'stripeCreate']);
			Route::get('/stripe/success/{order}', [StripeController::class, 'stripeSuccess']);
			Route::get('/stripe/cancel/{order}', [StripeController::class, 'stripeCancel']);
			
			#VENDOR
			Route::get('/vendor/products', [PartnerCampaignApiController::class, 'products']);
			Route::get('/vendor/products/download-sample-template', [PartnerCampaignApiController::class, 'downloadTemplate']);
			Route::post('/vendor/products/upload', [PartnerCampaignApiController::class, 'upload']);
			Route::get('/vendor/products/import-errors', [PartnerCampaignApiController::class,'importErrors']);
			Route::post('/vendor/products/clear-import-errors', [PartnerCampaignApiController::class,'clearImportErrors']);
			
			#PARTNERS ORDERS
			Route::get('/vendor/product-orders', [PartnerCampaignApiController::class,'partnerVendorOrders']);
			Route::get('/user/product-orders', [PartnerCampaignApiController::class,'partnerUserOrders']);
			
			Route::get('/order/order-details/{order_id}', [PartnerCampaignApiController::class,'partnerOrderDetails']);
			
			Route::post('/order/change-order-status', [PartnerCampaignApiController::class,'updateorderStatus']);
			Route::post('/order/change-payment-status', [PartnerCampaignApiController::class,'updatePaymentStatus']);
			Route::get('/order/download-invoice/{order_id}', [PartnerCampaignApiController::class,'downloadInvoice']);
			
		});

	    #CANCEL ORDER
	    Route::post('/cancel-order', [ApiController::class, 'cancel_order_action']);
	    
	    #RE-ORDER
	    Route::post('/account/re-order', [ApiController::class, 'reOrderItem']);
	    
	    #ACCOUNT DEACTIVATE
	    Route::post('/deactivate-account', [ApiController::class, 'deactivateAccount']);
	    
	    #ACCOUNT PERMANENT DELETE
	    Route::post('/delete-permanent-account', [ApiController::class, 'deletePermanentAccount']);
	    
	    
	    #Product Comparison API Routes
		Route::prefix('compare')->group(function () {
			Route::get('/', [ProductComparisonController::class, 'index']);
			Route::post('/{product}', [ProductComparisonController::class, 'store']);
			Route::delete('/{product}', [ProductComparisonController::class, 'destroy']);
			Route::delete('/', [ProductComparisonController::class, 'clear']);
			Route::get('/count', [ProductComparisonController::class, 'count']);
			Route::get('/check/{product}', [ProductComparisonController::class, 'check']);
		});
		
	    Route::get('/products', [ApiController::class, 'products'])->name('products');
	    
	    #USER SEARCH HISTORY
	    Route::get('/user-search-history', [ApiController::class, 'getUserSearchHistory']);
	    
	    #PAYMENT GATEWAY LIST
	    Route::get('/get-payment-gateway-list', [ApiController::class, 'getPaymentGatewayList'])->name('api.getPaymentGatewayList');
	    
	    #USER WALLET
	    Route::get('/get-user-wallet-balance', [WalletController::class, 'getUserWalletBalance'])->name('api.user.getUserWalletBalance');
	    Route::get('/get-user-wallet-history', [WalletController::class, 'getUserWalletHistory'])->name('api.user.getUserWalletHistory');
	    Route::post('/wallet/add-balance', [WalletController::class, 'addBalance']);
	    Route::post('/wallet/stripe/success', [WalletController::class, 'stripeWebhook']); 
	    Route::post('/wallet/paypal/success', [WalletController::class, 'paypalWebhook']);
	    
	    #LOGIN METHOD 
        Route::get('/login-methods', [ApiController::class, 'getLoginMethods'])->name('api.login.getLoginMethods');
        Route::get('/get-linked-account', [ApiController::class, 'getUserLinkedAccount'])->name('api.login.getUserLinkedAccount');
        //Route::post('/add-link-account', [ApiController::class, 'AddLinkAccount'])->name('api.login.AddLinkAccount'); 
        Route::post('/add-link-account', [ApiController::class, 'sendOtpForLoginLinking'])->name('api.login.sendOtpForLoginLinking'); 
        Route::post('/verify-login-method', [ApiController::class, 'verifyAndStoreLoginMethod'])->name('api.login.verifyAndStoreLoginMethod');
        Route::post('/verify-social-login-method', [ApiController::class, 'verifySocialAccount'])->name('api.login.verifySocialAccount');
	    Route::get('/set-primary-login-method/{id}', [ApiController::class, 'switchPrimaryLoginMethod'])->name('api.login.switchPrimaryLoginMethod');
	    Route::get('/delete-user-login-method/{id}', [ApiController::class, 'deleteUserLoginMethod'])->name('api.login.deleteUserLoginMethod');
	    
	    #ENABLE BIOMATRIC
	    Route::post('/biometric/enable', [BiometricController::class, 'enable']);
	    
		#TRUSTED DEVICE FOR FUTURE LOGIN
		Route::post('/device/register', [TrustedDeviceController::class, 'registerDevice']);
		Route::post('/device/check', [TrustedDeviceController::class, 'checkDevice']);
		
	    #FIREBASE ROUTES
		Route::post('/save-device-token', [FirebaseController::class, 'saveDeviceToken']);
		Route::post('/send-notification', [FirebaseController::class, 'sendNotification']);
		Route::get('/user-notifications', [FirebaseController::class, 'getNotifications']);
		Route::post('/notifications/read', [FirebaseController::class, 'markAsRead']);
		Route::post('/notifications/delete', [FirebaseController::class, 'deleteNotification']);
		Route::post('/verify-id-token', [FirebaseController::class, 'verifyIdToken']);
		
		#APPLY COUPON FOR USERS
	    Route::get('/get-user-coupons', [ApiController::class, 'getUserCoupons']);
	    Route::post('/apply-coupon', [ApiController::class, 'applyCoupon']);
	    
	    #PAYPAL
	    Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
        Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);
        
        #STRIPE
        Route::post('/stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
        Route::post('/stripe/capture-payment', [StripeController::class, 'capturePayment']);
        
        #ORDER UPDATE API AFTER PAYMENT COMPLETE
        Route::post('/update-order-status', [ApiController::class, 'orderStatusUpdate']);
	    
	    Route::post('/verifyUserOtp', [ApiController::class, 'verifyUserOtp'])->name('verifyUserOtp');
	    Route::post('/resend-otp', [ApiController::class, 'resendOtp'])->name('resendOtp');
	    
	    Route::post('/change-password', [ApiController::class, 'changePassword'])->name('changePassword');
	    
	    Route::post('/update-currency', [ApiController::class, 'updateCurrency'])->name('updateCurrency');
	    
		Route::post('/editProfile', [ApiController::class, 'editProfile'])->name('editProfile');
		Route::get('/me', [ApiController::class, 'me'])->name('me');
		Route::post('/logout', [ApiController::class, 'logout'])->name('logout');
		
		#REVIEWS 
		Route::post('/reviews', [ApiController::class, 'getReviews'])->name('getReviews');
		Route::post('/total_reviews', [ApiController::class, 'total_reviews'])->name('total_reviews');
		Route::post('/review_react', [ApiController::class, 'review_react'])->name('review_react');
		Route::post('/review_details', [ApiController::class, 'getReviewDetails'])->name('getReviewDetails');
		Route::post('/review_submit', [ApiController::class, 'review_submit'])->name('review_submit');
		Route::post('/review_update', [ApiController::class, 'review_update'])->name('review_update');
		Route::post('/review_delete', [ApiController::class, 'review_delete'])->name('review_delete');
		
		
		Route::post('/checkout/place-order', [ApiController::class, 'placeOrder'])->name('checkout.placeOrder');
		Route::post('/cart/add', [ApiController::class, 'addToCart']);
		Route::post('/cart/increaseQty', [ApiController::class, 'increaseQty']);
		Route::get('/cart', [ApiController::class, 'getCart']);
		Route::delete('/cart/delete/{id}', [ApiController::class, 'deleteFromCart']);
		Route::post('/cart/decrease/Quantity', [ApiController::class, 'decreaseQuantity']);
		Route::post('/wishlist/add', [ApiController::class, 'addToWishlist']);
		Route::get('/wishlist', [ApiController::class, 'getWishlist']);
		Route::delete('/wishlist/remove/{id}', [ApiController::class, 'removeFromWishlist']);

		#vendor Api's
		Route::get('/vendor/products', [ApiController::class, 'vendor_products'])->name('vendor_products');
		Route::post('/vendor/products/create', [ApiController::class, 'product_add'])->name('product_add');
		Route::post('/vendor/product/Update/{id}', [ApiController::class, 'product_Update'])->name('product_Update');
		Route::get('/vendor/product/delete/{id}', [ApiController::class, 'product_delete'])->name('product_delete');
		Route::post('/vendor/product/status/update', [ApiController::class, 'productupdateStatus'])->name('productupdateStatus');
		
		Route::post('/vendor/store/create', [ApiController::class, 'create_store'])->name('create_store');
		Route::get('/vendor/store', [ApiController::class, 'vendor_stores'])->name('vendor_stores');
		Route::get('/vendor/store/details/{id}', [ApiController::class, 'vendor_store_details'])->name('vendor_store_details');
		Route::post('/vendor/store/update/{id}', [ApiController::class, 'update_store'])->name('update_store');
		Route::delete('/vendor/store/delete/{id}', [ApiController::class, 'delete_store'])->name('delete_store');

		Route::get('/brands', [ApiController::class, 'brands'])->name('brands');
		Route::post('/vendor/attribute/create', [ApiController::class, 'attribute_create'])->name('attribute_create');
		Route::get('/vendor/attributes', [ApiController::class, 'attributes'])->name('attributes');
		Route::get('/vendor/attribute/details/{id}', [ApiController::class, 'attribute_details'])->name('attribute_details');
		Route::post('/vendor/attribute/update/{id}', [ApiController::class, 'update_attribute'])->name('update_attribute');
		// Route::delete('/vendor/attribute/delete/{id}', [ApiController::class, 'delete_store'])->name('delete_store');
		
		#COUPONS
		Route::get('/vendor/get-coupons', [ApiController::class, 'getVendorCoupons']);
		Route::post('/vendor/create-coupon', [ApiController::class, 'createVendorCoupons']);
		Route::get('/vendor/edit-coupon/{id}', [ApiController::class, 'editVendorCoupons']);
		Route::post('/vendor/update-coupon', [ApiController::class, 'updateVendorCoupons']);
		Route::get('/vendor/delete-coupon/{id}', [ApiController::class, 'deleteVendorCoupons']);
		
		#CJ DROPSHIPING
		Route::get('/vendor/cj/token', [CjController::class, 'getCjToken'])->name('vendor.cj.token');
        Route::post('/vendor/cj/import', [CjController::class, 'cjStore'])->name('vendor.cj.import');
		
		#DROP SHIPPING PRODUCT IMPORT FROM WHOLESALE2
		Route::post('/vendor/wholesale2b/product-import', [ApiController::class, 'productImport']);
		Route::get('/vendor/getVendorStores', [ApiController::class, 'getVendorStores']);
		
		Route::get('/vendor/dashboard', [ApiController::class, 'vendor_dashboard'])->name('vendor_dashboard');
        
        #UPDATE FCM TOKEN
		Route::post('/update-fcm-token', [ApiController::class, 'updateFcmToken'])->name('updateFcmToken');
        
        #STORE STATUS
		Route::post('/vendor/store-status/', [ApiController::class, 'vendor_store_status'])->name('vendor_store_status');
		
		#ORDER STATUS
		Route::post('/vendor/order-status/', [ApiController::class, 'vendor_order_status'])->name('vendor_order_status');
		
		#CHANGE ORDER PAYMENT STATUS
		Route::post('/vendor/order-payment-status/', [ApiController::class, 'vendor_order_payment_status'])->name('vendor_order_payment_status');
		
		#ORDER LISTS
		Route::get('/vendor/orders/', [ApiController::class, 'vendor_orders'])->name('vendor_orders');
		
		#VENDOR ORDER DETAILS
		Route::get('/vendor/order-details/{id}', [ApiController::class, 'vendor_orders_details'])->name('vendor_orders_details');
        
		#ADDRESS
		Route::get('/account/address', [ApiController::class, 'userAddress'])->name('account.userAddress');

		Route::get('/account/edit/address/{id}', [ApiController::class, 'editAddress'])->name('account.editAddress');

		Route::post('/account/address/update_address_action', [ApiController::class, 'update_address_action'])->name('account.update_address_action');

		Route::post('/account/address/update_action', [ApiController::class, 'update_action'])->name('account.update_action');

		Route::get('/account/get_country_state', [ApiController::class, 'get_countryandstate'])->name('account.get_countryandstate');

		Route::post('/account/add_address_action', [ApiController::class, 'add_address_action'])->name('account.add_address_action');

		Route::get('/account/delete/address/{id}', [ApiController::class, 'deleteAddress'])->name('account.deleteAddress');

		Route::get('/account/orders', [ApiController::class, 'orders'])->name('account.orders');
		Route::get('/account/orders/details/{id}', [ApiController::class, 'orders_details'])->name('account.orders_details');

	});

});

