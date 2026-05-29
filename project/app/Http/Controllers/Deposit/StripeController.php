<?php

namespace App\Http\Controllers\Deposit;

use App\Classes\GeniusMailer;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Generalsetting;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stripe\Error\Card;
use Carbon\Carbon;
use Input;
use Redirect;
use URL;
use Validator;
use Config;

class StripeController extends Controller
{
    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('Stripe')->first();
        $paydata = $data->convertAutoData();

        \Stripe\Stripe::setApiKey($paydata['secret']);
    }

    public function store(Request $request, WalletService $wallet){
       
       
        $item_amount = $request->amount;
        $gs = Generalsetting::findOrFail(1);
        $request->validate(['account_id' => 'required']);
        $account = $wallet->accountFromRequest(auth()->user(), $request->account_id);
        if ($message = $wallet->ensureActive($account)) {
            return redirect()->back()->with('warning', $message);
        }
        Session::put('request',$request->all() + ['account_id' => $account->id]);
        
        $support = ['USD'];
        if(!in_array($request->currency_code,$support)){
            return redirect()->back()->with('warning','Please Select USD Or EUR Currency For Paypal.');
        }
        $user = auth()->user();

        $session = \Stripe\Checkout\Session::create([
            "line_items" => [
                [
                    "quantity" => 1,
                    "price_data" => [
                        "currency" => $request->currency_code,
                        "unit_amount" =>$item_amount*100,
                        "product_data" => [
                            "name" => $gs->title . 'Plan Subscription',
                        ]
                    ]
                ]
                ],
            'mode' => 'payment',
            "locale" => "auto",
            'success_url' => route('deposit.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('subscription.paypal.cancle', [], true),
          ]);
          return redirect($session->url);
        }

        public function success(Request $request)
        {
            $gs = Generalsetting::findOrFail(1);
            $deposit = new Deposit();
            $item_name = $gs->title." Deposit";
            $item_number = Str::random(4).time();
            $sessionId = $request->get('session_id');
            $request = Session::get('request');

            try{
                $session = \Stripe\Checkout\Session::retrieve($sessionId);

        
                if (!$session) {
                    throw new NotFoundHttpException;
                }

               

                if ($session->payment_status == 'paid'  && $session->status=='complete') {
                    $currency = Currency::where('id',$request['currency_id'])->first();
                    $amountToAdd = $request['amount']/$currency->value;

                    $deposit['deposit_number'] = Str::random(12);
                    $deposit['user_id'] = auth()->id();
                    $deposit['account_id'] = $request['account_id'] ?? null;
                    $deposit['currency_id'] = $request['currency_id'];
                    $deposit['amount'] = $amountToAdd;
                    $deposit['method'] = $request['method'];
                    $deposit['txnid'] = $session->payment_intent;
                    $deposit['charge_id'] = $sessionId;
                    $deposit['status'] = "complete";
                    $deposit->save();

                    $gs =  Generalsetting::findOrFail(1);
        
                    $user = auth()->user();
                    $wallet = app(WalletService::class);
                    $account = $wallet->accountFromRequest($user, $request['account_id'] ?? null);
                    if ($message = $wallet->ensureActive($account)) {
                        return redirect()->route('user.deposit.create')->with('unsuccess', $message);
                    }
                    $wallet->credit($account, $amountToAdd);
                    $wallet->log($user, $account, $amountToAdd, "Deposit", "plus", $deposit->deposit_number);


                    if($gs->is_smtp == 1)
                    {
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
                    }
                    else
                    {
                       $to = $user->email;
                       $subject = " You have deposited successfully.";
                       $msg = "Hello ".$user->name."!\nYou have invested successfully.\nThank you.";
                       $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
                       mail($to,$subject,$msg,$headers);            
                    }

                    return redirect()->route('user.deposit.create')->with('success','Deposit amount '.formatMoneyAmount($request['amount']).' (USD) successfully!');
                }
                else{
                    return redirect()->route('user.deposit.create')->with('unsuccess','Deposit amount '.formatMoneyAmount($request['amount']).' (USD) failed!');
                }
                
            }catch (Exception $e){
                return back()->with('unsuccess', $e->getMessage());
            }
        
       
    }
}
