@extends('layouts.user')

@section('contents')
<div class="container-xl">
  <div class="page-header d-print-none">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">{{ __('Overview') }}</div>
        <h2 class="page-title">{{ __('My Accounts') }}</h2>
      </div>
      <div class="col-auto ms-auto">
        <a href="{{ route('user.accounts.create') }}" class="btn btn-primary">{{ __('Create Account') }}</a>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    @includeIf('includes.flash')
    <div class="row row-cards">
      @forelse($accounts as $account)
        <div class="col-sm-6 col-xl-4">
          <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start gap-3">
              <div>
                <h3 class="mb-1">{{ $account->label ?: __('Account') }}</h3>
                <p class="text-muted mb-2">{{ $account->account_number }}</p>
              </div>
              <span class="badge bg-{{ $account->status == 'active' ? 'success' : ($account->status == 'pending' ? 'warning' : 'danger') }}">
                {{ ucfirst($account->status) }}
              </span>
            </div>
            <h2 class="my-3">{{ showprice($account->balance, $currency) }}</h2>
            <p class="mb-1">{{ __('Plan') }}: {{ $account->plan->title ?? __('No Plan') }}</p>
            <p class="mb-3">{{ __('Type') }}: {{ $account->is_default ? __('Default') : __('Additional') }}</p>
            <div class="d-flex flex-wrap gap-2">
              <a href="{{ route('user.accounts.show', $account->id) }}" class="btn btn-outline-primary">{{ __('Details') }}</a>
              @if($account->status == 'active' && optional($activeAccount)->id != $account->id)
                <a href="{{ route('user.accounts.switch', $account->id) }}" class="btn btn-primary">{{ __('Use Account') }}</a>
              @elseif(optional($activeAccount)->id == $account->id)
                <span class="btn btn-success disabled">{{ __('Active') }}</span>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card p-5 text-center">
            <h3>{{ __('No accounts found.') }}</h3>
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
