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
        $activeSidebarAccount = app(\App\Services\WalletService::class)->activeAccount(auth()->user());
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
            <li class="menu-li">
              <a href="{{ route('user.accounts.index') }}" class="menu-link {{ request()->routeIs('user.accounts.*') ? 'active text-primary' : '' }}">
                <i class="las la-credit-card"></i>
                <span>{{ __('My Accounts') }}</span>
              </a>
            </li>

            @if (!in_array('Loan',$modules))
              <li class="menu-li">
                <button class="menu-btn group {{ request()->routeIs('user.loans.*') ? 'active' : '' }}" type="button">
                  <span class="flex items-center justify-center gap-2">
                    <span class="menu-icon"><i class="las la-hand-holding-usd"></i></span>
                    <span class="menu-title font-medium">{{ __('Loan') }}</span>
                  </span>
                  <span class="plus-minus"><i class="las la-plus text-xl"></i><i class="las la-minus text-xl"></i></span>
                  <span class="chevron-down hidden"><i class="las la-angle-down text-base"></i></span>
                </button>
                <ul class="{{ request()->routeIs('user.loans.*') ? 'submenu-show' : 'submenu-hide' }} submenu">
                  <li><a href="{{ route('user.loans.plan') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Loan Plan') }}</span></a></li>
                  <li><a href="{{ route('user.loans.index') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('All Loans') }}</span></a></li>
                  <li><a href="{{ route('user.loans.pending') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Pending Loans') }}</span></a></li>
                  <li><a href="{{ route('user.loans.running') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Running Loans') }}</span></a></li>
                  <li><a href="{{ route('user.loans.paid') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Paid Loans') }}</span></a></li>
                  <li><a href="{{ route('user.loans.rejected') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Rejected Loans') }}</span></a></li>
                </ul>
              </li>
            @endif

            @if (!in_array('DPS',$modules))
              <li class="menu-li">
                <button class="menu-btn group {{ request()->routeIs('user.dps.*') ? 'active' : '' }}" type="button">
                  <span class="flex items-center justify-center gap-2">
                    <span class="menu-icon"><i class="las la-warehouse"></i></span>
                    <span class="menu-title font-medium">{{ __('DPS') }}</span>
                  </span>
                  <span class="plus-minus"><i class="las la-plus text-xl"></i><i class="las la-minus text-xl"></i></span>
                  <span class="chevron-down hidden"><i class="las la-angle-down text-base"></i></span>
                </button>
                <ul class="{{ request()->routeIs('user.dps.*') ? 'submenu-show' : 'submenu-hide' }} submenu">
                  <li><a href="{{ route('user.dps.plan') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Dps Plan') }}</span></a></li>
                  <li><a href="{{ route('user.dps.index') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('All dps') }}</span></a></li>
                  <li><a href="{{ route('user.dps.running') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Running dps') }}</span></a></li>
                  <li><a href="{{ route('user.dps.matured') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Matured dps') }}</span></a></li>
                </ul>
              </li>
            @endif

            @if (!in_array('FDR',$modules))
              <li class="menu-li">
                <button class="menu-btn group {{ request()->routeIs('user.fdr.*') ? 'active' : '' }}" type="button">
                  <span class="flex items-center justify-center gap-2">
                    <span class="menu-icon"><i class="las la-user-shield"></i></span>
                    <span class="menu-title font-medium">{{ __('FDR') }}</span>
                  </span>
                  <span class="plus-minus"><i class="las la-plus text-xl"></i><i class="las la-minus text-xl"></i></span>
                  <span class="chevron-down hidden"><i class="las la-angle-down text-base"></i></span>
                </button>
                <ul class="{{ request()->routeIs('user.fdr.*') ? 'submenu-show' : 'submenu-hide' }} submenu">
                  <li><a href="{{ route('user.fdr.plan') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Fdr Plan') }}</span></a></li>
                  <li><a href="{{ route('user.fdr.index') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('All Fdr') }}</span></a></li>
                  <li><a href="{{ route('user.fdr.running') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Running Fdr') }}</span></a></li>
                  <li><a href="{{ route('user.fdr.closed') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Closed Fdr') }}</span></a></li>
                </ul>
              </li>
            @endif

            @if (!in_array('Request Money',$modules))
              <li class="menu-li">
                <button class="menu-btn group {{ request()->routeIs('user.money.request.index') || request()->routeIs('user.request.money.receive') || request()->routeIs('user.money.request.create') ? 'active' : '' }}" type="button">
                  <span class="flex items-center justify-center gap-2">
                    <span class="menu-icon"><i class="las la-file-signature"></i></span>
                    <span class="menu-title font-medium">{{ __('Request Money') }}</span>
                  </span>
                  <span class="plus-minus"><i class="las la-plus text-xl"></i><i class="las la-minus text-xl"></i></span>
                  <span class="chevron-down hidden"><i class="las la-angle-down text-base"></i></span>
                </button>
                <ul class="{{ request()->routeIs('user.money.request.index') || request()->routeIs('user.request.money.receive') || request()->routeIs('user.money.request.create') ? 'submenu-show' : 'submenu-hide' }} submenu">
                  <li><a href="{{ route('user.money.request.create') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Create Request') }}</span></a></li>
                  <li><a href="{{ route('user.money.request.index') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Send Request Money') }}</span></a></li>
                  <li><a href="{{ route('user.request.money.receive') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Receive Request Money') }}</span></a></li>
                </ul>
              </li>
            @endif

            @if (!in_array('Deposit',$modules))
              <li class="menu-li">
                <a href="{{ route('user.deposit.index') }}" class="menu-link {{ request()->routeIs('user.deposit.*') ? 'active text-primary' : '' }}">
                  <i class="las la-piggy-bank"></i>
                  <span>{{ __('Deposit') }}</span>
                </a>
              </li>
            @endif

            @if (!in_array('Wire Transfer',$modules))
              <li class="menu-li">
                <a href="{{ route('user.wire.transfer.index') }}" class="menu-link {{ request()->routeIs('user.wire.transfer.*') ? 'active text-primary' : '' }}">
                  <i class="las la-university"></i>
                  <span>{{ __('Wire Transfer') }}</span>
                </a>
              </li>
            @endif

            @if (!in_array('Transfer',$modules))
              <li class="menu-li">
                <button class="menu-btn group {{ request()->routeIs('send.money.create') || request()->routeIs('user.beneficiaries.*') || request()->routeIs('user.other.bank') || request()->routeIs('tranfer.logs.index') ? 'active' : '' }}" type="button">
                  <span class="flex items-center justify-center gap-2">
                    <span class="menu-icon"><i class="las la-exchange-alt"></i></span>
                    <span class="menu-title font-medium">{{ __('Transfer') }}</span>
                  </span>
                  <span class="plus-minus"><i class="las la-plus text-xl"></i><i class="las la-minus text-xl"></i></span>
                  <span class="chevron-down hidden"><i class="las la-angle-down text-base"></i></span>
                </button>
                <ul class="{{ request()->routeIs('send.money.create') || request()->routeIs('user.beneficiaries.*') || request()->routeIs('user.other.bank') || request()->routeIs('tranfer.logs.index') ? 'submenu-show' : 'submenu-hide' }} submenu">
                  <li><a href="{{ route('send.money.create') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Send Money') }}</span></a></li>
                  <li><a href="{{ route('user.beneficiaries.index') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Beneficiary Manage') }}</span></a></li>
                  <li><a href="{{ route('user.other.bank') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Other Bank Transfer') }}</span></a></li>
                  <li><a href="{{ route('tranfer.logs.index') }}" class="submenu-link"><i class="las la-minus text-xl"></i><span>{{ __('Transfer History') }}</span></a></li>
                </ul>
              </li>
            @endif

            @if ($gs->withdraw_status == 1)
              @if (!in_array('Withdraw',$modules))
                <li class="menu-li">
                  <a href="{{ route('user.withdraw.index') }}" class="menu-link {{ request()->routeIs('user.withdraw.*') ? 'active text-primary' : '' }}">
                    <i class="las la-wallet"></i>
                    <span>{{ __('Withdraw') }}</span>
                  </a>
                </li>
              @endif
            @endif

            @if (!in_array('Pricing Plan',$modules))
              <li class="menu-li">
                <a href="{{ route('user.package.index') }}" class="menu-link {{ request()->routeIs('user.package.*') ? 'active text-primary' : '' }}">
                  <i class="las la-layer-group"></i>
                  <span>{{ __('Pricing Plan') }}</span>
                </a>
              </li>
            @endif
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
                    <span class="font-medium">{{ showprice($activeSidebarAccount->balance ?? 0, $currency) }}</span>
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
