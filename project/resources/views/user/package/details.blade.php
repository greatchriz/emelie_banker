@extends('layouts.user')

@push('css')
    
@endpush

@section('contents')
<div class="container-xl">
    <div class="page-header d-print-none">
      <div class="row align-items-center">
        <div class="col">
          <h2 class="page-title">
            {{__('Package Details')}}
          </h2>
        </div>

      </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th class="45%" width="45%">{{__('Plan Title')}}</th>
                            <td width="10%">:</td>
                            <td class="45%" width="45%">{{ $data->title }}</td>
                        </tr>

                        <tr>
                            <th class="45%" width="45%">{{__('Plan Amount')}}</th>
                            <td width="10%">:</td>
                            <td class="45%" width="45%">{{ showPrice($data->amount,$currency) }}</td>
                        </tr>

                        <tr>
                            <th class="45%" width="45%">{{__('Maximum Send Money')}} (@lang('Daily'))</th>
                            <td width="10%">:</td>
                            <td class="45%" width="45%">{{ showPrice($data->daily_send,$currency) }}</td>
                        </tr>

                        <tr>
                            <th class="45%" width="45%">{{__('Maximum Send Money')}} (@lang('Monthly'))</th>
                            <td width="10%">:</td>
                            <td class="45%" width="45%">{{ showPrice($data->monthly_send,$currency) }}</td>
                        </tr>

                        <tr>
                            <th class="45%" width="45%">{{__('Maximum Receive Money')}} (@lang('Daily'))</th>
                            <td width="10%">:</td>
                            <td class="45%" width="45%">{{ showPrice($data->daily_receive,$currency) }}</td>
                        </tr>

                        <tr>
                            <th class="45%" width="45%">{{__('Maximum Receive Money')}} (@lang('Monthly'))</th>
                            <td width="10%">:</td>
                            <td class="45%" width="45%">{{ showPrice($data->monthly_receive,$currency) }}</td>
                        </tr>

                        <tr>
                            <th class="45%" width="45%">{{__('Maximum Withdraw Amount')}} (@lang('Daily'))</th>
                            <td width="10%">:</td>
                            <td class="45%" width="45%">{{ showPrice($data->daily_withdraw,$currency) }}</td>
                        </tr>

                        <tr>
                            <th class="45%" width="45%">{{__('Maximum Withdraw Amount')}} (@lang('Monthly'))</th>
                            <td width="10%">:</td>
                            <td class="45%" width="45%">{{ showPrice($data->monthly_withdraw,$currency) }}</td>
                        </tr>

                        <tr>
                            <th width="45%">{{__('Maximum Loan Amount')}} (@lang('Monthly'))</th>
                            <td width="10%">:</td>
                            <td width="45%">{{ showPrice($data->loan_amount,$currency) }}</td>
                        </tr>

                        <tr>
                            <th width="45%">{{__('End Days')}}</th>
                            <td width="10%">:</td>
                            <td width="45%">{{ $data->days }} @lang('Days')</td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    <form id="subscription-form" action="{{ route('subscription.free.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if ($data->amount != 0)
                            <div class="form-group">
                                <label class="form-label required">{{__('Payment Method')}}</label>
                                <select name="method" id="subscriptionMethod" class="form-select" required>
                                    <option value="">{{ __('Select Payment Method') }}</option>
                                    @foreach ($gateways as $gateway)
                                        @if (in_array($gateway->keyword,$availableGatways))
                                            @if ($gateway->type == 'manual')
                                                <option value="Manual" data-details="{{$gateway->details}}">{{ $gateway->title }}</option>
                                            @else
                                                <option value="{{$gateway->keyword}}">{{ $gateway->name }}</option>
                                            @endif
                                        @endif
                                    @endforeach                   
                                </select>
                            </div>
                        @endif

                        

                        <input type="hidden" name="price" value="{{ convertedPrice($data->amount,$currency) }}">
                        <input type="hidden" name="days" value="{{ $data->days }}">
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="bank_plan_id" value="{{ $data->id }}">
                        <input type="hidden" name="currency_sign" value="{{ $currency->sign }}">
                        <input type="hidden" id="currencyCode" name="currency_code" value="{{ $currency->name }}">
                        <input type="hidden" name="currency_id" value="{{ $currency->id }}">

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

@push('js')
<script src="https://js.paystack.co/v1/inline.js"></script>

<script type="text/javascript">
'use strict';

