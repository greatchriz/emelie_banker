@extends('layouts.user')

@push('css')
  <style>
    .quick-action-dropdown > summary {
      list-style: none;
    }

    .quick-action-dropdown > summary::-webkit-details-marker {
      display: none;
    }

    .quick-action-card {
      position: relative;
      display: flex;
      min-height: 118px;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
      border-radius: 8px;
      border: 1px solid rgba(148, 163, 184, .22);
      background: rgba(15, 23, 42, .035);
      padding: 16px 12px;
      text-align: center;
      transition: border-color .25s ease, background-color .25s ease, transform .25s ease;
    }

    .quick-action-card:hover {
      border-color: rgba(32, 183, 87, .58);
      background: rgba(32, 183, 87, .08);
      transform: translateY(-1px);
    }

    .quick-action-icon {
      display: flex;
      width: 42px;
      height: 42px;
      align-items: center;
      justify-content: center;
      border-radius: 999px;
      color: #fff;
      line-height: 1;
    }

    .quick-action-label {
      display: flex;
      min-height: 34px;
      align-items: center;
      justify-content: center;
      color: inherit;
      font-size: 13px;
      font-weight: 600;
      line-height: 1.25;
    }

    .quick-action-chevron {
      position: absolute;
      right: 12px;
      bottom: 12px;
      color: #20B757;
      font-size: 16px;
      transition: transform .25s ease;
    }

    .quick-action-dropdown[open] .quick-action-chevron {
      transform: rotate(180deg);
    }

    .quick-action-menu {
      position: absolute;
      left: 0;
      right: 0;
      z-index: 30;
      margin-top: 8px;
      display: grid;
      gap: 8px;
      border-radius: 8px;
      border: 1px solid rgba(148, 163, 184, .24);
      background: var(--quick-action-menu-bg, #fff);
      padding: 8px;
      box-shadow: 0 18px 45px rgba(15, 23, 42, .18);
    }

    .dark .quick-action-card {
      border-color: rgba(148, 163, 184, .16);
      background: rgba(255, 255, 255, .025);
    }

    .dark .quick-action-card:hover {
      border-color: rgba(32, 183, 87, .52);
      background: rgba(32, 183, 87, .08);
    }

    .dark .quick-action-menu {
      --quick-action-menu-bg: #111827;
    }

    @media (max-width: 420px) {
      .quick-action-card {
        min-height: 110px;
        padding: 14px 8px;
      }

      .quick-action-icon {
        width: 38px;
        height: 38px;
      }
    }
  </style>
@endpush

@section('contents')
  <div class="user-dashboard mb-6 flex flex-wrap items-center justify-between gap-4 lg:mb-8">
    <div>
      <h3>{{ __('Dashboard') }}</h3>
      <p class="mt-1 text-sm text-n100">{{ __('Welcome back') }}, {{ $user->name }}</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
      <a href="{{ route('user.accounts.create') }}" class="btn-primary">
        <i class="las la-plus-circle text-base md:text-lg"></i>
        {{ __('Request Account') }}
      </a>
      <a href="{{ route('user.accounts.index') }}" class="inline-flex items-center gap-1 font-semibold text-primary">
        {{ __('Manage Accounts') }}
        <i class="las la-arrow-right"></i>
      </a>
    </div>
  </div>

  @if($activeAccounts->isEmpty())
    <div class="kyc-alert box mb-4">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h5 class="h5">{{ __('No Active Account Available') }}</h5>
          <p class="text-sm text-n700">{{ __('You can view your dashboard, but transactions are disabled until an account is active.') }}</p>
        </div>
        <a href="{{ route('user.accounts.create') }}" class="btn-primary">{{ __('Request Account') }}</a>
      </div>
    </div>
  @endif

  @if (auth()->user()->kyc_status != 1)
    <div class="kyc-alert box mb-4">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="alert-icon flex size-11 shrink-0 items-center justify-center rounded-full">
            <i class="las la-user-check text-2xl"></i>
          </span>
          <div>
            <h5 class="h5">{{ __('KYC Verification Required') }}</h5>
            <p class="text-sm text-n700">{{ __('You have a information to submit for kyc verification.') }}</p>
          </div>
        </div>
        <a href="{{ route('user.kyc.form') }}" class="btn-primary kyc-submit">{{ __('Submit') }}</a>
      </div>
    </div>
  @endif

  <div class="grid grid-cols-12 gap-4 xxl:gap-6">
    <div class="box col-span-12 bg-n0">
      <div class="bb-dashed mb-5 flex flex-wrap items-center justify-between gap-3 pb-4">
        <div>
          <span class="text-sm text-n100">{{ __('Total Portfolio Balance') }}</span>
          <div class="mt-2 flex items-center gap-3">
            <h2 class="text-3xl font-semibold" data-balance-value>{{ showprice($accounts->where('status', 'active')->sum('balance'), $currency) }}</h2>
            <button type="button" class="inline-flex size-9 items-center justify-center rounded-full border border-n30 bg-secondary/5 text-primary duration-300 hover:bg-primary hover:text-white" data-balance-toggle aria-label="{{ __('Hide balance') }}" title="{{ __('Hide balance') }}">
              <i class="las la-eye-slash text-xl"></i>
            </button>
          </div>
        </div>
        <div class="flex flex-wrap gap-2 text-sm">
          <span class="rounded-full bg-[#20B757]/10 px-3 py-1 font-medium text-[#20B757]">{{ $accounts->where('status', 'active')->count() }} {{ __('Active') }}</span>
          <span class="rounded-full bg-[#FFC861]/15 px-3 py-1 font-medium text-[#8A6100]">{{ $accounts->where('status', 'pending')->count() }} {{ __('Pending') }}</span>
          <span class="rounded-full bg-[#EF4444]/10 px-3 py-1 font-medium text-[#EF4444]">{{ $accounts->whereNotIn('status', ['active', 'pending'])->count() }} {{ __('Blocked') }}</span>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @forelse($accounts as $account)
          @php
            $isActive = $account->status === 'active';
            $isPending = $account->status === 'pending';
            $isBlocked = !$isActive && !$isPending;
            $accountName = $account->label ?: __('Account');
            $statusText = $account->status === 'disabled' ? __('Restricted') : ucfirst($account->status);
            $statusClass = $isActive
              ? 'bg-[#20B757]/10 text-[#20B757]'
              : ($isPending ? 'bg-[#FFC861]/15 text-[#8A6100]' : 'bg-[#EF4444]/10 text-[#EF4444]');
            $panelStyle = $isBlocked ? 'filter: grayscale(.18);' : '';
            $disabledTitle = $isBlocked ? __('Account Access Restricted') : __('Account Unavailable');
            $disabledMessage = $isBlocked
              ? __('This account is currently restricted, so deposits, transfers, and withdrawals are unavailable. Please contact support for help resolving this.')
              : __('This account is not active yet. Financial actions will be available after approval.');
            $actionRoutes = [
              ['label' => __('Deposit'), 'icon' => 'la-plus-circle', 'color' => 'bg-[#4371E9]', 'url' => route('user.deposit.create', ['account_id' => $account->id])],
              ['label' => __('Transfer'), 'icon' => 'la-paper-plane', 'color' => 'bg-primary', 'url' => route('send.money.create', ['account_id' => $account->id])],
              ['label' => __('Withdraw'), 'icon' => 'la-wallet', 'color' => 'bg-[#8B5CF6]', 'url' => route('user.withdraw.create', ['account_id' => $account->id])],
              ['label' => __('History'), 'icon' => 'la-list-alt', 'color' => 'bg-[#20B757]', 'url' => route('user.transaction', ['account_id' => $account->id])],
            ];
          @endphp

          <div class="relative overflow-hidden rounded-lg border {{ $isBlocked ? 'border-[#EF4444] bg-[#FFF1F1] shadow-[0_0_0_1px_rgba(239,68,68,.22),0_18px_44px_rgba(239,68,68,.14)] dark:bg-[#211518]' : 'border-n30 bg-n0 shadow-sm' }} p-3 md:p-4">
            @if($isBlocked)
              <div class="pointer-events-none absolute inset-0 bg-[#EF4444]/[.055]"></div>
              <div class="pointer-events-none absolute left-0 top-0 h-full w-1.5 bg-[#EF4444]"></div>
            @endif

            @if($isBlocked)
              <div class="relative z-10 mb-3 flex items-center justify-between gap-3 rounded-lg border border-[#EF4444]/35 bg-[#EF4444]/15 px-3 py-2 text-[#EF4444]">
                <span class="inline-flex items-center gap-2 text-sm font-semibold">
                  <i class="las la-lock"></i>
                  {{ __('Restricted Account') }}
                </span>
                <span class="text-xs font-medium">{{ __('Contact support') }}</span>
              </div>
            @endif

            <div class="relative z-0" style="{{ $panelStyle }}">
              <div class="mb-3 flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="mb-2 flex flex-wrap items-center gap-2">
                    <h4 class="h4 truncate">{{ $accountName }}</h4>
                  </div>
                  <div class="flex flex-wrap items-center gap-2 text-sm text-n700">
                    <span>{{ __('No') }}: <span id="account-number-{{ $account->id }}">{{ $account->account_number }}</span></span>
                    <button type="button" class="inline-flex size-7 items-center justify-center rounded-full bg-secondary/5 text-primary" onclick="copyText(document.getElementById('account-number-{{ $account->id }}').textContent.trim(), @js(__('Account number copied')))" aria-label="{{ __('Copy account number') }}">
                      <i class="las la-copy"></i>
                    </button>
                  </div>
                </div>
                <span class="{{ $statusClass }} rounded-full px-3 py-1 text-xs font-semibold">{{ $statusText }}</span>
              </div>

              <div class="mb-3">
                <div>
                  <span class="text-xs text-n100">{{ __('Balance') }}</span>
                  <div class="mt-1 flex items-center gap-3">
                    <h3 class="text-2xl font-semibold" data-balance-value>{{ showprice($account->balance, $currency) }}</h3>
                    <button type="button" class="inline-flex size-8 items-center justify-center rounded-full border border-n30 bg-white text-primary duration-300 hover:bg-primary hover:text-white" data-balance-toggle aria-label="{{ __('Hide balance') }}" title="{{ __('Hide balance') }}">
                      <i class="las la-eye-slash text-lg"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="mb-3 grid grid-cols-2 gap-2 md:grid-cols-4">
                @foreach($actionRoutes as $action)
                  @if($isActive)
                    <a href="{{ $action['url'] }}" class="group flex min-h-16 flex-col items-center justify-center gap-1.5 rounded-lg border border-n30 bg-white p-2.5 text-center duration-300 hover:border-primary hover:bg-primary/5">
                      <span class="{{ $action['color'] }} flex size-9 items-center justify-center rounded-full text-white">
                        <i class="las {{ $action['icon'] }} text-xl"></i>
                      </span>
                      <span class="text-xs font-medium">{{ $action['label'] }}</span>
                    </a>
                  @else
                    <button type="button" class="flex min-h-16 flex-col items-center justify-center gap-1.5 rounded-lg border {{ $isBlocked ? 'border-[#EF4444]/25 bg-[#2F2F35] text-white' : 'border-n30 bg-white text-n700' }} p-2.5 text-center" onclick="openAccountStatusModal(@js($disabledTitle), @js($disabledMessage), @js($isBlocked))">
                      <span class="flex size-9 items-center justify-center rounded-full {{ $isBlocked ? 'bg-white/10 text-white' : 'bg-secondary/20 text-n700' }}">
                        <i class="las {{ $isBlocked ? 'la-lock' : $action['icon'] }} text-xl"></i>
                      </span>
                      <span class="text-xs font-medium">{{ $action['label'] }}</span>
                    </button>
                  @endif
                @endforeach
              </div>

              <div class="flex flex-wrap gap-2 text-sm">
                @if($isActive)
                  <a href="{{ route('user.deposit.index', ['account_id' => $account->id]) }}" class="rounded-full border border-n30 px-3 py-1 text-primary">{{ __('Deposit History') }}</a>
                  <a href="{{ route('tranfer.logs.index', ['account_id' => $account->id]) }}" class="rounded-full border border-n30 px-3 py-1 text-primary">{{ __('Transfer History') }}</a>
                  <a href="{{ route('user.withdraw.index', ['account_id' => $account->id]) }}" class="rounded-full border border-n30 px-3 py-1 text-primary">{{ __('Withdrawals') }}</a>
                @endif
                <a href="{{ route('user.accounts.show', $account->id) }}" class="rounded-full border border-n30 px-3 py-1 text-primary">{{ __('Account Details') }}</a>
              </div>
            </div>
          </div>
        @empty
          <div class="rounded-lg border border-n30 bg-secondary/5 p-8 text-center">
            <h4 class="h4">{{ __('No accounts found.') }}</h4>
            <p class="mt-2 text-sm text-n700">{{ __('Request an account to start using dashboard actions.') }}</p>
          </div>
        @endforelse
      </div>
    </div>

    <div class="box col-span-12 bg-n0 lg:col-span-5">
      <div class="bb-dashed mb-4 pb-4">
        <h4 class="h4">{{ __('Your Referral Link') }}</h4>
      </div>
      <p class="mb-3 text-sm text-n700">{{ __('Share your referral link with friends.') }}</p>
      <div class="flex items-center gap-2 rounded-[30px] border border-n30 bg-secondary/5 p-1">
        <input type="text" name="key" value="{{ url('/').'?reff='.$user->affilate_code }}" class="w-full bg-transparent px-4 py-2 text-sm" id="cronjobURL" readonly>
        <button class="flex h-10 w-12 shrink-0 items-center justify-center rounded-full bg-primary text-white" id="copyBoard" type="button" onclick="myFunction()" aria-label="{{ __('Copy') }}">
          <i class="las la-copy text-xl"></i>
        </button>
      </div>
    </div>

    <div class="box col-span-12 bg-n0">
      <div class="bb-dashed mb-4 pb-4">
        <h4 class="h4">{{ __('Quick Actions') }}</h4>
      </div>
      @php
        $modules = explode(" , ", $gs->user_module);
        $quickActionCard = 'quick-action-card';
        $quickActionIcon = 'quick-action-icon';
        $quickActionLabel = 'quick-action-label';
        $quickDropdownLink = 'flex items-center justify-between gap-3 rounded-lg border border-n30 bg-secondary/5 px-3 py-2 text-sm font-medium text-n700 duration-300 hover:border-primary hover:text-primary';
      @endphp
      <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        <a href="{{ route('user.accounts.index') }}" class="{{ $quickActionCard }}">
          <span class="{{ $quickActionIcon }} bg-[#4371E9]"><i class="las la-credit-card text-2xl"></i></span>
          <span class="{{ $quickActionLabel }}">{{ __('My Accounts') }}</span>
        </a>

        @if (!in_array('Deposit',$modules))
          <a href="{{ route('user.deposit.index') }}" class="{{ $quickActionCard }}">
            <span class="{{ $quickActionIcon }} bg-[#20B757]"><i class="las la-piggy-bank text-2xl"></i></span>
            <span class="{{ $quickActionLabel }}">{{ __('Deposit') }}</span>
          </a>
        @endif

        @if ($gs->withdraw_status == 1 && !in_array('Withdraw',$modules))
          <a href="{{ route('user.withdraw.index') }}" class="{{ $quickActionCard }}">
            <span class="{{ $quickActionIcon }} bg-[#8B5CF6]"><i class="las la-wallet text-2xl"></i></span>
            <span class="{{ $quickActionLabel }}">{{ __('Withdraw') }}</span>
          </a>
        @endif

        @if (!in_array('Wire Transfer',$modules))
          <a href="{{ route('user.wire.transfer.index') }}" class="{{ $quickActionCard }}">
            <span class="{{ $quickActionIcon }} bg-[#4371E9]"><i class="las la-university text-2xl"></i></span>
            <span class="{{ $quickActionLabel }}">{{ __('Wire Transfer') }}</span>
          </a>
        @endif

        <a href="{{ route('user.beneficiaries.index') }}" class="{{ $quickActionCard }}">
          <span class="{{ $quickActionIcon }} bg-[#FFC861]"><i class="las la-user-friends text-2xl"></i></span>
          <span class="{{ $quickActionLabel }}">{{ __('Beneficiaries') }}</span>
        </a>

        @if (!in_array('Transfer',$modules))
          <details class="quick-action-dropdown relative">
            <summary class="{{ $quickActionCard }} cursor-pointer list-none">
              <span class="{{ $quickActionIcon }} bg-[#0EA5E9]"><i class="las la-exchange-alt text-2xl"></i></span>
              <span class="{{ $quickActionLabel }}">{{ __('Transfer') }}</span>
              <i class="las la-angle-down quick-action-chevron"></i>
            </summary>
            <div class="quick-action-menu">
              <a href="{{ route('send.money.create') }}" class="{{ $quickDropdownLink }}">{{ __('Send Money') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.other.bank') }}" class="{{ $quickDropdownLink }}">{{ __('Other Bank Transfer') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('tranfer.logs.index') }}" class="{{ $quickDropdownLink }}">{{ __('Transfer History') }} <i class="las la-arrow-right"></i></a>
            </div>
          </details>
        @endif

        @if (!in_array('Request Money',$modules))
          <details class="quick-action-dropdown relative">
            <summary class="{{ $quickActionCard }} cursor-pointer list-none">
              <span class="{{ $quickActionIcon }} bg-[#20B757]"><i class="las la-download text-2xl"></i></span>
              <span class="{{ $quickActionLabel }}">{{ __('Request Money') }}</span>
              <i class="las la-angle-down quick-action-chevron"></i>
            </summary>
            <div class="quick-action-menu">
              <a href="{{ route('user.money.request.create') }}" class="{{ $quickDropdownLink }}">{{ __('Create Request') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.money.request.index') }}" class="{{ $quickDropdownLink }}">{{ __('Sent Requests') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.request.money.receive') }}" class="{{ $quickDropdownLink }}">{{ __('Received Requests') }} <i class="las la-arrow-right"></i></a>
            </div>
          </details>
        @endif

        @if (!in_array('Loan',$modules))
          <details class="quick-action-dropdown relative">
            <summary class="{{ $quickActionCard }} cursor-pointer list-none">
              <span class="{{ $quickActionIcon }} bg-[#F59E0B]"><i class="las la-hand-holding-usd text-2xl"></i></span>
              <span class="{{ $quickActionLabel }}">{{ __('Loan') }}</span>
              <i class="las la-angle-down quick-action-chevron"></i>
            </summary>
            <div class="quick-action-menu">
              <a href="{{ route('user.loans.plan') }}" class="{{ $quickDropdownLink }}">{{ __('Loan Plan') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.loans.index') }}" class="{{ $quickDropdownLink }}">{{ __('All Loans') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.loans.pending') }}" class="{{ $quickDropdownLink }}">{{ __('Pending Loans') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.loans.running') }}" class="{{ $quickDropdownLink }}">{{ __('Running Loans') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.loans.paid') }}" class="{{ $quickDropdownLink }}">{{ __('Paid Loans') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.loans.rejected') }}" class="{{ $quickDropdownLink }}">{{ __('Rejected Loans') }} <i class="las la-arrow-right"></i></a>
            </div>
          </details>
        @endif

        @if (!in_array('DPS',$modules))
          <details class="quick-action-dropdown relative">
            <summary class="{{ $quickActionCard }} cursor-pointer list-none">
              <span class="{{ $quickActionIcon }} bg-[#14B8A6]"><i class="las la-warehouse text-2xl"></i></span>
              <span class="{{ $quickActionLabel }}">{{ __('DPS') }}</span>
              <i class="las la-angle-down quick-action-chevron"></i>
            </summary>
            <div class="quick-action-menu">
              <a href="{{ route('user.dps.plan') }}" class="{{ $quickDropdownLink }}">{{ __('DPS Plan') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.dps.index') }}" class="{{ $quickDropdownLink }}">{{ __('All DPS') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.dps.running') }}" class="{{ $quickDropdownLink }}">{{ __('Running DPS') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.dps.matured') }}" class="{{ $quickDropdownLink }}">{{ __('Matured DPS') }} <i class="las la-arrow-right"></i></a>
            </div>
          </details>
        @endif

        @if (!in_array('FDR',$modules))
          <details class="quick-action-dropdown relative">
            <summary class="{{ $quickActionCard }} cursor-pointer list-none">
              <span class="{{ $quickActionIcon }} bg-[#6366F1]"><i class="las la-user-shield text-2xl"></i></span>
              <span class="{{ $quickActionLabel }}">{{ __('FDR') }}</span>
              <i class="las la-angle-down quick-action-chevron"></i>
            </summary>
            <div class="quick-action-menu">
              <a href="{{ route('user.fdr.plan') }}" class="{{ $quickDropdownLink }}">{{ __('FDR Plan') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.fdr.index') }}" class="{{ $quickDropdownLink }}">{{ __('All FDR') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.fdr.running') }}" class="{{ $quickDropdownLink }}">{{ __('Running FDR') }} <i class="las la-arrow-right"></i></a>
              <a href="{{ route('user.fdr.closed') }}" class="{{ $quickDropdownLink }}">{{ __('Closed FDR') }} <i class="las la-arrow-right"></i></a>
            </div>
          </details>
        @endif

        @if (!in_array('Pricing Plan',$modules))
          <a href="{{ route('user.package.index') }}" class="{{ $quickActionCard }}">
            <span class="{{ $quickActionIcon }} bg-[#22C55E]"><i class="las la-layer-group text-2xl"></i></span>
            <span class="{{ $quickActionLabel }}">{{ __('Pricing Plan') }}</span>
          </a>
        @endif

        <button type="button" class="{{ $quickActionCard }}" onclick="openJivoSupportChat()">
          <span class="{{ $quickActionIcon }} bg-[#EF4444]"><i class="las la-life-ring text-2xl"></i></span>
          <span class="{{ $quickActionLabel }}">{{ __('Support') }}</span>
        </button>
      </div>
    </div>
  </div>

  <div id="accountStatusModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4" style="background: rgba(0, 0, 0, .5);">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-2xl">
      <div class="mb-4 flex items-start gap-4">
        <span id="accountStatusModalIcon" class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#EF4444]/10 text-[#EF4444]">
          <i class="las la-lock text-2xl"></i>
        </span>
        <div>
          <h4 id="accountStatusModalTitle" class="h4">{{ __('Account Access Restricted') }}</h4>
          <p id="accountStatusModalMessage" class="mt-2 text-sm text-n700">{{ __('This account is currently restricted, so deposits, transfers, and withdrawals are unavailable. Please contact support for help resolving this.') }}</p>
        </div>
      </div>
      <div class="flex flex-wrap justify-end gap-3">
        <button type="button" class="rounded-lg border border-n30 px-4 py-2 font-medium" onclick="closeAccountStatusModal()">{{ __('Close') }}</button>
        <button type="button" class="btn-primary" onclick="openJivoSupportChat()">{{ __('Contact Support') }}</button>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    'use strict';

    function writeClipboard(value, successMessage) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(value);
      } else {
        var tempInput = document.createElement('input');
        tempInput.value = value;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
      }

      if (typeof toastr !== 'undefined') {
        toastr.success(successMessage);
      } else {
        alert(successMessage);
      }
    }

    function copyText(value, successMessage) {
      writeClipboard(value, successMessage);
    }

    function myFunction() {
      var copyText = document.getElementById('cronjobURL');
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      writeClipboard(copyText.value, "{{ __('Copied') }}");
    }

    function openAccountStatusModal(title, message, blocked) {
      var modal = document.getElementById('accountStatusModal');
      var icon = document.getElementById('accountStatusModalIcon');
      document.getElementById('accountStatusModalTitle').textContent = title;
      document.getElementById('accountStatusModalMessage').textContent = message;
      icon.innerHTML = '<i class="las ' + (blocked ? 'la-lock' : 'la-clock') + ' text-2xl"></i>';
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeAccountStatusModal() {
      var modal = document.getElementById('accountStatusModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function openJivoSupportChat() {
      closeAccountStatusModal();

      if (window.jivo_api && typeof window.jivo_api.open === 'function') {
        window.jivo_api.open();
        return;
      }

      window.location.href = "{{ route('user.message.index') }}";
    }

    document.getElementById('accountStatusModal').addEventListener('click', function (event) {
      if (event.target === this) {
        closeAccountStatusModal();
      }
    });

    document.querySelectorAll('.quick-action-dropdown').forEach(function (dropdown) {
      dropdown.addEventListener('toggle', function () {
        if (!dropdown.open) {
          return;
        }

        document.querySelectorAll('.quick-action-dropdown[open]').forEach(function (openDropdown) {
          if (openDropdown !== dropdown) {
            openDropdown.removeAttribute('open');
          }
        });
      });
    });

    document.addEventListener('click', function (event) {
      if (event.target.closest('.quick-action-dropdown')) {
        return;
      }

      document.querySelectorAll('.quick-action-dropdown[open]').forEach(function (dropdown) {
        dropdown.removeAttribute('open');
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') {
        return;
      }

      document.querySelectorAll('.quick-action-dropdown[open]').forEach(function (dropdown) {
        dropdown.removeAttribute('open');
      });
    });
  </script>
@endpush
