@php
  $accountSelectName = $name ?? 'account_id';
  $accountSelectLabel = $label ?? __('Account');
  $accountSelectRequired = $required ?? true;
  $selectedAccountId = old($accountSelectName, optional($selectedAccount ?? null)->id ?? request($accountSelectName));
  $accountSelectCurrency = $currency ?? ($defaultCurrency ?? null);
@endphp

<div class="form-group mb-3">
  <label class="form-label {{ $accountSelectRequired ? 'required' : '' }}">{{ $accountSelectLabel }}</label>
  <select name="{{ $accountSelectName }}" class="form-select" {{ $accountSelectRequired ? 'required' : '' }}>
    <option value="">{{ __('Select Account') }}</option>
    @foreach(($accounts ?? collect()) as $account)
      <option value="{{ $account->id }}" {{ (string) $selectedAccountId === (string) $account->id ? 'selected' : '' }}>
        {{ $account->label ?: __('Account') }} - {{ $account->account_number }}
        @if($accountSelectCurrency)
          ({{ showprice($account->balance, $accountSelectCurrency) }})
        @endif
      </option>
    @endforeach
  </select>
  @if(($accounts ?? collect())->isEmpty())
    <small class="text-danger">{{ __('No active account is available. Please request an account or contact support.') }}</small>
  @endif
</div>
