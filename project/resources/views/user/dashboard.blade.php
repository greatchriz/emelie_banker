@extends('layouts.user')

@push('css')
    
@endpush

@section('contents')
<div class="container-xl">

    <div class="page-header d-print-none">

    </div>
  </div>
  <div class="page-body">
    <div class="container-xl">

      @if (auth()->user()->kyc_status != 1)  
        <div class="row mb-3">
          <div class="col-md-12">
              <div class="card">
                  <div class="card-body">
                        <div class="form-group w-100 d-flex flex-wrap align-items-center justify-content-evenly justify-content-sm-between">
                          <h3 class="my-1 text-center text-sm-start">{{ __('You have a information to submit for kyc verification.') }}</h3>
                          <div class="my-1">
                            <a href="{{ route('user.kyc.form') }}" class="btn btn-warning">@lang('Submit')</a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      @endif

      <div class="row row-deck row-cards mb-2">

        <div class="col-sm-6 col-md-6">
          <div class="card mb-2">
            <div class="card-body p-3 p-md-4">
                <div class="balence--item">
                  <div class="icon">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="content">
                    <div class="subheader">{{__('Account Number')}}</div>
                    <div class="h1 mb-0 mt-2">{{ $user->account_number }}</div>
                  </div>
                </div>
              </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-6">
          <div class="card mb-2">
              <div class="card-body p-3 p-md-4">
                <div class="balence--item">
                  <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                  </div>
                  <div class="content">
                    <div class="subheader">{{__('Available Balance')}}</div>
                    <div class="h1 mb-0 mt-2">{{ showprice($user->balance,$currency) }}</div>
                  </div>
                </div>
              </div>
          </div>
        </div>

      </div>

      <div class="row justify-content-center">
        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-money-check"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->deposits) }}</div>
              <div class="text-muted">@lang('Deposits')</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="card-body p-3 p-md-4">
              <div class="h1 m-0">{{ count($user->withdraws) }}</div>
              <div class="text-muted">@lang('Withdraws')</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->transactions) }}</div>
              <div class="text-muted">@lang('Transactions')</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->loans) }}</div>
              <div class="text-muted">@lang('Loan')</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-wallet"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->dps) }}</div>
              <div class="text-muted">@lang('DPS')</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="far fa-credit-card"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->fdr) }}</div>
              <div class="text-muted">@lang('FDR')</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <p>{{ __('Your Referral Link') }}</p>
                        <div class="input-group input--group">
                            <input type="text" name="key" value="{{ url('/').'?reff='.$user->affilate_code}}" class="form-control" id="cronjobURL" readonly>
                            <button class="btn btn-sm copytext input-group-text" id="copyBoard" onclick="myFunction()"> <i class="fa fa-copy"></i> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">@lang('Recent Transaction')</h3>
            </div>

            @if (count($transactions) == 0)
                <p class="text-center p-2">@lang('NO DATA FOUND')</p>
              @else 
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-mobile-md text-nowrap datatable">
                  <thead>
                    <tr>
                      <th class="w-1">@lang('No'). 
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-dark icon-thick" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="6 15 12 9 18 15" /></svg>
                      </th>
                      <th>@lang('Type')</th>
                      <th>@lang('Txnid')</th>
                      <th>@lang('Amount')</th>
                      <th>@lang('Date')</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($transactions as $key=>$data)
                      <tr>
                        <td data-label="@lang('No')">
                          <div>
  
                            <span class="text-muted">{{ $loop->iteration }}</span>
                          </div>
                        </td>
  
                        <td data-label="@lang('Type')">
                          <div>
                            {{ $data->type }}
                          </div>
                        </td>
  
                        <td data-label="@lang('Txnid')">
                          <div>
                            {{ $data->txnid }}
                          </div>
                        </td>
  
                        <td data-label="@lang('Amount')">
                          <div>
                            <p class="m-0 text-{{ $data->profit == 'plus' ? 'success' : 'danger'}}">{{ showprice($data->amount,$currency) }}</p>
                          </div>
                        </td>
  
                        <td data-label="@lang('Date')">
                          <div>
                            {{date('d M Y',strtotime($data->created_at))}}
                          </div>
                        </td>
                        
                      </tr>
                    @endforeach
  
                  </tbody>
                </table>
              </div>
            @endif

          </div>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title">@lang('Recent Transfer History')</h3>
              <a href="{{ route('tranfer.logs.index') }}" class="btn btn-outline-primary btn-sm">@lang('View All')</a>
            </div>

            @if (count($recentTransfers) == 0)
              <p class="text-center p-2">@lang('NO DATA FOUND')</p>
            @else
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-mobile-md text-nowrap">
                  <thead>
                    <tr>
                      <th>@lang('Date')</th>
                      <th>@lang('Transaction Number')</th>
                      <th>@lang('Receiver')</th>
                      <th>@lang('Type')</th>
                      <th>@lang('Amount')</th>
                      <th>@lang('Status')</th>
                      <th>@lang('Action')</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($recentTransfers as $data)
                      @php
                        $receiverName = $data->receiver_id ? ($data->receiver->name ?? __('User Deleted')) : ($data->beneficiary->account_name ?? __('Deleted'));
                      @endphp
                      <tr>
                        <td data-label="@lang('Date')">{{ $data->created_at->toFormattedDateString() }}</td>
                        <td data-label="@lang('Transaction Number')">{{ $data->transaction_no }}</td>
                        <td data-label="@lang('Receiver')">{{ $receiverName }}</td>
                        <td data-label="@lang('Type')">{{ ucfirst($data->type) }} @lang('Bank')</td>
                        <td data-label="@lang('Amount')">{{ showprice($data->amount,$currency) }}</td>
                        <td data-label="@lang('Status')">
                          @if ($data->status == 1)
                            <span class="badge bg-success">@lang('Completed')</span>
                          @elseif($data->status == 2)
                            <span class="badge bg-danger">@lang('Rejected')</span>
                          @else
                            <span class="badge bg-warning">@lang('Pending')</span>
                          @endif
                        </td>
                        <td data-label="@lang('Action')">
                          <a href="{{ route('transfer.logs.show', $data->id) }}" class="btn btn-primary btn-sm">@lang('Receipt')</a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection

@push('js')
    <script>
      'use strict';

      function myFunction() {
        var copyText = document.getElementById("cronjobURL");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert('copied');
    }
    </script>
@endpush
