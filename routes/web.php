<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Auth\OtpController;
//use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\FirebaseAuthController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalPaymentController;
use App\Http\Controllers\EmisPaymentController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Auth\WebPasswordResetController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\FirebaseAppleAuthController;
use App\Http\Controllers\SocialLinkController;
use App\Http\Controllers\ProductComparisonController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PartnerCampaignViewController;
use App\Http\Controllers\PayoneerController;

Route::get('/', function () {
    //return redirect('login');
});

Route::middleware('logActivity')->group(function () {


Route::post('/checkout/shipping/select', 
    [CheckoutController::class, 'selectShipping']
)->name('checkout.shipping.select');


#PAYONEER GATEWAY
Route::get('/payoneer/pay/{order_id}', [PayoneerController::class, 'pay'])->name('payoneer.pay');
Route::get('/payoneer/success/{order_id}', [PayoneerController::class, 'success'])->name('payoneer.success');
Route::post('/payoneer/webhook', [PayoneerController::class, 'handle']);

Route::post('/auth/apple/firebase', [FirebaseAppleAuthController::class, 'appleSignIn']);

#COMPGAIN ROUTES
Route::get('/my-community-deal', [PartnerCampaignViewController::class, 'index'])->name('customer.partner.index');
Route::get('/my-community-deal/{id}', [PartnerCampaignViewController::class, 'show'])->name('customer.partner.show');

Route::middleware(['partnerAuth'])->prefix('partner')->name('partner.')->group(function () {
    
	Route::get('/checkout/{product}', [PartnerCampaignViewController::class, 'show'])->name('checkout');
    Route::post('/checkout/{product}', [PartnerCampaignViewController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{partnerOrder}', [PartnerCampaignViewController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{partnerOrder}', [PartnerCampaignViewController::class, 'cancelOrder'])->name('checkout.cancelOrder');
	
	#PAYPAL
	Route::get('/paypal/payment/{order}', [PaypalPaymentController::class, 'createPartnerOrderPayment'])->name('paypal.createPartnerOrderPayment');
	Route::get('/paypal/success/{order}', [PaypalPaymentController::class, 'successPartnerOrder'])->name('paypal.successPartnerOrder');
	Route::get('/paypal/cancel/{order}',  [PaypalPaymentController::class, 'cancelPartnerOrder'])->name('paypal.cancelPartnerOrder');
	
	#STRIPE
	Route::get('/stripe/payment/{order}', [StripePaymentController::class, 'createPartnerOrderPayment'])->name('stripe.createPartnerOrderPayment');
	Route::get('/stripe/success/{order}', [StripePaymentController::class, 'successPartnerOrder'])->name('stripe.successPartnerOrder');
	Route::get('/stripe/cancel/{order}', [StripePaymentController::class, 'cancelPartnerOrder'])->name('stripe.cancelPartnerOrder');
		
});


Route::get('/verify-otp', [OtpController::class, 'showOtpForm'])->name('verify.otp.form');
Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('verify.otp');

Route::get('/', [HomeController::class, 'index'])->name('home.index');

#SOCIAL LOGIN
/* Route::get('auth/google', [SocialController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback']);

Route::get('auth/facebook', [SocialController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [SocialController::class, 'handleFacebookCallback']); */

#FIREBASE LOGIN
Route::post('/firebase-login', [FirebaseAuthController::class, 'login']);

Route::get('/register', [HomeController::class, 'register'])->name('home.register');
Route::get('/register-as-vendor', [HomeController::class, 'registerAsVendor'])->name('home.registerAsVendor');
Route::post('/registerAsVendorAction', [HomeController::class, 'registerAsVendorAction'])->name('home.registerAsVendorAction');

#SWITCH CURRENCY
Route::get('/switch-currency/{code}', [HomeController::class, 'switchCurrency'])->name('currency.switch');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

#PAGES
Route::get('/about-us', [PagesController::class, 'about_us'])->name('page.about_us');
Route::get('/become-a-vendor', [PagesController::class, 'become_a_vendor'])->name('page.become_a_vendor');
Route::get('/contact-us', [PagesController::class, 'contact_us'])->name('page.contact_us');
Route::get('/faq', [PagesController::class, 'faq'])->name('page.faq');
Route::get('/error-404', [PagesController::class, 'error_404'])->name('page.error_404');


Route::get('/blog', [PagesController::class, 'blogList'])->name('page.blogs');
Route::get('/single-blog', [PagesController::class, 'single_blog'])->name('page.single_blog');
Route::get('/vendor', [PagesController::class, 'vendors'])->name('page.vendors');
Route::get('/stores', [PagesController::class, 'stores'])->name('page.stores');
Route::get('/store/product/{slug}', [PagesController::class, 'storesProduct'])->name('page.storesProduct');
Route::post('/store/submit-review', [PagesController::class, 'submitStoreReview'])->name('page.submitStoreReview');
Route::get('/career', [PagesController::class, 'career'])->name('page.career');
Route::get('/team-member', [PagesController::class, 'team_member'])->name('page.team_member');
Route::get('/affilate', [PagesController::class, 'affilate'])->name('page.affilate');
Route::get('/help', [PagesController::class, 'help'])->name('page.help');
Route::get('/privacy-policy', [PagesController::class, 'privacy_policy'])->name('page.privacy_policy');
Route::get('/term-conditions', [PagesController::class, 'term_conditions'])->name('page.term_conditions');
Route::get('/support-center', [PagesController::class, 'support_center'])->name('page.support_center');

//Route::get('/my-community-deal', [PagesController::class, 'myCommunityDeal'])->name('page.myCommunityDeal');
Route::get('/commodities', [PagesController::class, 'commodities'])->name('page.commodities');
Route::get('/agricultural-commodities', [PagesController::class, 'agriculturalCommodities'])->name('page.agriculturalCommodities');
Route::get('/minerals-materials', [PagesController::class, 'mineralsMaterials'])->name('page.mineralsMaterials');

#PRODUCTS
Route::get('/shop', [ProductController::class, 'shopList'])->name('product.shopList');
Route::get('/product/{slug}', [ProductController::class, 'product_details'])->name('product.product_details');
Route::get('/product/quick-view/{id}', [ProductController::class, 'quickView']);
Route::get('/search/suggestions', [ProductController::class, 'suggestions'])->name('search.suggestions');
Route::get('/search', [ProductController::class, 'searchPage'])->name('search.page');

#SEARCH
//Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
//Route::get('/search', [SearchController::class, 'searchPage'])->name('search.page');

#CHECKOUT
Route::get('/checkout/get-states/{country_name}', [CheckoutController::class, 'getStates']);
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
Route::get('/order-complete', [OrderController::class, 'order_complete'])->name('order.order_complete');

#PAYMENT
Route::get('/payment', [PaymentController::class, 'payments'])->name('stripe.payments');
Route::post('/api/stripe-pay', [PaymentController::class, 'stripePay'])->name('stripe.pay');
Route::post('/api/paypal/capture', [PaymentController::class, 'paypalCapture'])->name('paypal.capture');


//Route::post('/paypal/payment', [PaypalPaymentController::class, 'createPayment'])->name('paypal.payment');
Route::get('/paypal/payment/{order}', [PaypalPaymentController::class, 'createPayment'])->name('paypal.payment');
Route::get('/paypal/success/{order}', [PaypalPaymentController::class, 'success'])->name('paypal.success');
Route::get('/paypal/cancel/{order}', [PaypalPaymentController::class, 'cancel'])->name('paypal.cancel');

//Route::post('/stripe/payment', [StripePaymentController::class, 'createPayment'])->name('stripe.payment');
Route::get('/stripe/payment/{order}', [StripePaymentController::class, 'createPayment'])->name('stripe.payment');
Route::get('/stripe/success/{order}', [StripePaymentController::class, 'success'])->name('stripe.success');
Route::get('/stripe/cancel/{order}', [StripePaymentController::class, 'cancel'])->name('stripe.cancel');

#MAGIC LINK
Route::get('/magic-link', [MagicLinkController::class, 'magicLink'])->name('magic.form');
Route::post('/magic-link', [MagicLinkController::class, 'request'])->name('magic.link.send');
Route::get('/magic-login', [MagicLinkController::class, 'handleWeb'])->name('magic.login.web');

#USER RESET/FORGOT PASSWORD
Route::get('/user/forgot-password', [WebPasswordResetController::class, 'showForgotForm'])->name('user.password.request');
Route::post('/user/forgot-password', [WebPasswordResetController::class, 'sendOtp'])->name('user.password.email');
Route::get('/user/reset-password', [WebPasswordResetController::class, 'showResetForm'])->name('user.password.reset');
Route::post('/user/reset-password', [WebPasswordResetController::class, 'resetPassword'])->name('user.password.update');

#USER LOGIN
Route::get('/user/login', [CustomLoginController::class, 'showLoginForm'])->name('user.login');
Route::post('/user/login', [CustomLoginController::class, 'login'])->name('user.login');
Route::post('/user/checkout/login', [AuthenticatedSessionController::class, 'checkoutLogin'])->name('user.login.checkout');
Route::get('/user/verify-otp', [CustomLoginController::class, 'showOtpForm'])->name('user.verify.otp.form');
Route::post('/user/verify-otp', [CustomLoginController::class, 'verifyOtp'])->name('user.verify.otp');


#USER VERIFY AFTER REGISTERED
Route::get('/user/verifyotp', [RegisteredUserController::class, 'userShowOtpForm'])->name('user.userShowOtpForm');
Route::post('/user/verifyotp', [RegisteredUserController::class, 'userVerifyOtp'])->name('user.userVerifyOtp');

#REAL TIME FILED VAIDATE ROUTES
Route::post('/validate-login', [CustomLoginController::class, 'validateLoginField'])->name('validate.login');
//Route::post('/validate-register', [RegisteredUserController::class, 'validateRegisterField'])->name('validate.validateRegisterField');

// Comparison routes
Route::get('/compare', [ProductComparisonController::class, 'index'])->name('compare.index');
Route::post('/compare/{product}', [ProductComparisonController::class, 'store'])->name('compare.store');
Route::delete('/compare/{product}', [ProductComparisonController::class, 'destroy'])->name('compare.destroy');
Route::delete('/compare', [ProductComparisonController::class, 'clear'])->name('compare.clear');
Route::get('/compare/count', [ProductComparisonController::class, 'count'])->name('compare.count');
Route::get('/compare/check/{product}', [ProductComparisonController::class, 'check'])->name('compare.check');
	

Route::middleware('auth')->group(function () {
	
	#EMIS
	Route::get('/emis/pay/{order}', [EmisPaymentController::class, 'pay'])->name('emis.pay');
    Route::post('/emis/callback', [EmisPaymentController::class, 'callback'])->name('emis.callback');
    //Route::post('/emis/payment-success', [EmisPaymentController::class, 'payment_success'])->name('emis.payment.success');
    Route::get('/emis/payment-failed', [EmisPaymentController::class, 'payment_failed'])->name('emis.payment.failed');
	
	
	Route::post('/link-social-account', [SocialLinkController::class, 'linkAccount'])->name('link-social-account');

	/* Route::get('/checkout/get-states/{country_name}', [CheckoutController::class, 'getStates']);
	Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout.index');
	Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder'); */
	//Route::get('/order-complete', [OrderController::class, 'order_complete'])->name('order.order_complete');
	
	//Route::get('/track-order', [OrderController::class, 'track_order'])->name('order.track_order');
	Route::get('/track-order', [PagesController::class, 'track_order'])->name('order.track_order');
	Route::get('/order-view', [OrderController::class, 'order_view'])->name('order.order_view');
	Route::get('/customer-order/{id}/invoice', [OrderController::class, 'downloadInvoice'])->name('customer.order.invoice.download');
	
    
	#ACCOUNT PARTNER ORDER
	Route::get('/account/partner-orders', [AccountController::class, 'partnerOrders'])->name('account.partnerOrders');
	Route::get('/partner-customer-order/{id}/invoice', [OrderController::class, 'downloadPartnerInvoice'])->name('partner.customer.order.invoice.download');
	Route::get('/account/partner-order-details/{id}', [AccountController::class, 'partnerOrderDetails'])->name('account.partnerOrderDetails');
	Route::get('/cancel/partner-order/{order_number}', [AccountController::class, 'CancelPartnerOrders'])->name('account.CancelPartnerOrders');
	Route::post('/cancel/partner_order_action', [AccountController::class, 'partner_order_action'])->name('account.partner_order_action');
	
	Route::get('/my-account', [AccountController::class, 'my_account'])->name('account.my_account');
	Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
	Route::get('/cancel/order/{order_number}', [AccountController::class, 'CancelOrders'])->name('account.CancelOrders');
	Route::post('/cancel/order_action', [AccountController::class, 'order_action'])->name('account.order_action');
	Route::get('/account/re-order/{id}', [AccountController::class, 'reOrders'])->name('account.reOrders');
	Route::get('/account/order-details/{id}', [AccountController::class, 'orderDetails'])->name('account.orderDetails');
	
	Route::get('/account/details', [AccountController::class, 'accountDetails'])->name('account.accountDetails');
	Route::get('/account/change-password', [AccountController::class, 'changePassword'])->name('account.changePassword');
	Route::post('/account/changePasswordAction', [AccountController::class, 'changePasswordAction'])->name('account.changePasswordAction');
	Route::get('/account/downloads', [AccountController::class, 'downloads'])->name('account.downloads');
	Route::post('/account/update_account_action', [AccountController::class, 'update_account_action'])->name('account.update_account_action');
	
	#ACCOUNT LINKED WITH LOGIN METHOD
	Route::get('/account/link-accounts', [AccountController::class, 'link_accounts'])->name('account.link_accounts');
    Route::get('login/add/{method}', [AccountController::class, 'showAddLoginForm']) ->name('user.addLogin');
    Route::post('login/add', [AccountController::class, 'storeLogin'])->name('user.storeLogin');
	Route::post('login/{id}/switch-primary', [AccountController::class, 'switchPrimary'])->name('user.switchPrimary');
    Route::delete('login/{id}/unlink', [AccountController::class, 'unlinkLogin'])->name('user.unlinkLogin');
	Route::post('/user/send-otp', [AccountController::class, 'sendOtp'])->name('user.sendOtp');
	
	#RECENT SEARCH HISTORY
	Route::get('/account/search-history', [AccountController::class, 'searchHistory'])->name('account.searchHistory');
	
	#ACCOUNT DEACTIVATE ROUTES
	Route::get('/account/deactivate-account', [AccountController::class, 'deactivateAccount'])->name('account.deactivateAccount');
	
	#ACCOUNT DELETE PERMANENTLY ROUTES
	Route::get('/account/delete-account-permanently', [AccountController::class, 'deleteAccountPermanently'])->name('account.deleteAccountPermanently');
	
	
	#USER WALLET
	Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/add', [WalletController::class, 'showAddForm'])->name('wallet.showAddForm');
    Route::post('/wallet/add', [WalletController::class, 'addBalance'])->name('wallet.add');

    #STRIPE
    Route::get('/wallet/stripe/success', [WalletController::class, 'stripeSuccess'])->name('wallet.stripe.success');
    Route::get('/wallet/stripe/cancel', [WalletController::class, 'stripeCancel'])->name('wallet.stripe.cancel');

    #PAYPAL
    Route::get('/wallet/paypal/success', [WalletController::class, 'paypalSuccess'])->name('wallet.paypal.success');
    Route::get('/wallet/paypal/cancel', [WalletController::class, 'paypalCancel'])->name('wallet.paypal.cancel');
	
	
	#ADDRESS
	Route::get('/account/address', [AccountController::class, 'userAddress'])->name('account.userAddress');
	Route::get('/account/edit/address/{id}', [AccountController::class, 'editAddress'])->name('account.editAddress');
	Route::post('/account/address/update_address_action', [AccountController::class, 'update_address_action'])->name('account.update_address_action');
	
	Route::post('/account/address/update_action', [AccountController::class, 'update_action'])->name('account.update_action');
	
	Route::get('/account/add-new-address', [AccountController::class, 'addAddress'])->name('account.addAddress');
	Route::post('/account/add_address_action', [AccountController::class, 'add_address_action'])->name('account.add_address_action');
	
	Route::get('/account/delete/address/{id}', [AccountController::class, 'deleteAddress'])->name('account.deleteAddress');
	
	Route::get('/account/wishlist', [AccountController::class, 'wishlist'])->name('account.wishlist');
	Route::post('/account/wishlist/add', [AccountController::class, 'addWishlist'])->name('account.addWishlist');
    Route::delete('/account/wishlist/remove/{id}', [AccountController::class, 'removeWishlist'])->name('account.removeWishlist');
	
	Route::post('/submit-review', [HomeController::class, 'review_submit'])->name('productDetails.review_submit');
	
});

#ADD TO CART
Route::get('/cart', [CartController::class, 'cart'])->name('products.cart');
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/cart/get', [CartController::class, 'getCart']);
Route::post('/cart/remove', [CartController::class, 'remove']);
Route::post('/cart/removeCartProduct', [CartController::class, 'removeCartProduct'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::post('/cart/update-cart', [CartController::class, 'updateQuantityByCartPage'])->name('cart.updateCart');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

#APPLY COUPON
Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
Route::get('/cart/removeCoupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');

});

Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('event:clear');
    return 'All Laravel caches cleared and rebuilt successfully!';
});

require __DIR__.'/auth.php';
require __DIR__.'/vendor.php';
require __DIR__.'/admin.php';