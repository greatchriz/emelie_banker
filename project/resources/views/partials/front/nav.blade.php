<div class="navbar-wrapper">
    <div class="logo">
        <a href="{{ route('front.index') }}">
            <img src="{{asset('assets/images/'.$gs->logo)}}" alt="logo" />
        </a>
    </div>
    <div class="change-language d-sm-none ms-auto me-3 text--title">
        <select name="language" class="language selectors nice language-bar">
            @foreach(DB::table('languages')->get() as $language)
                <option value="{{route('front.language',$language->id)}}" {{ Session::has('language') ? ( Session::get('language') == $language->id ? 'selected' : '' ) : (DB::table('languages')->where('is_default','=',1)->first()->id == $language->id ? 'selected' : '') }} >
                    {{$language->language}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="nav-toggle d-lg-none">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="nav-menu-area">
        <div class="menu-close text--danger d-lg-none">
            <i class="fas fa-times"></i>
        </div>
        <ul class="nav-menu">
            @foreach(json_decode($gs->menu,true) as $key => $menue)
                <li><a href="{{ url($menue['href']) }}" target="{{ $menue['target'] == 'blank' ? '_blank' : '_self' }}">{{ $menue['title'] }}</a></li>
            @endforeach

            @if ($gs->is_contact)
                <li>
                    <a href="{{route('front.contact')}}">@lang('Contact')</a>
                </li>
            @endif
            
            
            @if (!auth()->user())

            <li>
                <div class="btn__grp ms-lg-3">
                        <a style="color: #536479;" href="{{ route('user.login') }}" class="cmn--btn">@lang('Login Now')</a>
                </div>
            </li>
            
            <li style="margin-top: 10px;">
                <div class="btn__grp ms-lg-3">
                        <a style="color: #536479;" href="{{ route('user.register') }}" class="cmn--btn">@lang('Register Now')</a>
                </div>
            </li>
            
             @else 
             
             <li style="margin-top: 10px;">
                <div class="btn__grp ms-lg-3">
                        <a style="color: #536479;" href="{{ route('user.dashboard') }}" class="cmn--btn">@lang('Dashboard')</a>
                </div>
             </li>
             
            @endif
            
            <li style="margin-top: 10px;">
                <div class="btn__grp ms-lg-3">
                    <div class="cmn--btn">
                        <div id="google_translate_element"></div>
                    </div>
                    
                </div>
            </li>
        </ul>
    </div>
</div>