<?php

namespace App\Http\Controllers\Api\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\PaymentGateway;
use App\Models\UserSubscription;
use App\Repositories\SubscriptionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Currency;
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
        $this->support_currencies = ['USD', 'EUR'];
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId($paydata['client_id']);
        $this->gateway->setSecret($paydata['client_secret']);
        $this->gateway->setTestMode(true);
        $this->subscriptionRepositorty = $subscriptionRepositorty;
    }

    public function store(Request $request)
    {
        $currency = Currency::findOrFail($request->currency_id);
        if (!in_array($currency->name, $this->support_currencies)) {
            return redirect()->back()->with('warning', 'Please Select USD Or EUR Currency For Paypal.');
        }


        $item_number = Str::random(4) . time();

        $settings = Generalsetting::findOrFail(1);
        $item_amount = $request->price;
        $cancel_url = route('api.subscription.paypal.cancel',["id"=>$request->bank_plan_id,"user_id"=>$request->user_id])."?currency_id=".$request->currency_id."&user_id=".$request->user_id."&order_number=".$item_number;
        $notify_url = route('api.subscription.paypal.notify',["id"=>$request->bank_plan_id,"user_id"=>$request->user_id])."?currency_id=".$request->currency_id."&user_id=".$request->user_id."&order_number=".$item_number;

        try {
            $response = $this->gateway->purchase(array(
                'amount' => $item_amount,
                'currency' => $currency->name,
                'returnUrl' => $notify_url,
                'cancelUrl' => $cancel_url,
            ))->send();

      

            if ($response->isRedirect()) {

                Session::put('input_data', $request->all());

                $item_name = $settings->title . " Subscription";
                
                $addionalData = ['subscription_number' => $item_number];

                Session::put('paypal_data', $request->all());
                Session::put('order_number', $item_number);
                $this->subscriptionRepositorty->order($request, 'pending', $addionalData);

                if ($response->redirect()) {
                    /** redirect to paypal **/

                   return redirect($response->redirect());

                }

                
            } else {
                $data['get'] = json_encode(['status' => false, 'data' => "Unknown error occurred", 'error' => []]);
                return view('frontend.api_payment', $data);
            }
        } catch (\Throwable $th) {
           
            $data['get'] = json_encode(['status' => false, 'data' => "Unknown error occurred", 'error' => []]);
            return view('frontend.api_payment', $data);
        }

    }

    public function notify(Request $request)
    {
        
        $responseData = $request->all();

        if (empty($responseData['PayerID']) || empty($responseData['token'])) {
            return redirect()->back()->with('error', 'Payment Failed');
        }

        $transaction = $this->gateway->completePurchase(array(
            'payer_id' => $responseData['PayerID'],
            'transactionReference' => $responseData['paymentId'],
        ));

        $response = $transaction->send();

        $order_number = $request->order_number;
       
        
        if ($response->isSuccessful()) {

            $subscription = UserSubscription::where('subscription_number', $order_number)->where('status', 'pending')->first();

            $subscription->status = "completed";
            $subscription->txnid = $response->getData()['transactions'][0]['related_resources'][0]['sale']['id'];
            $subscription->update();

            $this->subscriptionRepositorty->callAfterOrder($subscription);

            if ($subscription) {
                $data['get'] = json_encode(['status' => true, 'data' => "Payment Success", 'error' => []]);
                return view('frontend.api_payment', $data);
            } else {
                $data['get'] = json_encode(['status' => false, 'data' => "Something is wrong.", 'error' => []]);
                return view('frontend.api_payment', $data);
            }
        } else {
            $data['get'] = json_encode(['status' => false, 'data' => "Payment failed", 'error' => []]);
            return view('frontend.api_payment', $data);
        }

    }
}
