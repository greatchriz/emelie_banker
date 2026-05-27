@extends('layouts.front')

@section('content')
    <section class="hero-section bg--overlay bg_img" data-img="{{ asset('assets/images/'.$gs->breadcumb_banner) }}">
        <div class="container">
            <div class="hero-content">
                <h2 class="hero-title">@lang('FAQs')</h2>
                <ul class="breadcrumb">
                    <li><a href="{{ route('front.index') }}">@lang('Home')</a></li>
                    <li>@lang('FAQs')</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="front-faq-section pt-100 pb-100 pt-md-70 pb-md-70 pt-sm-50 pb-sm-50">
        <div class="container">
            <div class="section-title-center v9">
                <h5 class="sub-title v12">@lang('Help Center')</h5>
                <h2 class="big-title font-v1">@lang('Frequently Asked Questions')</h2>
            </div>

            @if (count($faqs) == 0)
                <div class="front-empty-state">
                    <h3>{{ __('No FAQ Found') }}</h3>
                </div>
            @else
                <div class="front-accordion">
                    @foreach ($faqs as $data)
                        <details {{ $loop->first ? 'open' : '' }}>
                            <summary>{{ $data->title }}</summary>
                            <div>@php echo $data->details; @endphp</div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if(isset($blogs) && count($blogs))
        <section class="front-blog-section pt-50 pb-100 pt-sm-50 pb-sm-50">
            <div class="container">
                <div class="section-title-center v9">
                    <h5 class="sub-title v12">@lang('News & Tips')</h5>
                    <h2 class="big-title font-v1">@lang('Latest Articles')</h2>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ($blogs as $blog)
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
    @endif
@endsection
