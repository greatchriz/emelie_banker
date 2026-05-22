<?php

namespace App\Http\Controllers\Api\Deposit;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Generalsetting;
use App\Models\PaymentGateway;
use App\Models\Transaction as AppTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Omnipay\Omnipay;

class PaypalController extends Controller
{
    private $_api_context;
    public $support_currencies;
    public $gateway;

    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('paypal')->first();
        $paydata = $data->convertAutoData();
        $this->support_currencies = ['USD', 'EUR'];

        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId($paydata['client_id']);
        $this->gateway->setSecret($paydata['client_secret']);
        $this->gateway->setTestMode(true);
    }

    public function store(Request $request)
    {

        $currency = Currency::whereId($request->currency_id)->first();

        if (!in_array($currency->name, $this->support_currencies)) {
            return redirect()->back()->with('warning', 'Please Select USD Or EUR Currency For Paypal.');
        }
        $item_number = Str::random(12);
        $settings = Generalsetting::findOrFail(1);
        $deposit = Deposit::findorFail($request->deposit_id);
        $user = User::findorFail($deposit->user_id);
        $cancel_url = route('api.deposit.paypal.cancel');
        $notify_url = route('api.deposit.paypal.notify') . "?order_number=" . $item_number;

        $item_amount = $request->amount;

        $currency = Currency::whereId($request->currency_id)->first();
        $amountToAdd = $request->amount / $currency->value;

        $deposit['user_id'] = $user->id;
        $deposit['currency_id'] = $request->currency_id;
        $deposit['amount'] = $amountToAdd;
        $deposit['method'] = $request->method;
        $deposit['deposit_number'] = $item_number;
        $deposit['status'] = "pending";

        $deposit->save();

        try {
            $response = $this->gateway->purchase(array(
                'amount' => $item_amount,
                'currency' => $request->currency_code,
                'returnUrl' => $notify_url,
                'cancelUrl' => $cancel_url,
            ))->send();

            if ($response->isRedirect()) {

                $item_number = Str::random(4) . time();
                if ($response->redirect()) {
                    /** redirect to paypal **/
                    return redirect($response->redirect());

                }
            } else {
                return redirect()->back()->with('unsuccess', $response->getMessage());

            }
        } catch (\Throwable $th) {

            return redirect()->back()->with('unsuccess', $response->getMessage());
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

        $deposit = Deposit::where('deposit_number', $request->order_number)->where('status', 'pending')->first();

        $user = User::findorFail($deposit->user_id);

        $response = $transaction->send();

        if ($response->isSuccessful()) {

            $deposit->txnid = $response->getData()['transactions'][0]['related_resources'][0]['sale']['id'];

            $deposit->update();

            $gs = Generalsetting::findOrFail(1);

            if ($gs->is_smtp == 1) {
                $data = [
                    'to' => $user->email,
                    'type' => "Deposit",
                    'cname' => $user->name,
                    'oamount' => $deposit->amount,
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
            $deposit->status = "complete";
            $deposit->save();

            $user->balance += $deposit->amount;
            $user->save();

            $trans = new AppTransaction();
            $trans->email = $user->email;
            $trans->amount = $deposit->amount;
            $trans->type = "Deposit";
            $trans->profit = "plus";
            $trans->txnid = $deposit->deposit_number;
            $trans->user_id = $user->id;
            $trans->save();

            $data['get'] = json_encode(['status' => true, 'data' => "Payment Success", 'error' => []]);
            return view('frontend.api_payment', $data);

        } else {
            $data['get'] = json_encode(['status' => false, 'data' => "Payment Failed", 'error' => []]);
            return view('frontend.api_payment', $data);
        }
    }
}
