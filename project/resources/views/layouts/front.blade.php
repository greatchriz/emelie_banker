<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    @if(isset($page->meta_tag) && isset($page->meta_description))
        <meta name="keywords" content="{{ $page->meta_tag }}">
        <meta name="description" content="{{ $page->meta_description }}">
    @elseif(isset($blog->meta_tag) && isset($blog->meta_description))
        <meta name="keywords" content="{{ $blog->meta_tag }}">
        <meta name="description" content="{{ $blog->meta_description }}">
    @else
        <meta name="keywords" content="{{ $seo->meta_keys }}">
        <meta name="author" content="GeniusOcean">
    @endif

    <title>{{ $gs->title }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/'.$gs->favicon) }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/front/fincatch/img/logo/favicon.svg') }}">

    <link rel="stylesheet" href="{{ asset('assets/front/fincatch/all-icons/myicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/fincatch/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/fincatch/css/plugins/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/fincatch/css/plugins/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/fincatch/css/plugins/venobox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/fincatch/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/fincatch/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/fincatch/css/front-overrides.css') }}">

    @if ($default_font->font_value)
        <link href="https://fonts.googleapis.com/css?family={{ $default_font->font_value }}&display=swap" rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    @endif

    @stack('css')

    <script src="//code.jivosite.com/widget/nYSIJIgMUG" async></script>

</head>

<body class="front-fincatch">
    <div id="main-wrapper">
        <header>
            @include('partials.front.nav')
        </header>

        <main>
            @yield('content')
        </main>

        @include('partials.front.footer')
    </div>

    @include('cookie-consent::index')

    <div class="modal fade" id="modal-apply">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title loan-title m-0">@lang('Basic')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.loan.amount') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="pt-3 pb-4">
                            <label for="amount" class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control" placeholder="0.00" id="amount">
                                <span class="input-group-text">{{ $currency->name }}</span>
                            </div>
                            <input type="hidden" name="planId" id="planId" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-success">@lang('Proceed')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pension">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title loan-title m-0">@lang('Basic')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.fdr.amount') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="pt-3 pb-4">
                            <label for="fdr-amount" class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control" placeholder="0.00" id="fdr-amount">
                                <span class="input-group-text">{{ $currency->name }}</span>
                            </div>
                            <input type="hidden" name="planId" id="fdrplan" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-success">@lang('Proceed')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/front/fincatch/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/front/fincatch/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/front/fincatch/js/plugins/anime.min.js') }}"></script>
    <script src="{{ asset('assets/front/fincatch/js/plugins/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/front/fincatch/js/plugins/venobox.min.js') }}"></script>
    <script src="{{ asset('assets/front/fincatch/js/plugins/wow.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/notify.js') }}"></script>
    <script src="{{ asset('assets/front/fincatch/js/index.js') }}"></script>
    <script src="{{ asset('assets/front/js/custom.js') }}"></script>

    <script>
        'use strict';
        let mainurl = '{{ url('/') }}';

        @if(Session::has('message'))
            toastr.options = {"closeButton": true, "progressBar": true};
            toastr.success("{{ session('message') }}");
        @endif
        @if(Session::has('error'))
            toastr.options = {"closeButton": true, "progressBar": true};
            toastr.error("{{ session('error') }}");
        @endif
        @if(Session::has('info'))
            toastr.options = {"closeButton": true, "progressBar": true};
            toastr.info("{{ session('info') }}");
        @endif
        @if(Session::has('warning'))
            toastr.options = {"closeButton": true, "progressBar": true};
            toastr.warning("{{ session('warning') }}");
        @endif

        $('.apply-loan').on('click', function(){
            $('#planId').val($(this).data('id'));
            $('.loan-title').text($(this).data('title'));
        });

        $('.apply-pension').on('click', function(){
            $('#fdrplan').val($(this).data('id'));
            $('.loan-title').text($(this).data('title'));
        });

        $('[data-img]').each(function(){
            $(this).css('background-image', 'url(' + $(this).data('img') + ')');
        });

        $(document).on('click', '.my-select + select.selectors + .list li, .my-select .list li', function(){
            const select = $(this).closest('.my-select').next('select.selectors');
            if (!select.length) {
                return;
            }
            select.prop('selectedIndex', $(this).index()).trigger('change');
        });
    </script>

    @stack('js')

    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script src="https://elfsightcdn.com/platform.js" async></script>
    <div class="elfsight-app-4ab5d99e-7bf8-4401-9b62-1c5697d1e527" data-elfsight-app-lazy></div>
</body>

</html>
