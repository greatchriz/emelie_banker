@extends('layouts.front')

@section('content')
    @php
        $featureIcons = [
            'assets/front/fincatch/img/core-feature/v1/list-card-1.png',
            'assets/front/fincatch/img/core-feature/v1/list-card-2.png',
            'assets/front/fincatch/img/core-feature/v1/list-card-4.png',
            'assets/front/fincatch/img/benefits/benefits-img-1.png',
        ];
        $serviceImages = [
            'assets/front/fincatch/img/component/Payment.svg',
            'assets/front/fincatch/img/component/Payment-Method.svg',
            'assets/front/fincatch/img/invoices/v1/middle-img.png',
            'assets/front/fincatch/img/paymenys/v2/visa_card.png',
            'assets/front/fincatch/img/financial/v1/chart.svg',
            'assets/front/fincatch/img/benefits/benefits-img-2.png',
        ];
    @endphp

    <section class="banner v8 bg-cover-center ml-100 mr-100 ml-xl-0 mr-xl-0 front-home-hero"
        data-background="{{ asset('assets/front/fincatch/img/banner/v8/banner-bg.jpg') }}">
        <div class="container">
            <div class="banner-content">
                <div class="section-title-white v1">
                    <h2 class="big-title font-v1">{{ $ps->hero_title }}</h2>
                    <p class="title-para">{{ $ps->hero_subtitle }}</p>
                </div>
                <ul class="check-mark-list v3">
                    @foreach ($features->take(2) as $feature)
                        <li>
                            <i class="my-icon icon-check"></i>
                            <h6 class="text font-v1">{{ $feature->title }}</h6>
                        </li>
                    @endforeach
                </ul>
                <ul class="all-btns">
                    <li><a href="{{ $ps->hero_btn_url }}" class="link-anime v24 round-border-sm icon-1">@lang('Get Started')</a></li>
                    <li><a href="{{ $ps->hero_link }}" class="link-anime v25 round-border-sm icon-1">@lang('Learn more')</a></li>
                </ul>
            </div>
        </div>
    </section>

    @if ($features->count())
        <section class="partners-logo v3 front-feature-strip pt-80 pb-80 pt-sm-50 pb-sm-50">
            <div class="container">
                <div class="section-title-center v9">
                    <h5 class="sub-title v12">@lang('Smart banking features')</h5>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ($features as $feature)
                        <div class="col-sm-6 col-lg-3">
                                <div class="front-feature-card">
                                    <div class="front-feature-icon">
                                    <img src="{{ asset($featureIcons[$loop->index % count($featureIcons)]) }}" alt="{{ $feature->title }}">
                                </div>
                                <h4>{{ $feature->title }}</h4>
                                <p>{{ $feature->details }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="component-studio v1 front-payment-section">
        <div class="container">
            <div class="payment-flows pb-100 pb-lg-80 pb-md-50">
                <div class="section-title v9">
                    <h2 class="big-title font-v1"><span class="text-color">{{ $ps->service_title }}</span></h2>
                    <p class="title-para">@php echo $ps->service_subtitle; @endphp</p>
                </div>
                <div class="front-payment-visual">
                    <img src="{{ asset('assets/front/fincatch/img/component/Payment.svg') }}" alt="payment">
                    <img src="{{ asset('assets/front/fincatch/img/component/Payment-Method.svg') }}" alt="payment method">
                </div>
            </div>

            @if ($services->count())
                <div class="component pb-100 pb-md-70 pb-50">
                    <div class="section-title-center v9">
                        <h5 class="sub-title v12">@lang('Services')</h5>
                        <h2 class="big-title font-v1">{{ $ps->service_title }}</h2>
                    </div>
                    <div class="row g-4 justify-content-center">
                        @foreach ($services as $service)
                            <div class="col-md-6 col-xl-4">
                                <div class="powerful-payment-card front-service-card">
                                    <div class="card-img">
                                        <img src="{{ asset($serviceImages[$loop->index % count($serviceImages)]) }}" alt="{{ $service->title }}">
                                    </div>
                                    <h2 class="payment-title">{{ $service->title }}</h2>
                                    <div class="payment-para">@php echo $service->details; @endphp</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="built-by-developers v1 front-about-band pt-120 pb-120 pt-md-70 pb-md-70 pt-xl-100 pb-xl-100 pt-sm-50 pb-sm-50">
        <div class="container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6 col-xl-7">
                    <div class="section-title-white v9">
                        <h5 class="sub-title v12">@lang('Who We are')</h5>
                        <h2 class="big-title font-v1">{{ $ps->about_title }}</h2>
                        <div class="title-para">@php echo $ps->about_text; @endphp</div>
                        <ul class="front-check-list">
                            @if ($ps->about_attributes)
                                @foreach (json_decode($ps->about_attributes, true) as $attribute)
                                    <li><i class="my-icon icon-check"></i><span>{{ $attribute }}</span></li>
                                @endforeach
                            @endif
                        </ul>
                        <a href="{{ route('front.about') }}" class="read-more v11">@lang('More About Us')</a>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-5">
                    <div class="front-about-img">
                        <img src="{{ asset('assets/front/fincatch/img/paymenys/v2/visa_card.png') }}" alt="{{ $ps->about_title }}">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($process->count())
        <section class="grow-with-peace v1 front-process-section pt-120 pb-120 pt-md-70 pb-md-70 pt-xl-100 pb-xl-100 pt-sm-50 pb-sm-50">
            <div class="container">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-6">
                        <div class="main-content">
                            <div class="section-title v9">
                                <h5 class="sub-title v12">@lang('Strategy')</h5>
                                <h2 class="big-title font-v1">{{ $ps->strategy_title }}</h2>
                                <div class="title-para">@php echo $ps->strategy_details; @endphp</div>
                            </div>
                            <div class="front-process-list">
                                @foreach ($process as $data)
                                    <div class="front-process-item">
                                        <span>{{ $loop->iteration }}</span>
                                        <div>
                                            <h4>{{ $data->title }}</h4>
                                            <p>{{ $data->details }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="grow-with-peace-img">
                            <img src="{{ asset('assets/front/fincatch/img/bg-shap/v1/01.png') }}" alt="{{ $ps->strategy_title }}">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="powerful-payment v1 front-plan-section pt-120 pb-120 pt-md-70 pb-md-70 pt-xl-100 pb-xl-100 pt-sm-50 pb-sm-50">
        <div class="container">
            <div class="section-title-center v9">
                <h5 class="sub-title v12">@lang('Pricing Plan')</h5>
                <h2 class="big-title font-v1">{{ $ps->plan_title }}</h2>
                <div class="title-para">@php echo $ps->plan_subtitle; @endphp</div>
            </div>

            <ul class="nav nav-tabs front-plan-tabs" role="tablist">
                <li><a href="#deposit" class="active" data-bs-toggle="tab">@lang('Deposit')</a></li>
                <li><a href="#pension" data-bs-toggle="tab">@lang('Pension')</a></li>
                <li><a href="#loan" data-bs-toggle="tab">@lang('Loan')</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="deposit">
                    <div class="row g-4 justify-content-center">
                        @foreach ($depositsplans as $data)
                            <div class="col-lg-4 col-md-6">
                                <div class="front-plan-card">
                                    <h3>{{ $data->title }}</h3>
                                    <strong>{{ $data->interest_rate }}%</strong>
                                    <span>@lang('Interest Rate')</span>
                                    <ul>
                                        <li><span>@lang('Per Installment')</span><b>{{ showprice($data->per_installment, $currency) }}</b></li>
                                        <li><span>@lang('Total Deposit')</span><b>{{ showprice($data->final_amount, $currency) }}</b></li>
                                        <li><span>@lang('After Matured')</span><b>{{ showprice(round($data->final_amount + $data->user_profit, 2), $currency) }}</b></li>
                                        <li><span>@lang('Installment Interval')</span><b>{{ $data->installment_interval }} {{ __('Days') }}</b></li>
                                        <li><span>@lang('Total Installment')</span><b>{{ $data->total_installment }}</b></li>
                                    </ul>
                                    <a href="{{ route('user.dps.planDetails', $data->id) }}" class="link-anime v24 round-border-sm">@lang('Apply')</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade" id="pension">
                    <div class="row g-4 justify-content-center">
                        @foreach ($fdrplans as $data)
                            <div class="col-lg-4 col-md-6">
                                <div class="front-plan-card">
                                    <h3>{{ $data->title }}</h3>
                                    <strong>{{ $data->interest_rate }}%</strong>
                                    <span>@lang('Interest Rate')</span>
                                    <ul>
                                        <li><span>@lang('Minimum Amount')</span><b>{{ showprice($data->min_amount, $currency) }}</b></li>
                                        <li><span>@lang('Maximum Amount')</span><b>{{ showprice($data->max_amount, $currency) }}</b></li>
                                        <li><span>@lang('Interval Type')</span><b>{{ $data->interval_type }}</b></li>
                                        <li><span>@lang('Locked In Period')</span><b>{{ $data->matured_days }} {{ __('Days') }}</b></li>
                                        @if ($data->interest_interval)
                                            <li><span>@lang('Get Profit every')</span><b>{{ $data->interest_interval }} {{ __('Days') }}</b></li>
                                        @endif
                                    </ul>
                                    <button class="link-anime v24 round-border-sm apply-pension" type="button" data-bs-toggle="modal" data-bs-target="#modal-pension" data-id="{{ $data->id }}" data-title="{{ $data->title }}">
                                        @lang('Apply Now')
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade" id="loan">
                    <div class="row g-4 justify-content-center">
                        @foreach ($loanplans as $data)
                            <div class="col-lg-4 col-md-6">
                                <div class="front-plan-card">
                                    <h3>{{ $data->title }}</h3>
                                    <strong>{{ $data->per_installment }}%</strong>
                                    <span>@lang('Per Installment')</span>
                                    <ul>
                                        <li><span>@lang('Minimum Amount')</span><b>{{ showprice($data->min_amount, $currency) }}</b></li>
                                        <li><span>@lang('Maximum Amount')</span><b>{{ showprice($data->max_amount, $currency) }}</b></li>
                                        <li><span>@lang('Installment Interval')</span><b>{{ $data->installment_interval }} {{ __('Days') }}</b></li>
                                        <li><span>@lang('Total Installment')</span><b>{{ $data->total_installment }}</b></li>
                                    </ul>
                                    <button class="link-anime v24 round-border-sm apply-loan" type="button" data-bs-toggle="modal" data-bs-target="#modal-apply" data-id="{{ $data->id }}" data-title="{{ $data->title }}">
                                        @lang('Apply Now')
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($testimonials->count())
        <section class="stay-control v1 front-testimonial-section pt-120 pb-120 pt-md-70 pb-md-70 pt-xl-100 pb-xl-100 pt-sm-50 pb-sm-50">
            <div class="container">
                <div class="section-title-center v9">
                    <h5 class="sub-title v12">@lang('Testimonials')</h5>
                    <h2 class="big-title font-v1">{{ $ps->review_title }}</h2>
                    <div class="title-para">@php echo $ps->review_text; @endphp</div>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ($testimonials as $data)
                        <div class="col-md-6 col-xl-4">
                            <div class="front-testimonial-card">
                                <img src="{{ asset('assets/images/'.$data->photo) }}" alt="{{ $data->title }}">
                                <div>@php echo $data->details; @endphp</div>
                                <h4>{{ $data->title }}</h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($counters->count())
        <section class="our-inverstors v1 front-counter-section pt-120 pb-120 pt-md-70 pb-md-70 pt-xl-100 pb-xl-100 pt-sm-50 pb-sm-50">
            <div class="container">
                <div class="row g-4 justify-content-center">
                    @foreach ($counters as $data)
                        <div class="col-sm-6 col-lg-3">
                            <div class="calculate-growth front-counter-card">
                                <i class="{{ $data->icon }}"></i>
                                <h2 class="calculate-growth-title">
                                    @if ($data->is_money == 1)$@endif{{ $data->count }}M
                                </h2>
                                <h4 class="mini-title">{{ $data->title }}</h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @includeIf('partials.front.cta')

    <section class="front-blog-section pt-120 pb-120 pt-md-70 pb-md-70 pt-xl-100 pb-xl-100 pt-sm-50 pb-sm-50">
        <div class="container">
            <div class="section-title-center v9">
                <h5 class="sub-title v12">@lang('News & Tips')</h5>
                <h2 class="big-title font-v1">{{ $ps->blog_title }}</h2>
                <div class="title-para">@php echo $ps->blog_text; @endphp</div>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($blogs->get() as $blog)
                    <div class="col-lg-4 col-md-6">
                        <div class="front-blog-card">
                            <a href="{{ route('blog.details', $blog->slug) }}">
                                <img src="{{ asset('assets/images/'.$blog->photo) }}" alt="{{ $blog->title }}">
                            </a>
                            <div>
                                <span>@lang('Admin')</span>
                                <h4><a href="{{ route('blog.details', $blog->slug) }}">{{ Str::limit($blog->title, 55) }}</a></h4>
                                <a href="{{ route('blog.details', $blog->slug) }}" class="read-more v10">@lang('Read More')</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($faqs->count())
        <section class="front-faq-section pt-50 pb-120 pt-sm-50 pb-sm-50">
            <div class="container">
                <div class="section-title-center v9">
                    <h5 class="sub-title v12">@lang('FAQs')</h5>
                    <h2 class="big-title font-v1">{{ $ps->faq_title }}</h2>
                    <div class="title-para">@php echo $ps->faq_subtitle; @endphp</div>
                </div>
                <div class="front-accordion">
                    @foreach ($faqs as $data)
                        <details {{ $loop->first ? 'open' : '' }}>
                            <summary>{{ $data->title }}</summary>
                            <div>@php echo $data->details; @endphp</div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
