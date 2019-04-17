<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use PragmaRX\Countries\Package\Countries;
use Storage;
use App\Siteuser;
use App\UserReferral;

class HomeController extends Controller
{
    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, User $user, UserReferral $referral)
    {
        $this->middleware('auth');
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
         

        return view('home');
    }


    /**
     * Show the security.
     *
     * @return \Illuminate\Http\Response
     */
    public function security()
    {
        return view('security');
    }

    /**
     * Show the contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Show the kyc.
     *
     * @return \Illuminate\Http\Response
     */
    public function kyc()
    {
        return view('kyc');
    }

    /**
     * Show the referral.
     *
     * @return \Illuminate\Http\Response
     */
    public function referral()
    {
        return view('referral');
    }

    /**
     * Prevent Usage.
     *
     * @return \Illuminate\Http\Response
     */
    public function preventUsage()
    {
        return back()->with('warning', 'You need to fill this form to proceed further!');
    }

    public function frontindex()
    {
        return view('auth.login');
    }

    /**
     * Show the Success.
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        return view('success');
    }

    /**
     * Update Profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profileUpdate()
    {
        $this->validate($this->request, User::PROFILE_RULES);

        $User = $this->user->find(auth()->user()->id);

        if ($this->request->hasFile('avatar')) {
            $this->validate($this->request, ['avatar' => 'required|image']);
            $User->update([
                // 'avatar' => url('/') . Storage::url($this->request->avatar->store('avatar')),
                'avatar' => $this->request->avatar->store('avatar'),
            ]);
        }

        $User->update([
            'first_name'       => $this->request->first_name,
            'last_name'        => $this->request->last_name,
            'phone'            => $this->request->phone,
            'country'          => $this->request->country,
            'city'             => $this->request->city,
            'address'          => $this->request->address,
            'postal_code'      => $this->request->postal_code,
            'twitter_profile'  => $this->request->twitter_profile ? $this->request->twitter_profile : '',
            'linkedin_profile' => $this->request->linkedin_profile ? $this->request->linkedin_profile : '',
            'completion'       => Self::updateCompletion(25),
        ]);

        return redirect()->route('kyc')->with('status', 'Updated!');
    }

    /**
     * Update Security.
     *
     * @return \Illuminate\Http\Response
     */
    public function securityUpdate()
    {

        $User = $this->user->find(auth()->user()->id);

        if ($this->request->E2F == 'on') {
            $User->preference->update([
                'E2F' => 1,
            ]);
        }

        if ($this->request->ESMS == 'on') {
            $User->preference->update([
                'ESMS' => 1,
            ]);
        }

        $User->update([
            'completion' => Self::updateCompletion(50),
        ]);

        return redirect()->route('kyc')->with('status', 'Updated!');
    }

    /**
     * Update Contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function contactUpdate()
    {

        $User = $this->user->find(auth()->user()->id);

        if ($this->request->contact_sms == 'on') {
            $User->preference->update([
                'contact_sms' => 1,
            ]);
        }

        if ($this->request->contact_email == 'on') {
            $User->preference->update([
                'contact_email' => 1,
            ]);
        }

        $User->update([
            'completion' => Self::updateCompletion(100),
        ]);

        return redirect()->route('seccess')->with('status', 'Updated!');
    }

    /**
     * Update kyc.
     *
     * @return \Illuminate\Http\Response
     */
    public function kycUpdate()
    {

        $this->validate($this->request, User::KYC_RULES);

        $User = $this->user->find(auth()->user()->id);

        $User->preference->update([
            'id_proof'           => $this->request->id_proof->store('proof'),
            'address_proof'      => $this->request->address_proof->store('proof'),
            'id_card'            => $this->request->id_card->store('proof'),
            'id_proof_type'      => $this->request->id_proof_type,
            'address_proof_type' => $this->request->address_proof_type,
        ]);

        $User->update([
            'completion' => Self::updateCompletion(100),
        ]);

        // $siteuser = new Siteuser();

        // $siteuser->setConnection('mysql2');

        // $SiteUser = $siteuser->where('user','=',$User->exchange_site_user)->update(['approval'=>'APPROVED']);
        
        return redirect()->route('contact')->with('status', 'Updated!');
    }

    public function updateCompletion($value)
    {
        if (auth()->user()->completion > $value) {
            return auth()->user()->completion;
        }

        return $value;
    }
}
