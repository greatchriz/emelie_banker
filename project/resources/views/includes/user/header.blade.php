<nav class="navbar-top topbarfull z-20 gap-3 border-b border-n0 bg-n0 py-3 shadow-sm duration-300" id="topbar">
  <div class="topbar-inner flex items-center justify-between">
    <div class="topbar-left flex grow items-center gap-4 xxl:gap-6">
      <a href="{{ route('user.dashboard') }}" class="topbar-logo hidden shrink-0">
        <img width="174" height="38" src="{{ asset('assets/images/'.$gs->logo) }}" alt="{{ $gs->title }}" class="logo-full2 hidden lg:block" />
      </a>
      <button aria-label="sidebar-toggle-btn" class="flex items-center rounded-s-2xl bg-primary px-0.5 py-3 text-xl text-white" id="sidebar-toggle-btn">
        <i class="las la-angle-left text-lg"></i>
      </button>

      <form class="topnav-search">
        <input type="text" placeholder="{{ __('Search') }}" class="w-full border-none bg-transparent py-2 focus:border-none focus:shadow-none focus:outline-none md:py-2.5 xxl:py-3 ltr:pl-4 rtl:pr-4" />
        <button aria-label="search btn" class="flex h-8 w-9 items-center justify-center rounded-full bg-primary text-white" type="button">
          <i class="las la-search text-lg"></i>
        </button>
      </form>
    </div>

    <div class="topbar-actions flex items-center gap-3 md:gap-4">
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

      <button id="darkModeToggle" aria-label="dark mode switch" class="topbar-btn" type="button">
        <i class="las la-sun text-2xl dark:hidden!"></i>
        <i class="las la-moon text-2xl dark:block! hidden!"></i>
      </button>

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
