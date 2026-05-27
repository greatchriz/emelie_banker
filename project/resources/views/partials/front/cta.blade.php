<section class="front-inline-cta pt-100 pb-100 pt-md-70 pb-md-70 pt-sm-50 pb-sm-50"
    data-background="{{ asset('assets/front/fincatch/img/banner/v7/banner-bg.jpg') }}">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-5">
                <div class="front-inline-cta-img">
                    <img src="{{ asset('assets/front/fincatch/img/invoices/v1/middle-img.png') }}" alt="{{ $ps->quick_title }}">
                </div>
            </div>
            <div class="col-lg-7">
                <div class="section-title-white v9">
                    <h5 class="sub-title v12">@lang('Quick Start')</h5>
                    <h2 class="big-title">{{ $ps->quick_title }}</h2>
                    <p class="title-para">{{ $ps->quick_subtitle }}</p>
                    <a href="{{ $ps->quick_link }}" class="link-anime v24 round-border-sm">@lang('Get Started Now')</a>
                </div>
            </div>
        </div>
    </div>
</section>
