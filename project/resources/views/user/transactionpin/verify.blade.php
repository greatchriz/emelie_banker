@extends('layouts.user')

@section('contents')
<div class="container-xl">
    <div class="page-header d-print-none">
      <div class="row align-items-center">
        <div class="col">
          <h2 class="page-title">{{__('Authorize Transaction')}}</h2>
        </div>
      </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card p-4">
                    @includeIf('includes.flash')

                    <div class="mb-3">
                        <div class="text-muted">{{ __('Transaction Type') }}</div>
                        <div class="h3 mb-2">{{ $pending['title'] ?? __('Transfer') }}</div>
                        <div class="text-muted">{{ __('Amount') }}</div>
                        <div class="h3">{{ $pending['amount'] ?? '' }}</div>
                    </div>

                    <form action="{{ route('user.transaction.pin.verify.submit') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3 mt-3">
                            <label class="form-label required">{{__('Transaction PIN')}}</label>
                            <input name="transaction_pin" class="form-control @error('transaction_pin') is-invalid @enderror" autocomplete="off" inputmode="numeric" maxlength="4" placeholder="{{__('4 digit PIN')}}" type="password" required>
                            @error('transaction_pin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-footer d-flex gap-2">
                            <a href="{{ route('user.transaction.pin.cancel') }}" class="btn btn-outline-secondary w-50">{{__('Cancel')}}</a>
                            <button type="submit" class="btn btn-primary w-50">{{__('Authorize')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
