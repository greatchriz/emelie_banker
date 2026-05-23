@extends('layouts.user')

@section('contents')
<div class="container-xl">
    <div class="page-header d-print-none">
      <div class="row align-items-center">
        <div class="col">
          <h2 class="page-title">{{__('Setup Transaction PIN')}}</h2>
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
                    <form action="{{ route('user.transaction.pin.setup.submit') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3 mt-3">
                            <label class="form-label required">{{__('Transaction PIN')}}</label>
                            <input name="transaction_pin" class="form-control" autocomplete="off" inputmode="numeric" maxlength="4" placeholder="{{__('4 digit PIN')}}" type="password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label required">{{__('Confirm Transaction PIN')}}</label>
                            <input name="transaction_pin_confirmation" class="form-control" autocomplete="off" inputmode="numeric" maxlength="4" placeholder="{{__('Confirm 4 digit PIN')}}" type="password" required>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">{{__('Submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
