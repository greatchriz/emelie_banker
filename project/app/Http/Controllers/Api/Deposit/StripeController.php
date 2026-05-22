<?php

namespace App\Http\Controllers\Api\Deposit;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Generalsetting;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StripeController extends Controller
{
    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('Stripe')->first();
        $paydata = $data->convertAutoData();

        \Stripe\Stripe::setApiKey($paydata['secret']);
    }

    public function store(Request $request)
    {
        $item_amount = $request->amount;
        $gs = Generalsetting::findOrFail(1);
        $deposit = Deposit::findOrFail($request->deposit_id);
        $deposit->currency_id = $request->currency_id;
        
        $support = ['USD'];
        if (!in_array($request->currency_code, $support)) {
            return redirect()->back()->with('warning', 'Please Select USD Or EUR Currency For Paypal.');
        }

        $session = \Stripe\Checkout\Session::create([
            "line_items" => [
                [
                    "quantity" => 1,
                    "price_data" => [
                        "currency" => $request->currency_code,
                        "unit_amount" => $item_amount * 100,
                        "product_data" => [
                            "name" => $gs->title . 'Plan Subscription',
                        ],
                    ],
                ],
            ],
            'mode' => 'payment',
            "locale" => "auto",
            'success_url' => route('api.deposit.stripe.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}&deposit_id=" . $request->deposit_id,
            'cancel_url' => route('api.deposit.paypal.cancel', [], true),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $gs = Generalsetting::findOrFail(1);
        $deposit = new Deposit();
        $sessionId = $request->get('session_id');

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (!$session) {
                throw new NotFoundHttpException();
            }

            if ($session->payment_status == 'paid' && $session->status == 'complete') {
                $deposit = Deposit::findOrFail($request['deposit_id']);

                $currency = Currency::where('id', $deposit->currency_id)->first();
                $amountToAdd = $deposit->amount / $currency->value;

                $deposit->status = "complete";
                $deposit->method = "stripe";

                $deposit->save();

                $gs = Generalsetting::findOrFail(1);

                $user = User::findOrFail($deposit->user_id);
                $user->balance += $amountToAdd;
                $user->save();

                $trans = new Transaction();
                $trans->email = $user->email;
                $trans->amount = $amountToAdd;
                $trans->type = "Deposit";
                $trans->profit = "plus";
                $trans->txnid = $deposit->deposit_number;
                $trans->user_id = $user->id;
                $trans->save();

                if ($gs->is_smtp == 1) {
                    $data = [
                        'to' => $user->email,
                        'type' => "Deposit",
                        'cname' => $user->name,
                        'oamount' => $request['amount'],
                        'aname' => "",
                        'aemail' => "",
                        'wtitle' => "",
                    ];

                    $mailer = new GeniusMailer();
                    $mailer->sendAutoMail($data);
                } else {
                    $to = $user->email;
                    $subject = " You have deposited successfully.";
                    $msg = "Hello " . $user->name . "!\nYou have invested successfully.\nThank you.";
                    $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
                    mail($to, $subject, $msg, $headers);
                }

                $data['get'] = json_encode(['status' => true, 'data' => "Deposit completed successfully", 'error' => []]);
                return view('frontend.api_payment', $data);
            } else {
                $data['get'] = json_encode(['status' => false, 'data' => "Something went wrong!", 'error' => []]);
                return view('frontend.api_payment', $data);
            }

        } catch (Exception $e) {
            $data['get'] = json_encode(['status' => false, 'data' => $e->getMessage(), 'error' => []]);
            return view('frontend.api_payment', $data);
        }

    }
}
