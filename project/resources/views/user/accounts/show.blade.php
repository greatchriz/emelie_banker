@extends('layouts.user')

@section('contents')
<div class="container-xl">
  <div class="page-header d-print-none">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">{{ $account->account_number }}</div>
        <h2 class="page-title">{{ $account->label ?: __('Account Details') }}</h2>
      </div>
      <div class="col-auto ms-auto">
        @if($account->status == 'active')
          <a href="{{ route('user.accounts.switch', $account->id) }}" class="btn btn-primary">{{ __('Use Account') }}</a>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row row-cards">
      <div class="col-lg-4">
        <div class="card p-4">
          <p>{{ __('Status') }}: <span class="badge bg-{{ $account->status == 'active' ? 'success' : ($account->status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($account->status) }}</span></p>
          <h2>{{ showprice($account->balance, $currency) }}</h2>
          <p>{{ __('Plan') }}: {{ $account->plan->title ?? __('No Plan') }}</p>
          <p>{{ __('Type') }}: {{ $account->is_default ? __('Default Account') : __('Additional Account') }}</p>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card">
          <div class="table-responsive">
            <table class="table table-vcenter card-table">
              <thead>
                <tr>
                  <th>{{ __('Date') }}</th>
                  <th>{{ __('Type') }}</th>
                  <th>{{ __('Amount') }}</th>
                  <th>{{ __('Txn') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse($transactions as $transaction)
                  <tr>
                    <td>{{ $transaction->created_at->toFormattedDateString() }}</td>
                    <td>{{ $transaction->type }}</td>
                    <td>{{ showprice($transaction->amount, $currency) }}</td>
                    <td>{{ $transaction->txnid }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center py-5">{{ __('No transactions found.') }}</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $transactions->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
