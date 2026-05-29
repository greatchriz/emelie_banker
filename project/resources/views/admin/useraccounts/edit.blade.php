@extends('layouts.admin')

@section('content')
<div class="card">
  <div class="d-sm-flex align-items-center justify-content-between py-3">
    <h5 class="mb-0 text-gray-800 pl-3">{{ __('Edit Account') }} - {{ $account->account_number }}</h5>
  </div>
</div>

<div class="card mt-3">
  <div class="card-body">
    <form action="{{ route('admin.user.accounts.update', $account->id) }}" method="POST">
      @csrf
      <div class="form-group">
        <label>{{ __('User') }}</label>
        <input type="text" class="form-control" value="{{ $account->user->name }} - {{ $account->user->email }}" readonly>
      </div>
      <div class="form-group">
        <label>{{ __('Label') }}</label>
        <input type="text" name="label" class="form-control" value="{{ $account->label }}">
      </div>
      <div class="form-group">
        <label>{{ __('Bank Plan') }}</label>
        <select name="bank_plan_id" class="form-control">
          <option value="">{{ __('No Plan') }}</option>
          @foreach($plans as $plan)
            <option value="{{ $plan->id }}" {{ $account->bank_plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->title }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>{{ __('Balance') }}</label>
        <input type="number" step="0.01" min="0" name="balance" class="form-control" value="{{ $account->balance }}" required>
      </div>
      <div class="form-group">
        <label>{{ __('Plan End Date') }}</label>
        <input type="date" name="plan_end_date" class="form-control" value="{{ optional($account->plan_end_date)->format('Y-m-d') }}">
      </div>
      <div class="form-group">
        <label>{{ __('Status') }}</label>
        <select name="status" class="form-control" required>
          @foreach(['pending','active','disabled','rejected'] as $status)
            <option value="{{ $status }}" {{ $account->status == $status ? 'selected' : '' }}>{{ $status === 'disabled' ? __('Restricted') : ucfirst($status) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>{{ __('Admin Note') }}</label>
        <textarea name="admin_note" class="form-control" rows="4">{{ $account->admin_note }}</textarea>
      </div>
      <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
  </div>
</div>
@endsection
