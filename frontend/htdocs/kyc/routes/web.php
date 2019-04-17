<?php
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return view('admin.auth.login');
});

Route::get('/not_found', function () {
    return view('notfound');
});

Route::get('/error', function () {
    return view('error');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/ksyrhwqnadlp/login', 'AdminAuth\LoginController@showLoginForm')->name('admin.login1');
    Route::post('/admin/login', 'AdminAuth\LoginController@loginAdmin')->name('admin.login');
    Route::post('/logout', 'AdminAuth\LoginController@logout')->name('logout');

    Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail')->name('password.request');
    Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset')->name('password.email');
    Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
    Route::get('/password/reset/{token}', 'AdminAuth\ResetPasswordController@showResetForm');
    
    Route::get('/admin/home', 'MasterController@index')->name('admin.home');
    Route::get('/referrals', 'MasterController@referrals')->name('admin.referrals');
    Route::get('/details', 'MasterController@details')->name('admin.details');
    Route::get('/change/password', 'MasterController@changePassword')->name('admin.change.password');
    Route::post('/update/password', 'MasterController@updatePassword')->name('admin.update.password');
    Route::get('/approval/{id}/{approval}', 'MasterController@approval')->name('admin.approval');
});

Auth::routes();
Route::get('/login', 'HomeController@frontindex')->name('frontend');
Route::post('/login1', 'Auth\LoginController@authenticate')->name('frontend.login');
Route::get('/logout', 'Auth\LoginController@front_logout')->name('frontend.logout');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/security', 'HomeController@security')->name('security');
Route::get('/contact', 'HomeController@contact')->name('contact');
Route::get('/kyc', 'HomeController@kyc')->name('kyc');
Route::get('/referral', 'HomeController@referral')->name('referral');
Route::get('/prevent/usage', 'HomeController@preventUsage')->name('prevent.usage');
Route::get('/ref', function (Request $request) {
    return redirect()->route('register', ['refid' => $request->refid]);
})->name('ref');
Route::post('/profile/update', 'HomeController@profileUpdate')->name('profile.update');
Route::post('/security/update', 'HomeController@securityUpdate')->name('security.update');
Route::post('/contact/update', 'HomeController@contactUpdate')->name('contact.update');
Route::post('/kyc/update', 'HomeController@kycUpdate')->name('kyc.update');
Route::get('/success', 'HomeController@success')->name('seccess');


/**
 * render storage images
 */
Route::get('storage/avatar/{image_name}', function($image_name){

    $storagePath = storage_path("app/public/avatar/{$image_name}");
    
    return response(File::get($storagePath), 200)->header('Content-Type', File::mimeType($storagePath));

});

Route::get('storage/proof/{image_name}', function($image_name){

    $storagePath = storage_path("app/public/proof/{$image_name}");
    
    return response(File::get($storagePath), 200)->header('Content-Type', File::mimeType($storagePath));

});

Route::get('test-email', function(Request $request){

    $User = App\User::where('email', $request->email)->first();
   
    Mail::to($User->email)->send(new App\Mail\RegistrationMail($User));

   /*  $res = Mail::send('email.register', ['User' => $User], function ($message) use($User) {
        $message->to('saikat@provenlogic.net');
        $message->subject('Thank you for registering with '.env('WEBSITE_NAME').' :)');
    });
 */
    //dd($res);
/* 
    Mail::raw('test', function ($m) use ($request) {
        $m->to('saikat@provenlogic.net', 'test')->subject('test');
    });  */


});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
