<?php

namespace App\Http\Controllers\User;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use App\Models\BankPlan;
use App\Models\Generalsetting;
use App\Models\SaveAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Str;

class SendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function create(){
        $data['saveAccounts'] = SaveAccount::whereUserId(auth()->id())->orderBy('id','desc')->get();
        $data['savedUser'] = NULL;

        return view('user.sendmoney.create',$data);
    }

    public function savedUser($no){
        $data['savedUser'] = User::whereAccountNumber($no)->first();
        $data['saveAccounts'] = SaveAccount::whereUserId(auth()->id())->orderBy('id','desc')->get();

        return view('user.sendmoney.create',$data);
    }

    public function success(){
        if(session('saveData') && session('sendstatus') == 1){
            $data['data'] = session()->get('saveData');

            session(['sendstatus'=>0]);
            return view('user.sendmoney.success',$data);
        }else{
            session(['sendstatus'=>0]);
            $data['savedUser'] =  NULL;
            $data['saveAccounts'] = SaveAccount::whereUserId(auth()->id())->orderBy('id','desc')->get();

            return view('user.sendmoney.create',$data);
        }
    }

    public function store(Request $request){
        $request->validate([
            'account_number' => 'required',
            'account_name' => 'required',
            'amount' => 'required|numeric|min:0'
        ]);

        $user = auth()->user();

        if($user->bank_plan_id === null){
            return redirect()->back()->with('unsuccess','You have to buy a plan to withdraw.');
        }

        if(now()->gt($user->plan_end_date)){
            return redirect()->back()->with('unsuccess','Plan Date Expired.');
        }

        $bank_plan = BankPlan::whereId($user->bank_plan_id)->first();
        $dailySend = BalanceTransfer::whereUserId(auth()->id())->whereDate('created_at', '=', date('Y-m-d'))->whereStatus(1)->sum('amount');
        $monthlySend = BalanceTransfer::whereUserId(auth()->id())->whereMonth('created_at', '=', date('m'))->whereStatus(1)->sum('amount');

        if($dailySend > $bank_plan->daily_send){
            return redirect()->back()->with('unsuccess','Daily send limit over.');
        }

        if($monthlySend > $bank_plan->monthly_send){
            return redirect()->back()->with('unsuccess','Monthly send limit over.');
        }

        if($request->amount > $user->balance){
            return redirect()->back()->with('unsuccess','Insufficient Account Balance.');
        }


        if($request->account_number == $user->account_number){
            return redirect()->back()->with('unsuccess','You can not send money yourself!!');
        }

        if($request->amount < 0){
            return redirect()->back()->with('unsuccess','Request Amount should be greater than this!');
        }

        if($request->amount > $user->balance){
            return redirect()->back()->with('unsuccess','Insufficient Balance.');
        }


        if($receiver = User::where('account_number',$request->account_number)->first()){
            session([
                'pending_transaction' => [
                    'type' => 'send_money',
                    'title' => 'Send Money',
                    'amount' => $request->amount,
                    'data' => $request->only('account_number', 'account_name', 'amount'),
                ],
            ]);

            if (!$user->transaction_pin) {
                return redirect()->route('user.transaction.pin.setup');
            }

            return redirect()->route('user.transaction.pin.verify');
        }else{
            return redirect()->back()->with('unsuccess','Sender not found!');
        }
    }

    public function completePendingTransfer(array $input)
    {
        $validator = Validator::make($input, [
            'account_number' => 'required',
            'account_name' => 'required',
            'amount' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->route('send.money.create')->with('unsuccess','Invalid transfer details.');
        }

        $user = auth()->user()->fresh();

        if($user->bank_plan_id === null){
            return redirect()->route('send.money.create')->with('unsuccess','You have to buy a plan to withdraw.');
        }

        if(now()->gt($user->plan_end_date)){
            return redirect()->route('send.money.create')->with('unsuccess','Plan Date Expired.');
        }

        $bank_plan = BankPlan::whereId($user->bank_plan_id)->first();
        $dailySend = BalanceTransfer::whereUserId(auth()->id())->whereDate('created_at', '=', date('Y-m-d'))->whereStatus(1)->sum('amount');
        $monthlySend = BalanceTransfer::whereUserId(auth()->id())->whereMonth('created_at', '=', date('m'))->whereStatus(1)->sum('amount');

        if($dailySend > $bank_plan->daily_send){
            return redirect()->route('send.money.create')->with('unsuccess','Daily send limit over.');
        }

        if($monthlySend > $bank_plan->monthly_send){
            return redirect()->route('send.money.create')->with('unsuccess','Monthly send limit over.');
        }

        if($input['account_number'] == $user->account_number){
            return redirect()->route('send.money.create')->with('unsuccess','You can not send money yourself!!');
        }

        if($input['amount'] < 0){
            return redirect()->route('send.money.create')->with('unsuccess','Request Amount should be greater than this!');
        }

        if($input['amount'] > $user->balance){
            return redirect()->route('send.money.create')->with('unsuccess','Insufficient Balance.');
        }

        if(! $receiver = User::where('account_number',$input['account_number'])->first()){
            return redirect()->route('send.money.create')->with('unsuccess','Sender not found!');
        }

        $gs = Generalsetting::first();
        $txnid = Str::random(4).time();
        $data = new BalanceTransfer();
        $data->user_id = auth()->user()->id;
        $data->receiver_id = $receiver->id;
        $data->transaction_no = $txnid;
        $data->type = 'own';
        $data->cost = 0;
        $data->amount = $input['amount'];
        $data->status = 1;
        $data->save();

        $receiver->increment('balance',$input['amount']);
        $user->decrement('balance',$input['amount']);

        session(['sendstatus'=>1, 'saveData'=>$data]);

        $trans = new Transaction();
        $trans->email = $user->email;
        $trans->amount = $input['amount'];
        $trans->type = "Send Money";
        $trans->profit = "minus";
        $trans->txnid = $txnid;
        $trans->user_id = $user->id;
        $trans->save();

        if($gs->is_smtp == 1)
        {
            $data = [
                'to' => $receiver->email,
                'type' => "send money",
                'cname' => $receiver->name,
                'oamount' => $input['amount'],
                'aname' => "",
                'aemail' => "",
                'wtitle' => "",
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoMail($data);
        }
        else
        {
            $to = $receiver->email;
            $subject = " Money send successfully.";
            $msg = "Hello ".$receiver->name."!\nMoney send successfully.\nThank you.";
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            mail($to,$subject,$msg,$headers);
        }

        return redirect()->route('user.send.money.success');
    }

    public function saveAccount(Request $request){
        $savedUser = SaveAccount::whereUserId(auth()->id())->where('receiver_id',$request->receiver_id)->first();

        if($savedUser){
            return redirect()->route('send.money.create')->with('success','Already in Beneficiary.');
        }
        $data = new SaveAccount();

        $data->user_id = $request->user_id;
        $data->receiver_id = $request->receiver_id;
        $data->save();

        return redirect()->route('send.money.create')->with('success','Money Send Successfully');
    }

    public function cancle(){
        return redirect()->route('send.money.create')->with('success','Money Send Successfully');
    }


}
