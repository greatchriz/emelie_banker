@extends('layouts.admin')

@section('content')
<div class="card">
  <div class="d-sm-flex align-items-center justify-content-between py-3">
    <h5 class="mb-0 text-gray-800 pl-3">{{ __('User Accounts') }}</h5>
    <a href="{{ route('admin.user.accounts.create') }}" class="btn btn-primary btn-sm mr-3">{{ __('Create Account') }}</a>
  </div>
</div>

<div class="card mt-3">
  <div class="card-body">
    @include('includes.admin.form-success')
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>{{ __('User') }}</th>
            <th>{{ __('Account') }}</th>
            <th>{{ __('Balance') }}</th>
            <th>{{ __('Plan') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($accounts as $account)
            <tr>
              <td>
                {{ $account->user->name }}<br>
                <small>{{ $account->user->email }}</small>
              </td>
              <td>
                {{ $account->account_number }}
                @if($account->is_default)
                  <span class="badge badge-info">{{ __('Default') }}</span>
                @endif
                <br><small>{{ $account->label }}</small>
              </td>
              <td>{{ $account->balance }}</td>
              <td>{{ $account->plan->title ?? __('No Plan') }}</td>
              <td><span class="badge badge-{{ $account->status == 'active' ? 'success' : ($account->status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($account->status) }}</span></td>
              <td>
                <div class="btn-group">
                  <a href="{{ route('admin.user.accounts.edit', $account->id) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a>
                  @if($account->status != 'active')
                    <a href="{{ route('admin.user.accounts.status', [$account->id, 'active']) }}" class="btn btn-success btn-sm">{{ __('Enable') }}</a>
                  @endif
                  @if($account->status != 'disabled')
                    <a href="{{ route('admin.user.accounts.status', [$account->id, 'disabled']) }}" class="btn btn-warning btn-sm">{{ __('Disable') }}</a>
                  @endif
                  @if($account->status == 'pending')
                    <a href="{{ route('admin.user.accounts.status', [$account->id, 'rejected']) }}" class="btn btn-danger btn-sm">{{ __('Reject') }}</a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center">{{ __('No accounts found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $accounts->links() }}
  </div>
</div>
@endsection
