@extends('layouts.user')

@section('contents')
  <div class="user-dashboard mb-6 flex flex-wrap items-center justify-between gap-4 lg:mb-8">
    <div>
      <h3>{{ __('Dashboard') }}</h3>
      <p class="mt-1 text-sm text-n100">{{ __('Welcome back') }}, {{ $user->name }}</p>
    </div>
    <a href="{{ route('user.deposit.create') }}" class="btn-primary">
      <i class="las la-plus-circle text-base md:text-lg"></i>
      {{ __('Add Balance') }}
    </a>
  </div>

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
    <div class="box col-span-12 bg-n0 lg:col-span-7">
      <div class="bb-dashed mb-5 flex flex-wrap items-center justify-between gap-3 pb-4">
        <div>
          <span class="text-sm text-n100">{{ __('Available Balance') }}</span>
          <h2 class="mt-2 text-3xl font-semibold">{{ showprice($user->balance,$currency) }}</h2>
        </div>
        <span class="flex rounded-xl bg-primary/10 px-4 py-2 text-sm font-medium text-primary">
          {{ __('Account') }}: {{ $user->account_number }}
        </span>
      </div>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        <a href="{{ route('send.money.create') }}" class="flex flex-col items-center gap-3 rounded-xl border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-primary text-white">
            <i class="las la-paper-plane text-2xl"></i>
          </span>
          <span class="font-medium">{{ __('Send Money') }}</span>
        </a>
        <a href="{{ route('user.money.request.create') }}" class="flex flex-col items-center gap-3 rounded-xl border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#20B757] text-white">
            <i class="las la-download text-2xl"></i>
          </span>
          <span class="font-medium">{{ __('Request') }}</span>
        </a>
        <a href="{{ route('user.deposit.create') }}" class="flex flex-col items-center gap-3 rounded-xl border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#4371E9] text-white">
            <i class="las la-plus-circle text-2xl"></i>
          </span>
          <span class="font-medium">{{ __('Deposit') }}</span>
        </a>
        <a href="{{ route('user.withdraw.index') }}" class="flex flex-col items-center gap-3 rounded-xl border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#8B5CF6] text-white">
            <i class="las la-wallet text-2xl"></i>
          </span>
          <span class="font-medium">{{ __('Withdraw') }}</span>
        </a>
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

    <div class="box col-span-12 bg-n0 min-[650px]:col-span-6 3xl:col-span-3">
      <div class="bb-dashed mb-4 flex items-center justify-between pb-4">
        <span class="font-medium">{{ __('Deposits') }}</span>
        <i class="las la-money-check text-2xl text-primary"></i>
      </div>
      <div class="flex items-center justify-between">
        <div>
          <h4 class="h4 mb-4">{{ count($user->deposits) }}</h4>
          <a href="{{ route('user.deposit.index') }}" class="flex items-center gap-1 whitespace-nowrap text-primary">{{ __('View Deposits') }} <i class="las la-arrow-right text-lg"></i></a>
        </div>
        <div class="-my-3 shrink-0 ltr:translate-x-3 xl:ltr:translate-x-7 3xl:ltr:translate-x-2 4xl:ltr:translate-x-9 rtl:-translate-x-3 xl:rtl:-translate-x-7 3xl:rtl:-translate-x-2 4xl:rtl:-translate-x-9">
          <div class="progress-chart"></div>
        </div>
      </div>
    </div>

    <div class="box col-span-12 bg-n0 min-[650px]:col-span-6 3xl:col-span-3">
      <div class="bb-dashed mb-4 flex items-center justify-between pb-4">
        <span class="font-medium">{{ __('Withdraws') }}</span>
        <i class="las la-dollar-sign text-2xl text-primary"></i>
      </div>
      <div class="flex items-center justify-between">
        <div>
          <h4 class="h4 mb-4">{{ count($user->withdraws) }}</h4>
          <a href="{{ route('user.withdraw.index') }}" class="flex items-center gap-1 whitespace-nowrap text-primary">{{ __('View Withdraws') }} <i class="las la-arrow-right text-lg"></i></a>
        </div>
        <div class="-my-3 shrink-0 ltr:translate-x-3 xl:ltr:translate-x-7 3xl:ltr:translate-x-2 4xl:ltr:translate-x-9 rtl:-translate-x-3 xl:rtl:-translate-x-7 3xl:rtl:-translate-x-2 4xl:rtl:-translate-x-9">
          <div class="progress-chart"></div>
        </div>
      </div>
    </div>

    <div class="box col-span-12 bg-n0 min-[650px]:col-span-6 3xl:col-span-3">
      <div class="bb-dashed mb-4 flex items-center justify-between pb-4">
        <span class="font-medium">{{ __('Transactions') }}</span>
        <i class="las la-exchange-alt text-2xl text-primary"></i>
      </div>
      <div class="flex items-center justify-between">
        <div>
          <h4 class="h4 mb-4">{{ count($user->transactions) }}</h4>
          <a href="{{ route('user.transaction') }}" class="flex items-center gap-1 whitespace-nowrap text-primary">{{ __('View Transactions') }} <i class="las la-arrow-right text-lg"></i></a>
        </div>
        <div class="-my-3 shrink-0 ltr:translate-x-3 xl:ltr:translate-x-7 3xl:ltr:translate-x-2 4xl:ltr:translate-x-9 rtl:-translate-x-3 xl:rtl:-translate-x-7 3xl:rtl:-translate-x-2 4xl:rtl:-translate-x-9">
          <div class="progress-chart"></div>
        </div>
      </div>
    </div>

    <div class="box col-span-12 bg-n0 min-[650px]:col-span-6 3xl:col-span-3">
      <div class="bb-dashed mb-4 flex items-center justify-between pb-4">
        <span class="font-medium">{{ __('Savings Products') }}</span>
        <i class="las la-layer-group text-2xl text-primary"></i>
      </div>
      <div class="grid grid-cols-3 gap-2 text-center">
        <div class="rounded-xl bg-primary/5 p-3">
          <h5 class="h5">{{ count($user->loans) }}</h5>
          <span class="text-xs text-n700">{{ __('Loan') }}</span>
        </div>
        <div class="rounded-xl bg-[#20B757]/5 p-3">
          <h5 class="h5">{{ count($user->dps) }}</h5>
          <span class="text-xs text-n700">{{ __('DPS') }}</span>
        </div>
        <div class="rounded-xl bg-[#FFC861]/5 p-3">
          <h5 class="h5">{{ count($user->fdr) }}</h5>
          <span class="text-xs text-n700">{{ __('FDR') }}</span>
        </div>
      </div>
    </div>

    <div class="box col-span-12 overflow-x-hidden">
      <div class="bb-dashed mb-4 flex flex-wrap items-center justify-between gap-3 pb-4">
        <h4 class="h4">{{ __('Your Assets') }}</h4>
        <div class="rounded-lg border border-n30 bg-primary/5">
          <button id="one_month" class="asset-btn" type="button">1M</button>
          <button id="six_months" class="asset-btn" type="button">6M</button>
          <button id="one_year" class="asset-btn active" type="button">1Y</button>
          <button id="ytd" class="asset-btn" type="button">YTD</button>
          <button id="all" class="asset-btn" type="button">{{ __('all') }}</button>
        </div>
      </div>
      <div id="asset-chart"></div>
    </div>

    <div class="box col-span-12 bg-n0 lg:col-span-5">
      <div class="bb-dashed mb-4 pb-4">
        <h4 class="h4">{{ __('Quick Actions') }}</h4>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('user.wire.transfer.index') }}" class="flex flex-col items-center gap-3 rounded-xl border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#4371E9] text-white"><i class="las la-university text-2xl"></i></span>
          <span class="font-medium">{{ __('Wire Transfer') }}</span>
        </a>
        <a href="{{ route('tranfer.logs.index') }}" class="flex flex-col items-center gap-3 rounded-xl border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#20B757] text-white"><i class="las la-receipt text-2xl"></i></span>
          <span class="font-medium">{{ __('Transfer History') }}</span>
        </a>
        <a href="{{ route('user.beneficiaries.index') }}" class="flex flex-col items-center gap-3 rounded-xl border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#FFC861] text-white"><i class="las la-user-friends text-2xl"></i></span>
          <span class="font-medium">{{ __('Beneficiaries') }}</span>
        </a>
        <a href="{{ route('user.message.index') }}" class="flex flex-col items-center gap-3 rounded-xl border border-n30 bg-primary/5 p-4 text-center duration-300 hover:border-primary hover:bg-primary/10">
          <span class="flex size-12 items-center justify-center rounded-full bg-[#EF4444] text-white"><i class="las la-life-ring text-2xl"></i></span>
          <span class="font-medium">{{ __('Support') }}</span>
        </a>
      </div>
    </div>

    <div class="box col-span-12 overflow-x-hidden lg:col-span-7">
      <div class="bb-dashed mb-4 flex flex-wrap items-center justify-between gap-3 pb-4">
        <h4 class="h4">{{ __('Revenue vs Expenses') }}</h4>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2"><div class="size-3 rounded-full bg-[#20B757]"></div><span class="text-sm">{{ __('Credits') }}</span></div>
          <div class="flex items-center gap-2"><div class="size-3 rounded-full bg-[#FFC861]"></div><span class="text-sm">{{ __('Debits') }}</span></div>
        </div>
      </div>
      <div id="revenue-expense-chart"></div>
    </div>

    <div class="box col-span-12 lg:col-span-6">
      <div class="bb-dashed mb-4 flex flex-wrap items-center justify-between gap-4 pb-4">
        <h4 class="h4">{{ __('Recent Transaction') }}</h4>
        <a href="{{ route('user.transaction') }}" class="inline-flex items-center gap-1 font-semibold text-primary">
          {{ __('See More') }}
          <i class="las la-arrow-right"></i>
        </a>
      </div>

      @if (count($transactions) == 0)
        <p class="py-8 text-center text-n100">@lang('NO DATA FOUND')</p>
      @else
        <div class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead>
              <tr class="bg-secondary/5">
                <th class="px-6 py-5 text-start">{{ __('Type') }}</th>
                <th class="px-6 py-5 text-start">{{ __('Txnid') }}</th>
                <th class="px-6 py-5 text-start">{{ __('Amount') }}</th>
                <th class="px-6 py-5 text-start">{{ __('Date') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($transactions as $data)
                <tr class="even:bg-secondary/5">
                  <td class="px-6 py-3">
                    <div class="flex items-center gap-3">
                      <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <i class="las {{ $data->profit == 'plus' ? 'la-arrow-down' : 'la-arrow-up' }} text-xl"></i>
                      </span>
                      <span class="font-medium">{{ $data->type }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-3">{{ $data->txnid }}</td>
                  <td class="px-6 py-3">
                    <span class="{{ $data->profit == 'plus' ? 'text-[#20B757]' : 'text-[#EF4444]' }}">{{ showprice($data->amount,$currency) }}</span>
                  </td>
                  <td class="px-6 py-3">{{ date('d M Y',strtotime($data->created_at)) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    <div class="box col-span-12 lg:col-span-6">
      <div class="bb-dashed mb-4 flex flex-wrap items-center justify-between gap-4 pb-4">
        <h4 class="h4">{{ __('Recent Transfer History') }}</h4>
        <a href="{{ route('tranfer.logs.index') }}" class="inline-flex items-center gap-1 font-semibold text-primary">
          {{ __('View All') }}
          <i class="las la-arrow-right"></i>
        </a>
      </div>

      @if (count($recentTransfers) == 0)
        <p class="py-8 text-center text-n100">@lang('NO DATA FOUND')</p>
      @else
        <div class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead>
              <tr class="bg-secondary/5">
                <th class="px-6 py-5 text-start">{{ __('Receiver') }}</th>
                <th class="px-6 py-5 text-start">{{ __('Amount') }}</th>
                <th class="px-6 py-5 text-start">{{ __('Status') }}</th>
                <th class="px-6 py-5 text-start">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($recentTransfers as $data)
                @php
                  $receiverName = $data->receiver_id ? ($data->receiver->name ?? __('User Deleted')) : ($data->beneficiary->account_name ?? __('Deleted'));
                @endphp
                <tr class="even:bg-secondary/5">
                  <td class="px-6 py-3">
                    <div class="flex items-center gap-3">
                      <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <i class="las la-user text-xl"></i>
                      </span>
                      <span>
                        <span class="mb-1 block font-medium">{{ $receiverName }}</span>
                        <span class="text-xs">{{ $data->transaction_no }} - {{ $data->created_at->toFormattedDateString() }}</span>
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-3">
                    <span class="font-medium">{{ showprice($data->amount,$currency) }}</span>
                    <span class="block text-xs">{{ ucfirst($data->type) }} @lang('Bank')</span>
                  </td>
                  <td class="px-6 py-3">
                    @if ($data->status == 1)
                      <span class="status-pill status-success">@lang('Completed')</span>
                    @elseif($data->status == 2)
                      <span class="status-pill status-danger">@lang('Rejected')</span>
                    @else
                      <span class="status-pill status-warning">@lang('Pending')</span>
                    @endif
                  </td>
                  <td class="px-6 py-3">
                    <a href="{{ route('transfer.logs.show', $data->id) }}" class="btn-primary py-2 text-sm">@lang('Receipt')</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
@endsection

@push('js')
  <script>
    'use strict';

    function myFunction() {
      var copyText = document.getElementById('cronjobURL');
      copyText.select();
      copyText.setSelectionRange(0, 99999);

      if (navigator.clipboard) {
        navigator.clipboard.writeText(copyText.value);
      } else {
        document.execCommand('copy');
      }

      if (typeof toastr !== 'undefined') {
        toastr.success("{{ __('Copied') }}");
      } else {
        alert('copied');
      }
    }
  </script>
@endpush
