<aside id="sidebar" class="sidebar sidebarshow">
  <div class="sidebar-inner relative">
    <div class="logo-column">
      <div class="logo-container">
        <div class="logo-inner">
          <a href="{{ route('user.dashboard') }}" class="logo-wrapper">
            <img src="{{ asset('assets/images/'.$gs->logo) }}" width="174" height="38" class="logo-full" alt="{{ $gs->title }}" />
            <img src="{{ asset('assets/user/bankhub/images/logo.png') }}" width="37" height="36" class="logo-icon hidden" alt="{{ $gs->title }}" />
          </a>
          <img width="141" height="38" class="logo-text hidden" src="{{ asset('assets/user/bankhub/images/logo-text.png') }}" alt="{{ $gs->title }}" />
          <button class="sidebar-close-btn xl:hidden" id="sidebar-close-btn" type="button">
            <i class="las la-times"></i>
          </button>
        </div>
      </div>

      @php
        $modules = explode(" , ", $gs->user_module);
        $sidebarPortfolioBalance = auth()->user()->accounts()->active()->sum('balance');
      @endphp

      <div class="menu-container pb-28">
        <div class="menu-wrapper">
          <p class="menu-heading">{{ __('Navigation') }}</p>
          <ul class="menu-ul">
            <li class="menu-li">
              <a href="{{ route('user.dashboard') }}" class="menu-link {{ request()->routeIs('user.dashboard') ? 'active text-primary' : '' }}">
                <i class="las la-home"></i>
                <span>{{ __('Dashboard') }}</span>
              </a>
            </li>
          </ul>
        </div>

        @if (!in_array('More',$modules))
          <div class="menu-wrapper">
            <p class="menu-heading">{{ __('More') }}</p>
            <ul class="menu-ul">
              <li class="menu-li">
                <a href="{{ route('user.show2faForm') }}" class="menu-link {{ request()->routeIs('user.show2faForm') ? 'active text-primary' : '' }}">
                  <i class="las la-shield-alt"></i>
                  <span>{{ __('2FA Security') }}</span>
                </a>
              </li>
              <li class="menu-li">
                <a href="{{ route('user.referral.index') }}" class="menu-link {{ request()->routeIs('user.referral.index') ? 'active text-primary' : '' }}">
                  <i class="las la-user-friends"></i>
                  <span>@lang('Referred Users')</span>
                </a>
              </li>
              <li class="menu-li">
                <a href="{{ route('user.referral.commissions') }}" class="menu-link {{ request()->routeIs('user.referral.commissions') ? 'active text-primary' : '' }}">
                  <i class="las la-coins"></i>
                  <span>@lang('Referral Commissions')</span>
                </a>
              </li>
              <li class="menu-li">
                <a href="{{ route('user.message.index') }}" class="menu-link {{ request()->routeIs('user.message.*') ? 'active text-primary' : '' }}">
                  <i class="las la-life-ring"></i>
                  <span>{{ __('Support Tickets') }}</span>
                </a>
              </li>
              <li class="menu-li">
                <a href="{{ route('user.transaction') }}" class="menu-link {{ request()->routeIs('user.transaction') ? 'active text-primary' : '' }}">
                  <i class="las la-receipt"></i>
                  <span>{{ __('Transactions') }}</span>
                </a>
              </li>
            </ul>
          </div>
        @endif

        <div class="px-4 xxl:px-6 3xl:px-8">
          <div class="balance-part">
            <p class="border-t-2 border-dashed border-primary/20 py-4 text-xs font-semibold lg:py-6">{{ __('Balance') }}</p>
            <ul>
              <li>
                <a href="{{ route('user.dashboard') }}" class="group flex w-full items-center justify-between rounded-xl px-4 py-2.5 lg:py-3 3xl:px-6">
                  <span class="flex items-center gap-2">
                    <span class="-mb-1 self-center text-primary"><i class="las la-wallet"></i></span>
                    <span class="font-medium" data-balance-value>{{ showprice($sidebarPortfolioBalance, $currency) }}</span>
                  </span>
                </a>
                @if (!in_array('Deposit',$modules))
                  <a href="{{ route('user.deposit.create') }}" class="group flex w-full items-center justify-between rounded-xl px-4 py-2.5 lg:py-3 3xl:px-6">
                    <span class="flex items-center gap-2">
                      <span class="-mb-1 self-center text-primary"><i class="las la-plus-circle"></i></span>
                      <span class="font-medium">{{ __('Add More Balance') }}</span>
                    </span>
                  </a>
                @endif
              </li>
            </ul>
          </div>
          @if (!in_array('Pricing Plan',$modules))
            <div class="upgrade-part">
              <img src="{{ asset('assets/user/bankhub/images/upgrade.png') }}" width="272" height="173" alt="{{ __('Upgrade') }}" />
              <p class="mb-8 mt-6 text-sm">
                {{ __('Upgrade your account for more banking features.') }}
              </p>
              <a href="{{ route('user.package.index') }}" class="btn-primary flex w-full justify-center">{{ __('Upgrade Now') }}</a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</aside>
