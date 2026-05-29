@extends('layouts.user')

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
          <h2 class="mt-2 text-3xl font-semibold">{{ showprice($accounts->where('status', 'active')->sum('balance'), $currency) }}</h2>
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
            $statusText = ucfirst($account->status);
            $statusClass = $isActive
              ? 'bg-[#20B757]/10 text-[#20B757]'
              : ($isPending ? 'bg-[#FFC861]/15 text-[#8A6100]' : 'bg-[#EF4444]/10 text-[#EF4444]');
            $panelStyle = $isBlocked ? 'filter: grayscale(1); opacity: .58;' : '';
            $disabledTitle = $isBlocked ? __('Account Blocked') : __('Account Unavailable');
            $disabledMessage = $isBlocked
              ? __('This account has been blocked by admin. Please contact support for assistance.')
              : __('This account is not active yet. Financial actions will be available after approval.');
            $actionRoutes = [
              ['label' => __('Deposit'), 'icon' => 'la-plus-circle', 'color' => 'bg-[#4371E9]', 'url' => route('user.deposit.create', ['account_id' => $account->id])],
              ['label' => __('Transfer'), 'icon' => 'la-paper-plane', 'color' => 'bg-primary', 'url' => route('send.money.create', ['account_id' => $account->id])],
              ['label' => __('Withdraw'), 'icon' => 'la-wallet', 'color' => 'bg-[#8B5CF6]', 'url' => route('user.withdraw.create', ['account_id' => $account->id])],
              ['label' => __('History'), 'icon' => 'la-list-alt', 'color' => 'bg-[#20B757]', 'url' => route('user.transaction', ['account_id' => $account->id])],
            ];
          @endphp

          <div class="relative overflow-hidden rounded-lg border {{ $isBlocked ? 'border-[#EF4444]/30 bg-[#EF4444]/5' : 'border-n30 bg-n0' }} p-4 shadow-sm">
            @if($isBlocked)
              <div class="pointer-events-none absolute inset-0 z-10 flex items-center justify-center" style="background: rgba(255, 255, 255, .58);">
                <span class="inline-flex items-center gap-2 rounded-full bg-[#EF4444] px-4 py-2 text-sm font-semibold text-white shadow-lg">
                  <i class="las la-lock"></i>
                  {{ __('Blocked Account') }}
                </span>
              </div>
            @endif

            <div class="relative z-0" style="{{ $panelStyle }}">
              <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="mb-2 flex flex-wrap items-center gap-2">
                    <h4 class="h4 truncate">{{ $accountName }}</h4>
                    @if($account->is_default)
                      <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary">{{ __('Default') }}</span>
                    @endif
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

              <div class="mb-4 grid gap-3 md:grid-cols-3">
                <div>
                  <span class="text-xs text-n100">{{ __('Balance') }}</span>
                  <h3 class="mt-1 text-2xl font-semibold">{{ showprice($account->balance, $currency) }}</h3>
                </div>
                <div>
                  <span class="text-xs text-n100">{{ __('Plan') }}</span>
                  <p class="mt-1 font-medium">{{ $account->plan->title ?? __('No Plan') }}</p>
                </div>
                <div>
                  <span class="text-xs text-n100">{{ __('Created') }}</span>
                  <p class="mt-1 font-medium">{{ optional($account->created_at)->format('d M Y') }}</p>
                </div>
              </div>

              <div class="mb-4 grid grid-cols-2 gap-2 md:grid-cols-4">
                <div class="rounded-lg bg-secondary/5 p-3">
                  <span class="text-xs text-n100">{{ __('Deposits') }}</span>
                  <p class="mt-1 font-semibold">{{ $account->deposits_count }}</p>
                </div>
                <div class="rounded-lg bg-secondary/5 p-3">
                  <span class="text-xs text-n100">{{ __('Transfers') }}</span>
                  <p class="mt-1 font-semibold">{{ $account->transfers_count }}</p>
                </div>
                <div class="rounded-lg bg-secondary/5 p-3">
                  <span class="text-xs text-n100">{{ __('Withdrawals') }}</span>
                  <p class="mt-1 font-semibold">{{ $account->withdraws_count }}</p>
                </div>
                <div class="rounded-lg bg-secondary/5 p-3">
                  <span class="text-xs text-n100">{{ __('Transactions') }}</span>
                  <p class="mt-1 font-semibold">{{ $account->transactions_count }}</p>
                </div>
              </div>

              <div class="mb-4 grid grid-cols-4 gap-2">
                @foreach($actionRoutes as $action)
                  @if($isActive)
                    <a href="{{ $action['url'] }}" class="group flex min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-n30 bg-white p-3 text-center duration-300 hover:border-primary hover:bg-primary/5">
                      <span class="{{ $action['color'] }} flex size-10 items-center justify-center rounded-full text-white">
                        <i class="las {{ $action['icon'] }} text-xl"></i>
                      </span>
                      <span class="text-xs font-medium">{{ $action['label'] }}</span>
                    </a>
                  @else
                    <button type="button" class="flex min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-n30 bg-white p-3 text-center text-n700" onclick="openAccountStatusModal(@js($disabledTitle), @js($disabledMessage), @js($isBlocked))">
                      <span class="flex size-10 items-center justify-center rounded-full bg-secondary/20 text-n700">
                        <i class="las {{ $isBlocked ? 'la-lock' : $action['icon'] }} text-xl"></i>
                      </span>
                      <span class="text-xs font-medium">{{ $action['label'] }}</span>
                    </button>
                  @endif
                @endforeach
              </div>

              <div class="mb-4 flex flex-wrap gap-2 text-sm">
                @if($isActive)
                  <a href="{{ route('user.deposit.index', ['account_id' => $account->id]) }}" class="rounded-full border border-n30 px-3 py-1 text-primary">{{ __('Deposit History') }}</a>
                  <a href="{{ route('tranfer.logs.index', ['account_id' => $account->id]) }}" class="rounded-full border border-n30 px-3 py-1 text-primary">{{ __('Transfer Logs') }}</a>
                  <a href="{{ route('user.withdraw.index', ['account_id' => $account->id]) }}" class="rounded-full border border-n30 px-3 py-1 text-primary">{{ __('Withdrawals') }}</a>
                @endif
                <a href="{{ route('user.accounts.show', $account->id) }}" class="rounded-full border border-n30 px-3 py-1 text-primary">{{ __('Account Details') }}</a>
              </div>

              <div class="rounded-lg border border-n30">
                <div class="flex items-center justify-between border-b border-n30 px-3 py-2">
                  <span class="text-sm font-semibold">{{ __('Latest Transactions') }}</span>
                  @if($isActive)
                    <a href="{{ route('user.transaction', ['account_id' => $account->id]) }}" class="text-xs font-medium text-primary">{{ __('View All') }}</a>
                  @endif
                </div>
                <div class="divide-y divide-n30">
                  @forelse($account->latestTransactions as $transaction)
                    <div class="flex items-center justify-between gap-3 px-3 py-3">
                      <div class="flex min-w-0 items-center gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full {{ $transaction->profit == 'plus' ? 'bg-[#20B757]/10 text-[#20B757]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                          <i class="las {{ $transaction->profit == 'plus' ? 'la-arrow-down' : 'la-arrow-up' }}"></i>
                        </span>
                        <div class="min-w-0">
                          <p class="truncate text-sm font-medium">{{ $transaction->type }}</p>
                          <p class="truncate text-xs text-n100">{{ $transaction->txnid }} - {{ date('d M Y', strtotime($transaction->created_at)) }}</p>
                        </div>
                      </div>
                      <span class="shrink-0 text-sm font-semibold {{ $transaction->profit == 'plus' ? 'text-[#20B757]' : 'text-[#EF4444]' }}">{{ showprice($transaction->amount, $currency) }}</span>
                    </div>
                  @empty
                    <p class="px-3 py-6 text-center text-sm text-n100">{{ __('No recent transactions') }}</p>
                  @endforelse
                </div>
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

    <div class="box col-span-12 bg-n0 lg:col-span-7">
      <div class="bb-dashed mb-4 pb-4">
        <h4 class="h4">{{ __('Quick Actions') }}</h4>
      </div>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        <a href="{{ route('user.wire.transfer.index') }}" class="flex flex-col items-center gap-3 rounded-lg border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#4371E9] text-white"><i class="las la-university text-2xl"></i></span>
          <span class="font-medium">{{ __('Wire Transfer') }}</span>
        </a>
        <a href="{{ route('user.beneficiaries.index') }}" class="flex flex-col items-center gap-3 rounded-lg border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#FFC861] text-white"><i class="las la-user-friends text-2xl"></i></span>
          <span class="font-medium">{{ __('Beneficiaries') }}</span>
        </a>
        <a href="{{ route('user.money.request.create') }}" class="flex flex-col items-center gap-3 rounded-lg border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#20B757] text-white"><i class="las la-download text-2xl"></i></span>
          <span class="font-medium">{{ __('Request Money') }}</span>
        </a>
        <a href="{{ route('user.message.index') }}" class="flex flex-col items-center gap-3 rounded-lg border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#EF4444] text-white"><i class="las la-life-ring text-2xl"></i></span>
          <span class="font-medium">{{ __('Support') }}</span>
        </a>
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
          <h4 id="accountStatusModalTitle" class="h4">{{ __('Account Blocked') }}</h4>
          <p id="accountStatusModalMessage" class="mt-2 text-sm text-n700">{{ __('This account has been blocked by admin. Please contact support for assistance.') }}</p>
        </div>
      </div>
      <div class="flex flex-wrap justify-end gap-3">
        <button type="button" class="rounded-lg border border-n30 px-4 py-2 font-medium" onclick="closeAccountStatusModal()">{{ __('Close') }}</button>
        <a href="{{ route('user.message.index') }}" class="btn-primary">{{ __('Contact Support') }}</a>
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

    document.getElementById('accountStatusModal').addEventListener('click', function (event) {
      if (event.target === this) {
        closeAccountStatusModal();
      }
    });
  </script>
@endpush
