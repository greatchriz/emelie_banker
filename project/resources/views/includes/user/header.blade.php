<nav class="navbar-top topbarfull z-20 gap-3 border-b border-n0 bg-n0 py-3 shadow-sm duration-300" id="topbar">
  <div class="topbar-inner flex items-center justify-between">
    <div class="flex grow items-center gap-4 xxl:gap-6">
      <a href="{{ route('user.dashboard') }}" class="topbar-logo hidden shrink-0">
        <img width="174" height="38" src="{{ asset('assets/images/'.$gs->logo) }}" alt="{{ $gs->title }}" class="logo-full2 hidden lg:block" />
      </a>
      <button aria-label="sidebar-toggle-btn" class="flex items-center rounded-s-2xl bg-primary px-0.5 py-3 text-xl text-white" id="sidebar-toggle-btn">
        <i class="las la-angle-left text-lg"></i>
      </button>

      <div class="topnav-layout">
        <div id="layout-btn" class="flex w-full cursor-pointer items-center justify-between gap-2 rounded-[30px] border border-n30 bg-primary/5 px-4 py-1 lg:py-1.5 xxl:px-6 xxl:py-2">
          <span class="flex select-none items-center gap-2">
            <i class="las la-border-all text-3xl text-primary"></i>
            <span id="selected-layout" class="capitalize">Vertical</span>
          </span>
          <i id="drop-arrow" class="las la-angle-down shrink-0 text-lg duration-300"></i>
        </div>
        <ul id="layout" class="hide absolute left-0 top-full z-20 w-full origin-top rounded-lg bg-n0 p-2 shadow-[0px_6px_30px_0px_rgba(0,0,0,0.08)] duration-300">
          <li data-layout="vertical" class="active">Vertical</li>
          <li data-layout="two-column">Two-Column</li>
          <li data-layout="hovered">Hovered</li>
          <li data-layout="horizontal">Horizontal</li>
          <li data-layout="detached">Detached</li>
        </ul>
      </div>

      <form class="topnav-search">
        <input type="text" placeholder="{{ __('Search') }}" class="w-full border-none bg-transparent py-2 focus:border-none focus:shadow-none focus:outline-none md:py-2.5 xxl:py-3 ltr:pl-4 rtl:pr-4" />
        <button aria-label="search btn" class="flex h-8 w-9 items-center justify-center rounded-full bg-primary text-white" type="button">
          <i class="las la-search text-lg"></i>
        </button>
      </form>
    </div>

    <div class="flex items-center gap-3 md:gap-4">
      <div class="relative lg:hidden">
        <button id="mobile-search-btn" class="flex h-10 w-10 cursor-pointer select-none items-center justify-center gap-2 rounded-full border border-n30 bg-primary/5 md:h-12 md:w-12" type="button">
          <i class="las la-search"></i>
        </button>
        <div id="mobile-search" class="hide invisible absolute -left-8 top-full z-20 flex min-w-max max-w-62.5 origin-[20%_20%] gap-3 overflow-y-auto rounded-md bg-n0 p-3 opacity-0 shadow-[0px_6px_30px_0px_rgba(0,0,0,0.08)] duration-300">
          <form class="flex w-full items-center justify-between gap-3 rounded-[30px] border border-n30 bg-secondary/5 p-1 focus-within:border-primary xxl:p-2">
            <input type="text" placeholder="{{ __('Search') }}" class="w-full bg-transparent py-1 ltr:pl-4 rtl:pr-4" />
            <button class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary text-white lg:h-8 lg:w-8" type="button">
              <i class="las la-search text-lg"></i>
            </button>
          </form>
        </div>
      </div>

      <button id="darkModeToggle" aria-label="dark mode switch" class="h-10 w-10 shrink-0 rounded-full border border-n30 bg-primary/5 md:h-12 md:w-12" type="button">
        <i class="las la-sun text-2xl dark:hidden!"></i>
        <i class="las la-moon text-2xl dark:block! hidden!"></i>
      </button>

      <div class="relative">
        <button id="notification-btn" class="topbar-btn" type="button">
          <i class="las la-bell text-2xl"></i>
          <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs text-white">2</span>
        </button>
        <div id="notification" class="hide absolute top-full z-20 origin-[60%_0] rounded-md bg-n0 shadow-[0px_6px_30px_0px_rgba(0,0,0,0.08)] duration-300 ltr:-right-27.5 sm:ltr:right-0 sm:ltr:origin-top-right rtl:-left-30 sm:rtl:left-0 sm:rtl:origin-top-left">
          <div class="flex items-center justify-between border-b border-n30 p-3 lg:px-4">
            <h5 class="h5">{{ __('Notifications') }}</h5>
            <a href="{{ route('user.message.index') }}" class="text-sm text-primary">{{ __('View All') }}</a>
          </div>
          <ul class="flex w-75 flex-col p-4">
            <li class="flex cursor-pointer gap-2 rounded-md px-2 py-1.5 duration-300 hover:bg-primary/10">
              <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"><i class="las la-envelope text-xl"></i></span>
              <span class="text-sm">
                <span class="block font-medium">{{ __('Support Center') }}</span>
                <span class="text-xs text-n100">{{ __('Check your latest messages') }}</span>
              </span>
            </li>
            <li class="flex cursor-pointer gap-2 rounded-md px-2 py-1.5 duration-300 hover:bg-primary/10">
              <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"><i class="las la-shield-alt text-xl"></i></span>
              <span class="text-sm">
                <span class="block font-medium">{{ __('Account Security') }}</span>
                <span class="text-xs text-n100">{{ __('Keep your profile up to date') }}</span>
              </span>
            </li>
          </ul>
        </div>
      </div>

      <a href="{{ route('user.message.index') }}" class="topbar-btn max-[620px]:hidden">
        <i class="lab la-facebook-messenger"></i>
      </a>

      <div class="topbar-currency max-[520px]:hidden">
        <select name="currency" class="currency selectors nc-select">
          @foreach(DB::table('currencies')->get() as $availableCurrency)
            <option value="{{ route('front.currency',$availableCurrency->id) }}" {{ Session::has('currency') ? (Session::get('currency') == $availableCurrency->id ? 'selected' : '') : (DB::table('currencies')->where('is_default','=',1)->first()->id == $availableCurrency->id ? 'selected' : '') }}>
              {{ $availableCurrency->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="relative shrink-0">
        <div id="profile-btn" class="size-10 cursor-pointer md:size-12">
          <img src="{{ auth()->user()->photo ? asset('assets/images/'.auth()->user()->photo) : asset('assets/user/img/user.jpg') }}" class="size-full rounded-full object-cover" width="48" height="48" alt="{{ auth()->user()->name }}" />
        </div>
        <div id="profile" class="hide absolute top-full z-20 rounded-md bg-n0 shadow-[0px_6px_30px_0px_rgba(0,0,0,0.08)] duration-300 ltr:right-0 ltr:origin-top-right rtl:left-0 rtl:origin-top-left">
          <div class="flex flex-col items-center border-b border-n40 p-3 text-center lg:p-4">
            <img src="{{ auth()->user()->photo ? asset('assets/images/'.auth()->user()->photo) : asset('assets/user/img/user.jpg') }}" width="60" height="60" class="size-15 rounded-full object-cover" alt="{{ auth()->user()->name }}" />
            <h6 class="h6 mt-2">{{ auth()->user()->name }}</h6>
            <span class="text-sm">{{ auth()->user()->email }}</span>
          </div>
          <ul class="flex w-62.5 flex-col p-4">
            <li>
              <a href="{{ route('user.profile.index') }}" class="flex items-center gap-2 rounded-md px-2 py-1.5 duration-300 hover:bg-primary hover:text-n0">
                <span class="flex items-center"><i class="las la-user text-xl"></i></span>
                {{ __('Edit Profile') }}
              </a>
            </li>
            <li>
              <a href="{{ route('user.change.password.form') }}" class="flex items-center gap-2 rounded-md px-2 py-1.5 duration-300 hover:bg-primary hover:text-n0">
                <span class="flex items-center"><i class="las la-cog mt-1 text-xl"></i></span>
                {{ __('Change Password') }}
              </a>
            </li>
            <li>
              <a href="{{ route('user.logout') }}" class="flex items-center gap-2 rounded-md px-2 py-1.5 duration-300 hover:bg-primary hover:text-n0">
                <span class="flex items-center"><i class="las la-sign-out-alt mt-1 text-xl"></i></span>
                {{ __('Logout') }}
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>
