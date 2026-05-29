@extends('layouts.user')

@section('contents')
@php
  $summary = $transferSummary ?? ['total' => 0, 'completed' => 0, 'pending' => 0, 'amount' => 0];
@endphp

<div class="user-dashboard mb-6 flex flex-wrap items-center justify-between gap-4 lg:mb-8">
  <div>
    <h3>{{ __('Transfer History') }}</h3>
    <p class="mt-1 text-sm text-n100">{{ __('Review and track every transfer from your accounts.') }}</p>
  </div>
  <a href="{{ route('send.money.create', request()->only('account_id')) }}" class="btn-primary">
    <i class="las la-paper-plane text-base md:text-lg"></i>
    {{ __('Send Money') }}
  </a>
</div>

<div class="grid grid-cols-12 gap-4 xxl:gap-6">
  <div class="box col-span-12 bg-n0">
    <form method="GET" action="{{ route('tranfer.logs.index') }}" class="flex flex-wrap items-end gap-3">
      <div class="min-w-[240px] flex-1">
        <label class="mb-2 block text-sm font-medium text-n700">{{ __('Filter by Account') }}</label>
        <select name="account_id" class="w-full rounded-lg border border-n30 bg-secondary/5 px-4 py-3 text-sm outline-none">
          <option value="">{{ __('All Accounts') }}</option>
          @foreach(($accounts ?? collect()) as $account)
            <option value="{{ $account->id }}" {{ optional($selectedAccount ?? null)->id == $account->id ? 'selected' : '' }}>
              {{ $account->label ?: __('Account') }} - {{ $account->account_number }}
            </option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn-primary h-12">{{ __('Apply') }}</button>
      <a href="{{ route('tranfer.logs.index') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-n30 px-5 font-semibold text-primary">{{ __('Reset') }}</a>
    </form>
  </div>

  <div class="col-span-12 grid grid-cols-2 gap-3 lg:grid-cols-4">
    <div class="rounded-lg border border-n30 bg-n0 p-4">
      <p class="text-xs text-n100">{{ __('Total Transfers') }}</p>
      <h4 class="mt-2 text-2xl font-semibold">{{ $summary['total'] }}</h4>
    </div>
    <div class="rounded-lg border border-[#20B757]/20 bg-[#20B757]/5 p-4">
      <p class="text-xs text-n100">{{ __('Completed') }}</p>
      <h4 class="mt-2 text-2xl font-semibold text-[#20B757]">{{ $summary['completed'] }}</h4>
    </div>
    <div class="rounded-lg border border-[#FFC861]/30 bg-[#FFC861]/10 p-4">
      <p class="text-xs text-n100">{{ __('Pending') }}</p>
      <h4 class="mt-2 text-2xl font-semibold text-[#8A6100]">{{ $summary['pending'] }}</h4>
    </div>
    <div class="rounded-lg border border-primary/20 bg-primary/5 p-4">
      <p class="text-xs text-n100">{{ __('Total Sent') }}</p>
      <h4 class="mt-2 text-2xl font-semibold">{{ showprice($summary['amount'], $currency) }}</h4>
    </div>
  </div>

  <div class="box col-span-12 bg-n0">
    <div class="bb-dashed mb-4 flex flex-wrap items-center justify-between gap-3 pb-4">
      <div>
        <h4 class="h4">{{ __('All Transfers') }}</h4>
        <p class="mt-1 text-sm text-n100">
          {{ $selectedAccount ? __('Showing transfers for the selected account.') : __('Showing transfers from all accounts.') }}
        </p>
      </div>
    </div>

    @if ($logs->isEmpty())
      <div class="rounded-lg border border-dashed border-n30 bg-secondary/5 p-8 text-center">
        <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-primary/10 text-primary">
          <i class="las la-exchange-alt text-3xl"></i>
        </span>
        <h4 class="mt-4 h4">{{ __('No transfer history found') }}</h4>
        <p class="mt-2 text-sm text-n700">{{ __('Transfers will appear here after you send money from your account.') }}</p>
      </div>
    @else
      <div class="space-y-3">
        @foreach($logs as $data)
          @php
            $isOwnBank = (bool) $data->receiver_id;
            $recipientName = $isOwnBank ? ($data->receiver->name ?? __('User Deleted')) : ($data->beneficiary->account_name ?? __('Deleted'));
            $recipientAccount = $isOwnBank ? ($data->receiver->account_number ?? __('User Deleted')) : ($data->beneficiary->account_number ?? __('Deleted'));
            $recipientBank = $isOwnBank ? __('Own Bank') : ($data->beneficiary->bank->title ?? $data->bank->title ?? __('Other Bank'));
            $statusText = $data->status == 1 ? __('Completed') : ($data->status == 2 ? __('Rejected') : __('Pending'));
            $statusClass = $data->status == 1
              ? 'bg-[#20B757]/10 text-[#20B757]'
              : ($data->status == 2 ? 'bg-[#EF4444]/10 text-[#EF4444]' : 'bg-[#FFC861]/15 text-[#8A6100]');
          @endphp

          <div class="rounded-lg border border-n30 bg-secondary/5 p-4 duration-300 hover:border-primary/30 hover:bg-primary/5">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
              <div class="flex min-w-0 items-start gap-3">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                  <i class="las {{ $isOwnBank ? 'la-university' : 'la-paper-plane' }} text-2xl"></i>
                </span>
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <h5 class="font-semibold">{{ $recipientName }}</h5>
                    <span class="{{ $statusClass }} rounded-full px-3 py-1 text-xs font-semibold">{{ $statusText }}</span>
                  </div>
                  <p class="mt-1 text-sm text-n700">{{ $recipientBank }} - {{ $recipientAccount }}</p>
                  <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-n100">
                    <span>{{ __('Txn') }}: {{ $data->transaction_no }}</span>
                    <span>{{ __('From') }}: {{ $data->account->account_number ?: __('Account unavailable') }}</span>
                    <span>{{ $data->created_at->format('d M Y, h:i A') }}</span>
                  </div>
                </div>
              </div>

              <div class="flex shrink-0 items-center justify-between gap-4 md:justify-end">
                <div class="text-left md:text-right">
                  <p class="text-xs text-n100">{{ ucfirst($data->type) }} {{ __('Transfer') }}</p>
                  <p class="mt-1 text-lg font-semibold">{{ showprice($data->amount, $currency) }}</p>
                </div>
                <a href="{{ route('transfer.logs.show', $data->id) }}" class="inline-flex size-10 items-center justify-center rounded-full bg-primary text-white" aria-label="{{ __('View details') }}">
                  <i class="las la-arrow-right text-xl"></i>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-5">
        {{ $logs->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
