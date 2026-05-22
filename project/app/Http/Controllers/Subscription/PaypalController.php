<?php

namespace App\Http\Controllers\Subscription;

use App\Repositories\SubscriptionRepository;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\PaymentGateway;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Omnipay\Omnipay;

class PaypalController extends Controller
{
    private $_api_context;
    public $subscriptionRepositorty;
    public $support_currencies;
    public $gateway;

    public function __construct(SubscriptionRepository $subscriptionRepositorty)
    {
        $data = PaymentGateway::whereKeyword('paypal')->first();
        $paydata = $data->convertAutoData();
        $this->support_currencies =['USD','EUR'];
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId($paydata['client_id']);
        $this->gateway->setSecret($paydata['client_secret']);
        $this->gateway->setTestMode(true);
        $this->subscriptionRepositorty = $subscriptionRepositorty;
    }

    public function store(Request $request){

       
        if(!in_array($request->currency_code,$this->support_currencies)){
            return redirect()->back()->with('warning','Please Select USD Or EUR Currency For Paypal.');
        }
        $settings = Generalsetting::findOrFail(1);
        $item_amount = $request->price;
        $return_url = route('front.index');
        $cancel_url = route('subscription.paypal.cancle');
        $notify_url = route('subscription.paypal.notify');

        try {
            $response = $this->gateway->purchase(array(
                'amount' => $item_amount,
                'currency' => $request->currency_code,
                'returnUrl' => $notify_url,
                'cancelUrl' => $cancel_url,
            ))->send();

            if ($response->isRedirect()) {

                Session::put('input_data', $request->all());

                $item_name = $settings->title." Subscription";
                $item_number = Str::random(4).time();
                $addionalData = ['subscription_number'=>$item_number];
               
                Session::put('paypal_data',$request->all());
                Session::put('order_number',$item_number);
                $this->subscriptionRepositorty->order($request,'pending',$addionalData);

                if ($response->redirect()) {
                    /** redirect to paypal **/
                    return redirect($response->redirect());

                }
            } else {
                return redirect()->back()->with('unsuccess', $response->getMessage());

            }
        } catch (\Throwable$th) {

            return redirect()->back()->with('unsuccess', $response->getMessage());
        }



    }

    public function notify(Request $request)
    {

        $responseData = $request->all();

        $payment_id = Session::get('paypal_payment_id');
        if (empty($responseData['PayerID']) || empty($responseData['token']))  {
            return redirect()->back()->with('error', 'Payment Failed'); 
        } 

        $transaction = $this->gateway->completePurchase(array(
            'payer_id' => $responseData['PayerID'],
            'transactionReference' => $responseData['paymentId'],
        ));

        $response = $transaction->send();

        $order_number = Session::get('order_number');
        $request = Session::get('input_data');

        if ($response->isSuccessful()) {

            $subscription = UserSubscription::where('subscription_number',$order_number)->where('status','pending')->first();
            $subscription->status = "completed";
            $subscription->txnid = $response->getData()['transactions'][0]['related_resources'][0]['sale']['id'];
            $subscription->update();

            $this->subscriptionRepositorty->callAfterOrder($request,$subscription);

            Session::forget('paypal_data');
         
            Session::forget('order_number');


            if ($subscription) {
                return redirect()->route('user.dashboard')->with('message','Bank Plan Updated');
            } else {
                return back()->with('error', __('Something is wrong.'));
            }
        } else {
            return redirect()->back()->with('error', __('Payment failed'));
        }

    }
}
