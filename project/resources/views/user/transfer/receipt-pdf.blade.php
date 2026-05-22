@php
  $receiverName = $log->receiver_id ? ($log->receiver->name ?? __('User Deleted')) : ($log->beneficiary->account_name ?? __('Deleted'));
  $receiverAccount = $log->receiver_id ? ($log->receiver->account_number ?? __('User Deleted')) : ($log->beneficiary->account_number ?? __('Deleted'));
  $receiverBank = $log->receiver_id ? __('Own Bank') : ($log->beneficiary->bank->title ?? $log->bank->title ?? __('Other Bank'));
  $statusText = $log->status == 1 ? __('Completed') : ($log->status == 2 ? __('Rejected') : __('Pending'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ __('Transfer Receipt') }} - {{ $log->transaction_no }}</title>
  <style>
    body { margin: 0; padding: 28px; color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 13px; background: #ffffff; }
    .receipt { border: 1px solid #d7deea; }
    .header { background: #003399; color: #ffffff; padding: 24px; }
    .title { font-size: 24px; font-weight: bold; margin-bottom: 8px; }
    .muted { color: #dce5ff; }
    .amount { font-size: 26px; font-weight: bold; margin-top: 10px; }
    .body { padding: 24px; }
    .section-title { font-size: 16px; font-weight: bold; color: #003399; margin: 18px 0 10px; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 9px 0; border-bottom: 1px solid #e9edf3; vertical-align: top; }
    td:first-child { color: #667085; width: 42%; }
    td:last-child { font-weight: bold; text-align: right; }
    .columns { width: 100%; }
    .columns td { border-bottom: 0; padding: 0; width: 50%; }
    .panel { padding-right: 18px; }
    .footer { padding: 16px 24px; background: #f8fafc; color: #667085; font-size: 12px; }
  </style>
</head>
<body>
  <div class="receipt">
    <div class="header">
      <div class="title">{{ __('Transfer Receipt') }}</div>
      <div class="muted">{{ __('Transaction') }} #{{ $log->transaction_no }}</div>
      <div class="muted">{{ $log->created_at->format('d M Y, h:i A') }}</div>
      <div class="amount">{{ showprice($log->amount,$currency) }}</div>
      <div>{{ __('Status') }}: {{ $statusText }}</div>
    </div>

    <div class="body">
      <table class="columns">
        <tr>
          <td>
            <div class="panel">
              <div class="section-title">{{ __('Sender Details') }}</div>
              <table>
                <tr><td>{{ __('Name') }}</td><td>{{ $log->user->name }}</td></tr>
                <tr><td>{{ __('Email') }}</td><td>{{ $log->user->email }}</td></tr>
                <tr><td>{{ __('Account Number') }}</td><td>{{ $log->user->account_number }}</td></tr>
              </table>
            </div>
          </td>
          <td>
            <div>
              <div class="section-title">{{ __('Receiver Details') }}</div>
              <table>
                <tr><td>{{ __('Name') }}</td><td>{{ $receiverName }}</td></tr>
                <tr><td>{{ __('Account Number') }}</td><td>{{ $receiverAccount }}</td></tr>
                <tr><td>{{ __('Bank') }}</td><td>{{ $receiverBank }}</td></tr>
              </table>
            </div>
          </td>
        </tr>
      </table>

      <div class="section-title">{{ __('Payment Analysis') }}</div>
      <table>
        <tr><td>{{ __('Transfer Type') }}</td><td>{{ ucfirst($log->type) }} {{ __('Bank Transfer') }}</td></tr>
        <tr><td>{{ __('Transfer Amount') }}</td><td>{{ showprice($log->amount,$currency) }}</td></tr>
        <tr><td>{{ __('Charge') }}</td><td>{{ showprice($log->cost,$currency) }}</td></tr>
        <tr><td>{{ __('Recipient Receives') }}</td><td>{{ showprice($log->final_amount,$currency) }}</td></tr>
        <tr><td>{{ __('Status') }}</td><td>{{ $statusText }}</td></tr>
        <tr><td>{{ __('Receipt Generated') }}</td><td>{{ now()->format('d M Y, h:i A') }}</td></tr>
      </table>
    </div>

    <div class="footer">
      {{ __('This receipt was generated electronically and is valid without a signature.') }}
    </div>
  </div>
</body>
</html>
