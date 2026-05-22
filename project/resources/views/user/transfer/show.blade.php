@extends('layouts.user')

@push('css')
<style>
  .receipt-shell { max-width: 920px; margin: 0 auto; }
  .receipt-header { background: #003399; color: #fff; border-radius: 6px 6px 0 0; }
  .receipt-title { font-size: 24px; font-weight: 700; }
  .receipt-meta { color: rgba(255,255,255,.82); }
  .receipt-total { font-size: 28px; font-weight: 700; }
  .receipt-row { display: flex; justify-content: space-between; gap: 18px; padding: 12px 0; border-bottom: 1px solid #e9edf3; }
  .receipt-row span:first-child { color: #667085; }
  .receipt-row span:last-child { color: #1f2937; font-weight: 600; text-align: right; }
  @media (max-width: 575.98px) {
    .receipt-row { display: block; }
    .receipt-row span { display: block; text-align: left !important; }
    .receipt-row span:last-child { margin-top: 4px; }
  }
</style>
@endpush

@section('contents')
@php
  $receiverName = $log->receiver_id ? ($log->receiver->name ?? __('User Deleted')) : ($log->beneficiary->account_name ?? __('Deleted'));
  $receiverAccount = $log->receiver_id ? ($log->receiver->account_number ?? __('User Deleted')) : ($log->beneficiary->account_number ?? __('Deleted'));
  $receiverBank = $log->receiver_id ? __('Own Bank') : ($log->beneficiary->bank->title ?? $log->bank->title ?? __('Other Bank'));
  $statusText = $log->status == 1 ? __('Completed') : ($log->status == 2 ? __('Rejected') : __('Pending'));
  $statusClass = $log->status == 1 ? 'success' : ($log->status == 2 ? 'danger' : 'warning');
@endphp

<div class="container-xl">
  <div class="page-header d-print-none">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">{{ __('Transfer History') }}</div>
        <h2 class="page-title">{{ __('Transfer Receipt') }}</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="{{ route('transfer.logs.download', $log->id) }}" class="btn btn-primary">
            <i class="fas fa-download me-2"></i>{{ __('Download Receipt') }}
          </a>
          <a href="{{ route('tranfer.logs.index') }}" class="btn btn-outline-secondary">
            {{ __('Back') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="receipt-shell">
      <div class="card">
        <div class="receipt-header p-4">
          <div class="d-flex flex-wrap justify-content-between gap-3">
            <div>
              <div class="receipt-title">{{ __('Transfer Receipt') }}</div>
              <div class="receipt-meta">{{ __('Transaction') }} #{{ $log->transaction_no }}</div>
              <div class="receipt-meta">{{ $log->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="text-end">
              <div class="receipt-meta">{{ __('Amount Sent') }}</div>
              <div class="receipt-total">{{ showprice($log->amount,$currency) }}</div>
              <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
            </div>
          </div>
        </div>

        <div class="card-body p-4">
          <div class="row g-4">
            <div class="col-md-6">
              <h3 class="mb-3">{{ __('Sender Details') }}</h3>
              <div class="receipt-row"><span>{{ __('Name') }}</span><span>{{ $log->user->name }}</span></div>
              <div class="receipt-row"><span>{{ __('Email') }}</span><span>{{ $log->user->email }}</span></div>
              <div class="receipt-row"><span>{{ __('Account Number') }}</span><span>{{ $log->user->account_number }}</span></div>
            </div>
            <div class="col-md-6">
              <h3 class="mb-3">{{ __('Receiver Details') }}</h3>
              <div class="receipt-row"><span>{{ __('Name') }}</span><span>{{ $receiverName }}</span></div>
              <div class="receipt-row"><span>{{ __('Account Number') }}</span><span>{{ $receiverAccount }}</span></div>
              <div class="receipt-row"><span>{{ __('Bank') }}</span><span>{{ $receiverBank }}</span></div>
            </div>
          </div>

          <div class="mt-4">
            <h3 class="mb-3">{{ __('Payment Analysis') }}</h3>
            <div class="receipt-row"><span>{{ __('Transfer Type') }}</span><span>{{ ucfirst($log->type) }} {{ __('Bank Transfer') }}</span></div>
            <div class="receipt-row"><span>{{ __('Transfer Amount') }}</span><span>{{ showprice($log->amount,$currency) }}</span></div>
            <div class="receipt-row"><span>{{ __('Charge') }}</span><span>{{ showprice($log->cost,$currency) }}</span></div>
            <div class="receipt-row"><span>{{ __('Recipient Receives') }}</span><span>{{ showprice($log->final_amount,$currency) }}</span></div>
            <div class="receipt-row"><span>{{ __('Status') }}</span><span>{{ $statusText }}</span></div>
            <div class="receipt-row"><span>{{ __('Receipt Generated') }}</span><span>{{ now()->format('d M Y, h:i A') }}</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
