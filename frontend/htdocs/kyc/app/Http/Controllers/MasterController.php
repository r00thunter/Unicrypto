<?php

namespace App\Http\Controllers;

use App\Admin;
use App\User;
use App\UserReferral;
use Auth;
use Illuminate\Http\Request;

class MasterController extends Controller
{

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * @var \App\User
     */
    private $user;

    /**
     * @var \App\User
     */
    private $referral;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, User $user, UserReferral $referral,Admin $admin)
    {
        // $this->middleware('admin');
        $this->admin = $admin;
        $this->user     = $user;
        $this->request  = $request;
        $this->referral = $referral;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Users = $this->user->orderBy('created_at', 'desc')->get();

        foreach ($Users as $key => $value) {
            $referredBy = $this->referral->where('referral_id', $value->id)->first();

            if (isset($referredBy->user_id)) {
                $Users[$key]->referredBy = $this->user->find($referredBy->user_id);
            } else {
                $Users[$key]->referredBy = [];
            }
        }
        return view('admin.home', compact('Users'));
    }

    /**
     * Approval.
     *
     * @return \Illuminate\Http\Response
     */
    public function approval($id, $approval)
    {

        $User = $this->user->find($id);

        $User->update([
            'approval' => $approval,
        ]);
        return back();
    }

    /**
     * Referral.
     *
     * @return \Illuminate\Http\Response
     */
    public function referrals()
    {
        $Referrals = $this->referral->where('user_id', $this->request->id)->get()->pluck('referral_id');

        $Users = $this->user->whereIn('id', $Referrals)->get();

        foreach ($Users as $key => $value) {
            $referredBy = $this->referral->where('referral_id', $value->id)->first();

            if (isset($referredBy->user_id)) {
                $Users[$key]->referredBy = $this->user->find($referredBy->user_id);
            } else {
                $Users[$key]->referredBy = [];
            }
        }

        return view('admin.home', compact('Users'));
    }

    /**
     * User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = $this->user->find($this->request->id);

        return view('admin.details', compact('user'));
    }

    /**
     * Show the change Password.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword()
    {
        return view('admin.change_password');
    }

    /**
     * Update password
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        ini_set('memory_limit', '-1');
        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
        ]);

        // echo $request->id;exit;
        // $Admin = $this->admin->find($request->id)->update(['password' => bcrypt($request->password)]);
         $Admin_password = $this->admin->find($request->id);
         // echo $Admin_password;exit;
         if ($Admin_password) {
             $Admin_password->update([
                'password' => bcrypt($request->password),
            ]);
              return back()->with('status', 'Password Updated!');
         }else{
                return back()->with('status', 'Password Not Updated');
        }

        
    }
}