$(document).on('change','#subscriptionMethod',function(){
	var val = $(this).val();

	if(val == 'stripe')
	{
		$('#subscription-form').prop('action','{{ route('subscription.stripe.submit') }}');

		$('.card-elements').prop('required',true);
        $('#manual_transaction_id').prop('required',false);
        $('.manual-payment').addClass('d-none');
	}

    if(val == 'paypal') {
        $('#subscription-form').prop('action','{{ route('subscription.paypal.submit') }}');
        $('#card-view').addClass('d-none');
        $('.card-elements').prop('required',false);
        $('#manual_transaction_id').prop('required',false);
        $('.manual-payment').addClass('d-none');
    }

    if(val == 'paytm') {
        $('#subscription-form').prop('action','{{ route('subscription.paytm.submit') }}');
        $('#card-view').addClass('d-none');
        $('.card-elements').prop('required',false);
        $('#manual_transaction_id').prop('required',false);

        $('.manual-payment').addClass('d-none');
    }

    if(val == 'instamojo') {
        $('#subscription-form').prop('action','{{ route('subscription.instamojo.submit') }}');
        $('#card-view').addClass('d-none');
        $('.card-elements').prop('required',false);
        $('#manual_transaction_id').prop('required',false);
        $('.manual-payment').addClass('d-none');
    }

    if(val == 'razorpay') {
        $('#subscription-form').prop('action','{{ route('subscription.razorpay.submit') }}');
        $('#card-view').addClass('d-none');
        $('.card-elements').prop('required',false);
        $('#manual_transaction_id').prop('required',false);
        $('.manual-payment').addClass('d-none');
    }

    if(val == 'mollie') {
        $('#subscription-form').prop('action','{{ route('subscription.molly.submit') }}');
        $('#card-view').addClass('d-none');
        $('.card-elements').prop('required',false);
        $('#manual_transaction_id').prop('required',false);
        $('.manual-payment').addClass('d-none');
    }

    if(val == 'flutterwave') {
        $('#subscription-form').prop('action','{{ route('subscription.flutter.submit') }}');
        $('#card-view').addClass('d-none');
        $('.card-elements').prop('required',false);
        $('#manual_transaction_id').prop('required',false);
        $('.manual-payment').addClass('d-none');
    }

    if(val == 'authorize.net')
    {
        $('#subscription-form').prop('action','{{ route('subscription.authorize.submit') }}');
        $('#card-view').removeClass('d-none');
        $('.card-elements').prop('required',true);
        $('#manual_transaction_id').prop('required',false);
        $('.manual-payment').addClass('d-none');
    }

});

$(document).on('submit','.step1-form',function(){
    var val = $('#sub').val();
    var total = $('#amount').val();
    var paystackInfo = $('#paystackInfo').val();
    var curr = $('#currencyCode').val();
    total = Math.round(total);
        if(val == 0)
        {
        var handler = PaystackPop.setup({
          key: paystackInfo,
          email: $('input[name=email]').val(),
          amount: total * 100,
          currency: curr,
          ref: ''+Math.floor((Math.random() * 1000000000) + 1),
          callback: function(response){
            $('#ref_id').val(response.reference);
            $('#sub').val('1');
            $('#final-btn').click();
          },
          onClose: function(){
            window.location.reload();
          }
        });
        handler.openIframe();
            return false;                    
        }
        else {
          $('#preloader').show();
            return true;   
        }
});

</script>

  <script src="//voguepay.com/js/voguepay.js"></script>

  <script type="text/javascript" src="{{ asset('assets/front/js/payvalid.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/front/js/paymin.js') }}"></script>
  <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
  <script type="text/javascript" src="{{ asset('assets/front/js/payform.js') }}"></script>


  <script type="text/javascript">
  'use strict';
  
    var cnstatus = false;
    var dateStatus = false;
    var cvcStatus = false;

    function validateCard(cn) {
      cnstatus = Stripe.card.validateCardNumber(cn);
      if (!cnstatus) {
        $("#errCard").html('Card number not valid<br>');
      } else {
        $("#errCard").html('');
      }
      btnStatusChange();


    }

    function validateCVC(cvc) {
      cvcStatus = Stripe.card.validateCVC(cvc);
      if (!cvcStatus) {
        $("#errCVC").html('CVC number not valid');
      } else {
        $("#errCVC").html('');
      }
      btnStatusChange();
    }

  </script>
@endpush

