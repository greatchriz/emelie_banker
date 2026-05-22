@extends('layouts.admin')

@section('content')

<div class="card">
  <div class="d-sm-flex align-items-center justify-content-between py-3">
    <h5 class="mb-0 text-gray-800 pl-3">{{ __('User Transfer Access') }}</h5>
    <ol class="breadcrumb py-0 m-0">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
      <li class="breadcrumb-item"><a href="{{ route('user.transfer.access') }}">{{ __('User Transfer Access') }}</a></li>
    </ol>
  </div>
</div>

<div class="row mt-3">
  <div class="col-lg-12">
    <div class="card mb-4">
      <div class="table-responsive p-3">
        <table class="table table-hover">
          <thead class="thead-light">
            <tr>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Email') }}</th>
              <th>{{ __('Account Number') }}</th>
              <th>{{ __('Transfer Access') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->account_number }}</td>
                <td>
                  <form action="{{ route('user.transfer.access.toggle', $user->id) }}" method="POST">
                    @csrf
                    <div class="custom-control custom-switch">
                      <input
                        type="checkbox"
                        name="transfer_access"
                        value="1"
                        class="custom-control-input transfer-access-toggle"
                        id="transfer_access_{{ $user->id }}"
                        {{ $user->transfer_access ? 'checked' : '' }}>
                      <label class="custom-control-label" for="transfer_access_{{ $user->id }}">
                        {{ $user->transfer_access ? __('On') : __('Off') }}
                      </label>
                    </div>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center">{{ __('No users found.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        {{ $users->links() }}
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  'use strict';

  $('.transfer-access-toggle').on('change', function () {
    this.form.submit();
  });
</script>
@endsection
