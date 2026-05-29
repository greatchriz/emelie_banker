@extends('layouts.admin')

@section('content')
<div class="card">
  <div class="d-sm-flex align-items-center justify-content-between py-3">
    <h5 class="mb-0 text-gray-800 pl-3">{{ __('Create User Account') }}</h5>
  </div>
</div>

<div class="card mt-3">
  <div class="card-body">
    <form action="{{ route('admin.user.accounts.store') }}" method="POST">
      @csrf
      <div class="form-group">
        <label>{{ __('User') }}</label>
        <select name="user_id" class="form-control" required>
          @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>{{ __('Label') }}</label>
        <input type="text" name="label" class="form-control">
      </div>
      <div class="form-group">
        <label>{{ __('Bank Plan') }}</label>
        <select name="bank_plan_id" class="form-control">
          <option value="">{{ __('No Plan') }}</option>
          @foreach($plans as $plan)
            <option value="{{ $plan->id }}">{{ $plan->title }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>{{ __('Opening Balance') }}</label>
        <input type="number" step="0.01" min="0" name="balance" class="form-control" value="0">
      </div>
      <div class="form-group">
        <label>{{ __('Status') }}</label>
        <select name="status" class="form-control" required>
          <option value="active">{{ __('Active') }}</option>
          <option value="pending">{{ __('Pending') }}</option>
          <option value="disabled">{{ __('Restricted') }}</option>
          <option value="rejected">{{ __('Rejected') }}</option>
        </select>
      </div>
      <button class="btn btn-primary">{{ __('Create') }}</button>
    </form>
  </div>
</div>
@endsection
