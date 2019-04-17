<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="tab-links">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link @if(Request::url() == route('home')) active @endif" href="{{route('home')}}" >Profile</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link @if(Request::url() == route('security')) active @endif" @if(auth()->user()->completion >= 25) href="{{route('security')}}" @else href="{{ route('prevent.usage') }}" @endif>Security</a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link @if(Request::url() == route('kyc')) active @endif" @if(auth()->user()->completion >= 25) href="{{route('kyc')}}" @else href="{{ route('prevent.usage') }}" @endif>KYC / AML Verification</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::url() == route('contact')) active @endif" @if(auth()->user()->completion >= 75) href="{{route('contact')}}" @else href="{{ route('prevent.usage') }}" @endif>Contact Preference</a>
            </li>

            <!-- <li class="nav-item">
                <a class="nav-link @if(Request::url() == route('contact')) active @endif" @if(auth()->user()->completion == 100) href="{{route('referral')}}" @else href="{{ route('prevent.usage') }}" @endif>Referral</a>
            </li> -->
        </ul>
    </div>
</div>
