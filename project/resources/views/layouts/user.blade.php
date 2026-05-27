<!doctype html>
<html dir="ltr" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $gs->title }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/'.$gs->favicon) }}">
    <link href="{{ asset('assets/user/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/user/bankhub/css/nice-select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/bankhub/css/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/bankhub/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/user/bankhub/css/user-overrides.css') }}">
    <script>
      (function () {
        try {
          if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
          } else {
            document.documentElement.style.colorScheme = 'light';
          }
        } catch (error) {
          document.documentElement.style.colorScheme = 'light';
        }
      })();
    </script>
    <script src="//code.jivosite.com/widget/nYSIJIgMUG" async></script>

    @stack('css')
  </head>

  <body class="user-shell vertical hidden bg-secondary/5">
    <div class="loader min-w-screen fixed inset-0 z-50! flex min-h-screen items-center justify-center bg-n0">
      <svg viewBox="25 25 50 50">
        <circle r="20" cy="50" cx="50"></circle>
      </svg>
    </div>

    <section class="topbar-container z-30">
      @includeIf('includes.user.header')
      @includeIf('includes.user.nav')
    </section>

    <main class="main-content has-sidebar">
      <div class="main-inner">
        @yield('contents')
      </div>
    </main>

    @includeIf('includes.user.footer')

    <script>
      let mainurl = '{{ url('/') }}';
      window.bankhubLogoFull = '{{ asset('assets/images/'.$gs->logo) }}';
      window.bankhubLogoIcon = '{{ asset('assets/user/bankhub/images/logo.png') }}';
      window.bankhubLogoText = '{{ asset('assets/user/bankhub/images/logo-text.png') }}';
    </script>
    <script src="{{ asset('assets/user/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/user/js/tabler.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/custom.js') }}"></script>
    <script src="{{ asset('assets/user/js/notify.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/user/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/user/bankhub/js/libs/nice-select2.js') }}"></script>
    <script src="{{ asset('assets/user/bankhub/js/charts.js') }}"></script>
    <script src="{{ asset('assets/user/bankhub/js/main.js') }}"></script>
    @stack('js')

    <script>
      'use strict';

      toastr.options = {
        closeButton: true,
        progressBar: true
      };

      @if(Session::has('message'))
        toastr.success("{{ session('message') }}");
      @endif

      @if(Session::has('success'))
        toastr.success("{{ session('success') }}");
      @endif

      @if(Session::has('error'))
        toastr.error("{{ session('error') }}");
      @endif

      @if(Session::has('info'))
        toastr.info("{{ session('info') }}");
      @endif

      @if(Session::has('warning'))
        toastr.warning("{{ session('warning') }}");
      @endif
    </script>
  </body>
</html>
