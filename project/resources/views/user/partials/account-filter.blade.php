@php
  $filterAction = $action ?? url()->current();
@endphp

<form method="GET" action="{{ $filterAction }}" class="mb-3">
  <div class="row g-2 align-items-end">
    <div class="col-md-6 col-lg-4">
      <label class="form-label">{{ __('Filter by Account') }}</label>
      <select name="account_id" class="form-select">
        <option value="">{{ __('All Accounts') }}</option>
        @foreach(($accounts ?? collect()) as $account)
          <option value="{{ $account->id }}" {{ optional($selectedAccount ?? null)->id == $account->id ? 'selected' : '' }}>
            {{ $account->label ?: __('Account') }} - {{ $account->account_number }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-auto">
      <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
      <a href="{{ $filterAction }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
    </div>
  </div>
</form>
