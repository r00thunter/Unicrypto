<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::GET('/', function () {
    return view('auth.login');
});

Auth::routes();
Route::group(['middleware' => ['auth']], function() {
Route::GET('/home', 'HomeController@index')->name('home');
// Route::GET('/page-content/open-orders', 'PageContentController@open_orders')->name('order');
// Route::GET('/page-content/open-orders/{id}', 'PageContentController@openOrders')->name('order');
// Route::GET('/page-content/currency-address', 'PageContentController@currency_address')->name('currency');
// Route::GET('/page-content/fiat-wallet', 'PageContentController@fiat_wallet')->name('fiat');
// Route::GET('/page-content/index', 'PageContentController@index')->name('index');
// Route::GET('/page-content/login1', 'PageContentController@login')->name('login1');
// Route::GET('/page-content/forgot-password', 'PageContentController@forgot_password')->name('forgot_password');
// Route::GET('/page-content/register', 'PageContentController@register')->name('register');
// Route::GET('/page-content/order-history', 'PageContentController@order_history')->name('order-history');
// Route::GET('/page-content/profile', 'PageContentController@profile')->name('profile');
// Route::GET('/page-content/trade-history', 'PageContentController@trade_history')->name('trade-history');
// Route::GET('/page-content/deposit', 'PageContentController@deposite')->name('deposite');
// Route::GET('/page-content/withdraw', 'PageContentController@withdraw')->name('withdraw');
// Route::GET('/page-content/bank-account', 'PageContentController@bank_account')->name('bank-account');
// Route::GET('/page-content/profile-setting', 'PageContentController@profile_settings')->name('profile-setting');
// Language
Route::GET('/language', 'LanguageController@index')->name('language');
Route::POST('/language-add', 'LanguageController@createLanguage')->name('language.add');
Route::POST('/language-edit', 'LanguageController@editLanguage')->name('language.edit');
Route::POST('/language-delete', 'LanguageController@deleteLanguage')->name('language.delete');

// Page
Route::GET('/page', 'PageController@index')->name('page');
Route::POST('/page-add', 'PageController@createPage')->name('page.add');
Route::POST('/page-edit', 'PageController@editPage')->name('page.edit');
Route::POST('/page-delete', 'PageController@deletePage')->name('page.delete');
Route::GET('/page-content/{id}', 'PageController@createPage1')->name('page.content.add');
Route::GET('/menu', 'MenuController@index')->name('menu');
Route::POST('/menu-add', 'MenuController@createMenu')->name('menu.add');
Route::POST('/menu-edit', 'MenuController@editMenu')->name('menu.edit');
Route::POST('/menu-delete', 'MenuController@deleteMenu')->name('menu.delete');

Route::GET('/footer', 'FooterController@index')->name('footer');
Route::POST('/footer-add', 'FooterController@createFooter')->name('footer.add');
Route::POST('/footer-edit', 'FooterController@editFooter')->name('footer.edit');
Route::POST('/footer-delete', 'FooterController@deleteFooter')->name('footer.delete');

Route::GET('/media', 'MediaController@index')->name('media');
Route::POST('/media-add', 'MediaController@createMedia')->name('media.add');
Route::POST('/media-edit', 'MediaController@editMedia')->name('media.edit');
Route::POST('/media-delete', 'MediaController@deleteMedia')->name('media.delete');



Route::POST('/page-content/open-orders/store', 'PageContentController@openOrderStore')->name('pagecontent.openorder.store');
Route::POST('/page-content/open-orders/update', 'PageContentController@openOrderUpdate')->name('pagecontent.openorder.update');
Route::POST('/page-content/deposit/store', 'PageContentController@depositStore')->name('pagecontent.deposit.store');
Route::POST('/page-content/withdraw/store', 'PageContentController@withdrawStore')->name('pagecontent.withdraw.store');
Route::POST('/page-content/withdraw/store', 'PageContentController@profileStore')->name('pagecontent.profile.store');
Route::POST('/page-content/order-history/store', 'PageContentController@order-history_Store')->name('pagecontent.order-history.store');
Route::POST('/page-content/profile-setting/store', 'PageContentController@profile-setting_Store')->name('pagecontent.profile-setting.store');
Route::POST('/page-content/currency-address/store', 'PageContentController@currency_address_Store')->name('pagecontent.currency-address.store');
Route::POST('/page-content/fiat-wallet/store', 'PageContentController@fiat_wallet_Store')->name('pagecontent.fiat-wallet.store');

Route::POST('/page-content/forgot-password/store', 'PageContentController@forgot_password_Store')->name('pagecontent.forgot-password.store');





// page content display

Route::GET('/page-content/{id}', 'PageContentController@showPageContent')->name('show.page.ontent');





// Page content Store


Route::POST('/login-content-store', 'PageContentController@loginContentStore')->name('login.content.store');
Route::POST('/login-content-edit', 'PageContentController@loginContentEdit')->name('login.content.edit');

Route::POST('/register-content-store', 'PageContentController@registerContentStore')->name('register.content.store');
Route::POST('/register-content-edit', 'PageContentController@registerContentEdit')->name('register.content.edit');

Route::POST('/forgot-pass-content-store', 'PageContentController@forgotContentStore')->name('forgot.pass.content.store');
Route::POST('/forgot-pass-content-edit', 'PageContentController@forgotContentEdit')->name('forgot.pass.content.edit');

Route::POST('/open-orders-content-store', 'PageContentController@openorderContentStore')->name('open.orders.content.store');
Route::POST('/open-orders-content-edit', 'PageContentController@openorderContentEdit')->name('open.orders.content.edit');

Route::POST('/trade-history-content-store', 'PageContentController@tradehistoryContentStore')->name('trade.history.content.store');
Route::POST('/trade-history-content-edit', 'PageContentController@tradehistoryContentEdit')->name('trade.history.content.edit');

Route::POST('/order-history-content-store', 'PageContentController@orderhistoryContentStore')->name('order.history.content.store');
Route::POST('/order-history-content-edit', 'PageContentController@orderhistoryContentEdit')->name('order.history.content.edit');

Route::POST('/crypto-address-content-store', 'PageContentController@cryptoaddressContentStore')->name('crypto.address.content.store');
Route::POST('/crypto-address-content-edit', 'PageContentController@cryptoaddressContentEdit')->name('crypto.address.content.edit');

Route::POST('/deposit-content-store', 'PageContentController@depositContentStore')->name('deposit.content.store');
Route::POST('/deposit-content-edit', 'PageContentController@depositContentEdit')->name('deposit.content.edit');

Route::POST('/withdraw-content-store', 'PageContentController@withdrawContentStore')->name('withdraw.content.store');
Route::POST('/withdraw-content-edit', 'PageContentController@withdrawContentEdit')->name('withdraw.content.edit');

Route::POST('/profile-content-store', 'PageContentController@profileContentStore')->name('profile.content.store');
Route::POST('/profile-content-edit', 'PageContentController@profileContentEdit')->name('profile.content.edit');

Route::POST('/security-content-store', 'PageContentController@securityContentStore')->name('security.content.store');
Route::POST('/security-content-edit', 'PageContentController@securityContentEdit')->name('security.content.edit');

Route::POST('/balance-content-store', 'PageContentController@balanceContentStore')->name('balance.content.store');
Route::POST('/balance-content-edit', 'PageContentController@balanceContentEdit')->name('balance.content.edit');

Route::POST('/crypto-wallet-content-store', 'PageContentController@cryptowalletContentStore')->name('crypto.wallet.content.store');
Route::POST('/crypto-wallet-content-edit', 'PageContentController@cryptowalletContentEdit')->name('crypto.wallet.content.edit');

Route::POST('/bank-account-content-store', 'PageContentController@bankaccountContentStore')->name('bank.account.content.store');
Route::POST('/bank-account-content-edit', 'PageContentController@bankaccountContentEdit')->name('bank.account.content.edit');

Route::POST('/home-content-store', 'PageContentController@homeContentStore')->name('home.content.store');
Route::POST('/home-content-edit', 'PageContentController@homeContentEdit')->name('home.content.edit');

Route::POST('/aboutus-content-store', 'PageContentController@aboutusContentStore')->name('aboutus.content.store');
Route::POST('/aboutus-content-edit', 'PageContentController@aboutusContentEdit')->name('aboutus.content.edit');

Route::POST('/howit-content-store', 'PageContentController@howitContentStore')->name('howit.content.store');
Route::POST('/howit-content-edit', 'PageContentController@howitContentEdit')->name('howit.content.edit');

Route::POST('/terms-content-store', 'PageContentController@termsContentStore')->name('terms.content.store');
Route::POST('/terms-content-edit', 'PageContentController@termsContentEdit')->name('terms.content.edit');

Route::POST('/api-content-store', 'PageContentController@apiContentStore')->name('api.content.store');
Route::POST('/api-content-edit', 'PageContentController@apiContentEdit')->name('api.content.edit');


Route::POST('/simple-trade-content-store', 'PageContentController@simpletradeContentStore')->name('simple.trade.content.store');
Route::POST('/simple-trade-content-edit', 'PageContentController@simpletradeContentEdit')->name('simple.trade.content.edit');

});