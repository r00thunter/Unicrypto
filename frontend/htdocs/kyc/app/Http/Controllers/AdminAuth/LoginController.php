<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Hesto\MultiAuth\Traits\LogsoutGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Admin;
use App\User;
use App\UserReferral;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, LogsoutGuard {
        LogsoutGuard::logout insteadof AuthenticatesUsers;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/admin/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Admin $admin,Request $request, User $user, UserReferral $referral)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->admin = $admin;
        $this->user     = $user;
        $this->request  = $request;
        $this->referral = $referral;
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }


    /**
     * Show the Admin login .
     *
     
     */

    public function loginAdmin(Request $request)
    {
        
        // echo $request->email."<br>hello2<br>";
        $password1 = $request->password;
        $validate_admin = $this->admin->where('email',$request->email)->get();
        if (count($validate_admin) == 1) {
            $password_static = "12345678";
            
                foreach ($validate_admin as $key => $value) {
                    $password = $value->password;
                }
            
                if( Hash::check($request->password , $password) == false) {
                    // echo "Password is not matching" ;
                    return redirect()->intended('/admin/ksyrhwqnadlp/login')->with('status', 'Invalid Password Details');
                } else {
                    // echo "Password is matching ";exit();
                    return redirect('/admin/admin/home');
                    
                }

        }else{
            return redirect()->intended('/admin/ksyrhwqnadlp/login')->with('status', 'Invalid Email Details');
        }

        
        // $password = Hash::check(Input::get('admin_password'), $validate_admin->password)
    }
}
