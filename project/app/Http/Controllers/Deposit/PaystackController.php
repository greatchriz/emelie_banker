<?php

namespace App\Http\Controllers\Deposit;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Generalsetting;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaystackController extends Controller
{
    public function __construct()
    {

    }

    public function store(Request $request, WalletService $wallet){
        if($request->currency_code != "NGN")
        {
            return redirect()->back()->with('unsuccess','Please Select NGN Currency For Paystack.');
        }

        $account = $wallet->activeAccount(auth()->user());
        if ($message = $wallet->ensureActive($account)) {
            return redirect()->back()->with('unsuccess', $message);
        }

        $deposit = new Deposit();
        $deposit['user_id'] = auth()->user()->id;
        $deposit['account_id'] = $account->id;
        $deposit['deposit_number'] = Str::random(12);
        $deposit['amount'] = $request->amount;
        $deposit['method'] = $request->method;
        $deposit['status'] = "complete";

        $deposit->save();

        
        $gs =  Generalsetting::findOrFail(1);
        $currency = Currency::where('id',$request->currency_id)->first();
        $amountToAdd = $request->amount/$currency->value;

        $user = auth()->user();
        $wallet->credit($account, $amountToAdd);
        $wallet->log($user, $account, $amountToAdd, "Deposit", "plus", $deposit->deposit_number ?? Str::random(12));

        if($gs->is_smtp == 1)
        {
            $data = [
                'to' => $user->email,
                'type' => "Deposti",
                'cname' => $user->name,
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
        
        return redirect()->route('user.deposit.create')->with('success','Deposit amount ('.$request->amount.') successfully!');
    }
}
