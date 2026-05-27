<div class="menu-bar v8 fincatch-public-nav">
    <div class="container-fluid">
        <div class="menu-content">
            <div class="menu-logo">
                <a href="{{ route('front.index') }}">
                    <img src="{{ asset('assets/images/'.$gs->logo) }}" alt="{{ $gs->title }}">
                </a>
            </div>

            <nav class="main-menu">
                <ul>
                    @foreach(json_decode($gs->menu, true) ?? [] as $menue)
                        <li>
                            <a href="{{ url($menue['href']) }}" target="{{ $menue['target'] == 'blank' ? '_blank' : '_self' }}">
                                {{ $menue['title'] }}
                            </a>
                        </li>
                    @endforeach

                    @if ($gs->is_contact)
                        <li>
                            <a href="{{ route('front.contact') }}">@lang('Contact')</a>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="menu-right">
                <ul class="right-menur-btns">
                    <li class="front-select-item">
                        <select name="currency" class="currency selectors front-select">
                            @foreach(DB::table('currencies')->get() as $navCurrency)
                                <option value="{{ route('front.currency', $navCurrency->id) }}" {{ Session::has('currency') ? (Session::get('currency') == $navCurrency->id ? 'selected' : '') : (DB::table('currencies')->where('is_default', 1)->first()->id == $navCurrency->id ? 'selected' : '') }}>
                                    {{ $navCurrency->name }}
                                </option>
                            @endforeach
                        </select>
                    </li>
                    <li class="front-select-item front-language-item">
                        <select name="language" class="language selectors front-select">
                            @foreach(DB::table('languages')->get() as $language)
                                <option value="{{ route('front.language', $language->id) }}" {{ Session::has('language') ? (Session::get('language') == $language->id ? 'selected' : '') : (DB::table('languages')->where('is_default', 1)->first()->id == $language->id ? 'selected' : '') }}>
                                    {{ $language->language }}
                                </option>
                            @endforeach
                        </select>
                    </li>
                    @guest
                        <li class="front-auth-link">
                            <a href="{{ route('user.login') }}" class="link-anime v25 round-border-sm">@lang('Sign In')</a>
                        </li>
                        <li class="front-auth-link">
                            <a href="{{ route('user.register') }}" class="link-anime v24 round-border-sm">@lang('Start now')</a>
                        </li>
                    @else
                        <li class="front-auth-link">
                            <a href="{{ route('user.dashboard') }}" class="link-anime v24 round-border-sm">@lang('Dashboard')</a>
                        </li>
                    @endguest
                    <li>
                        <button class="mobile-btns" type="button" aria-label="@lang('Open menu')">
                            <i class="my-icon icon-category"></i>
                        </button>
                    </li>
                </ul>

                <div class="mobile-menu-bar">
                    <div class="mobile-content">
                        <ul class="all-btns">
                            @guest
                                <li><a href="{{ route('user.login') }}" class="link-anime v25 round-border-sm">@lang('Sign In')</a></li>
                                <li><a href="{{ route('user.register') }}" class="link-anime v24 round-border-sm">@lang('Start now')</a></li>
                            @else
                                <li><a href="{{ route('user.dashboard') }}" class="link-anime v24 round-border-sm">@lang('Dashboard')</a></li>
                            @endguest
                        </ul>

                        <div class="front-mobile-selects">
                            <select name="currency" class="currency selectors front-select">
                                @foreach(DB::table('currencies')->get() as $navCurrency)
                                    <option value="{{ route('front.currency', $navCurrency->id) }}" {{ Session::has('currency') ? (Session::get('currency') == $navCurrency->id ? 'selected' : '') : (DB::table('currencies')->where('is_default', 1)->first()->id == $navCurrency->id ? 'selected' : '') }}>
                                        {{ $navCurrency->name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="language" class="language selectors front-select">
                                @foreach(DB::table('languages')->get() as $language)
                                    <option value="{{ route('front.language', $language->id) }}" {{ Session::has('language') ? (Session::get('language') == $language->id ? 'selected' : '') : (DB::table('languages')->where('is_default', 1)->first()->id == $language->id ? 'selected' : '') }}>
                                        {{ $language->language }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="google_translate_element" class="front-google-translate"></div>

                        <ul class="social-link">
                            @if ($social->f_status)
                                <li><a href="{{ $social->facebook }}"><i class="my-icon icon-facebook"></i></a></li>
                            @endif
                            @if ($social->t_status)
                                <li><a href="{{ $social->twitter }}"><i class="my-icon icon-twitter"></i></a></li>
                            @endif
                            @if ($social->l_status)
                                <li><a href="{{ $social->linkedin }}"><i class="my-icon icon-linkedin-in"></i></a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
