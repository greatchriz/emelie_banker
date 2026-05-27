<footer>
    <div class="footer-section v8 front-footer">
        <div class="cta pt-95 pb-95 pt-md-70 pb-md-70 pt-sm-50 pb-sm-50">
            <div class="container">
                <div class="title-left-right-center">
                    <div class="section-title-white v9">
                        <h2 class="big-title">{{ $ps->quick_title ?? __('Get started today') }}</h2>
                        <p class="title-para">{{ $ps->quick_subtitle ?? '' }}</p>
                    </div>
                    <ul class="all-btns">
                        <li>
                            <a href="{{ $ps->quick_link ?? route('user.register') }}" class="link-anime v24 round-border-sm">@lang('Start now')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="info-footer pt-130 pt-sm-50 pt-md-70 pt-lg-100">
            <div class="container">
                <div class="front-footer-brand">
                    <a href="{{ route('front.index') }}">
                        <img src="{{ asset('assets/images/'.$gs->footer_logo) }}" alt="{{ $gs->title }}">
                    </a>
                    <p>{{ $ps->side_text ?? $gs->title }}</p>
                </div>

                <div class="info-footer-content pt-80 pb-80 pt-md-50 pb-md-50">
                    <div class="footer-widget">
                        <h4 class="footer-widget-title">@lang('Company')</h4>
                        <div class="footer-widget-content">
                            <ul class="link-list">
                                @foreach(json_decode($gs->menu, true) ?? [] as $menue)
                                    <li>
                                        <a href="{{ url($menue['href']) }}" target="{{ $menue['target'] == 'blank' ? '_blank' : '_self' }}">{{ $menue['title'] }}</a>
                                    </li>
                                @endforeach
                                @if ($gs->is_contact)
                                    <li><a href="{{ route('front.contact') }}">@lang('Contact')</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="footer-widget">
                        <h4 class="footer-widget-title">@lang('Pages')</h4>
                        <div class="footer-widget-content">
                            <ul class="link-list">
                                @foreach(DB::table('pages')->whereStatus(1)->orderBy('id','desc')->get() as $data)
                                    <li><a href="{{ route('front.page', $data->slug) }}">{{ $data->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="footer-widget">
                        <h4 class="footer-widget-title">@lang('Contact')</h4>
                        <div class="footer-widget-content">
                            <ul class="link-list">
                                <li><a href="#0">{{ $ps->street }}</a></li>
                                <li><a href="mailto:{{ $ps->contact_email }}">{{ $ps->contact_email }}</a></li>
                                <li><a href="mailto:{{ $ps->email }}">{{ $ps->email }}</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="footer-widget">
                        <h4 class="footer-widget-title">@lang('Social')</h4>
                        <div class="footer-widget-content">
                            <ul class="link-list">
                                @if ($social->f_status)
                                    <li><a href="{{ $social->facebook }}">Facebook</a></li>
                                @endif
                                @if ($social->t_status)
                                    <li><a href="{{ $social->twitter }}">Twitter</a></li>
                                @endif
                                @if ($social->l_status)
                                    <li><a href="{{ $social->linkedin }}">LinkedIn</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="footer-widget front-newsletter">
                        <h4 class="footer-widget-title">@lang('Newsletter')</h4>
                        <div class="footer-widget-content">
                            <p>@lang('Stay Excited, Subscribe to our Newsletter')</p>
                            <form action="{{ route('front.subscriber') }}" method="POST">
                                @csrf
                                <input type="email" name="email" class="form-control" placeholder="@lang('Your email address...')">
                                <button type="submit" class="link-anime v24 round-border-sm">@lang('Subscribe')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-main v8">
            <div class="container">
                <div class="footer-main-content">
                    <h5 class="text">
                        @php
                            echo $gs->copyright;
                        @endphp
                    </h5>
                    <h5 class="text">{{ $gs->title }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="back-to-top">
        <a href="#"><i class="my-icon icon-arrow-up"></i></a>
    </div>
</footer>
