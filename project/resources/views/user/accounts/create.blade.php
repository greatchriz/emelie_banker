@extends('layouts.user')

@section('contents')
<div class="container-xl">
  <div class="page-header d-print-none">
    <div class="row align-items-center">
      <div class="col">
        <h2 class="page-title">{{ __('Request New Account') }}</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    @includeIf('includes.flash')
    <div class="row row-cards">
      <div class="col-lg-8">
        <div class="card p-4">
          <form action="{{ route('user.accounts.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
              <label class="form-label">{{ __('Account Label') }}</label>
              <input type="text" name="label" class="form-control" value="{{ old('label') }}" placeholder="{{ __('Savings, Business, Travel...') }}">
            </div>
            <div class="form-group mb-3">
              <label class="form-label">{{ __('Bank Plan') }}</label>
              <select name="bank_plan_id" class="form-select">
                <option value="">{{ __('Use my current/default plan') }}</option>
                @foreach($plans as $plan)
                  <option value="{{ $plan->id }}" {{ old('bank_plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->title }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">{{ __('Submit Request') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
