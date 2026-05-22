<?php

namespace App\Http\Controllers\Api\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\PaymentGateway;
use App\Repositories\SubscriptionRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Currency;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StripeController extends Controller
{
    public $subscriptionRepositorty;

    public function __construct(SubscriptionRepository $subscriptionRepositorty)
    {
        $data = PaymentGateway::whereKeyword('Stripe')->first();
        $paydata = $data->convertAutoData();

        \Stripe\Stripe::setApiKey($paydata['secret']);
        $this->subscriptionRepositorty = $subscriptionRepositorty;
    }

    public function store(Request $request)
    {
        $gs = Generalsetting::findOrFail(1);

        $currency = Currency::where('id',$request->currency_id)->first();
        $item_amount = $request->price;
        $support = ['USD'];
        if (!in_array($currency->name, $support)) {
            return redirect()->back()->with('warning', 'Please Select USD Or EUR Currency For Paypal.');
        }

        Session::put('request', $request->all());

        $session = \Stripe\Checkout\Session::create([
            "line_items" => [
                [
                    "quantity" => 1,
                    "price_data" => [
                        "currency" => $currency->name,
                        "unit_amount" => $item_amount * 100,
                        "product_data" => [
                            "name" => $gs->title . 'Plan Subscription',
                        ],
                    ],
                ],
            ],
            'mode' => 'payment',
            "locale" => "auto",
            'success_url' => route('api.subscription.stripe.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}"."&currency_id=".$request->currency_id."&user_id=".$request->user_id."&bank_plan_id=".$request->bank_plan_id,
            'cancel_url' => route('api.subscription.paypal.cancel', ["id" => $request->bank_plan_id, "user_id" => $request->user_id]) . "?currency_id=" . $request->currency_id . "&user_id=" . $request->user_id,
        ]);
        return redirect($session->url);

    }

    public function notify (Request $request)
    {
        $sessionId = $request->get('session_id');
            
        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

        
            if (!$session) {
                throw new NotFoundHttpException();
            }

            $item_number = Str::random(4) . time();
            $request['method'] = 'stripe';
            
            if ($session->payment_status == 'paid' && $session->status == 'complete') {
                $addionalData = ['subscription_number' => $item_number, 'txnid' => $session->payment_intent];
                $this->subscriptionRepositorty->OrderFromSession($request, 'completed', $addionalData);
              
                $data['get'] = json_encode(['status' => true, 'data' => "Payment Success", 'error' => []]);
                return view('frontend.api_payment', $data);
            }

        } catch (Exception $e) {
            $data['get'] = json_encode(['status' => false, 'data' => $e->getMessage(), 'error' => []]);
            return view('frontend.api_payment', $data);
        }

    }
}
