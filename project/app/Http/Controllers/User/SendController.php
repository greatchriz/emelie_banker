<?php

namespace App\Http\Controllers\User;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use App\Models\BankPlan;
use App\Models\Generalsetting;
use App\Models\SaveAccount;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Models\User;
use Illuminate\Support\Str;

class SendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function create(Request $request, WalletService $wallet){
        $data['saveAccounts'] = SaveAccount::whereUserId(auth()->id())->orderBy('id','desc')->get();
        $data['savedUser'] = NULL;
        $data['accounts'] = $wallet->activeAccounts(auth()->user());
        $data['selectedAccount'] = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'));

        return view('user.sendmoney.create',$data);
    }

    public function savedUser(Request $request, WalletService $wallet, $no){
        $account = UserAccount::where('account_number', $no)->with('user')->first();
        $data['savedUser'] = $account ? $account->user : User::whereAccountNumber($no)->first();
        if ($data['savedUser'] && $account) {
            $data['savedUser']->account_number = $account->account_number;
        }
        $data['saveAccounts'] = SaveAccount::whereUserId(auth()->id())->orderBy('id','desc')->get();
        $data['accounts'] = $wallet->activeAccounts(auth()->user());
        $data['selectedAccount'] = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'));

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
            $wallet = app(WalletService::class);
            $data['accounts'] = $wallet->activeAccounts(auth()->user());
            $data['selectedAccount'] = null;

            return view('user.sendmoney.create',$data);
        }
    }

    public function store(Request $request, WalletService $wallet){
        $request->validate([
            'account_id' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'amount' => 'required|numeric|min:0'
        ]);

        $user = auth()->user();
        $account = $wallet->accountFromRequest($user, $request->account_id);
        if ($message = $wallet->ensureActive($account)) {
            return redirect()->back()->with('unsuccess', $message);
        }

        if($message = $wallet->hasValidPlan($account)){
            return redirect()->back()->with('unsuccess',$message);
        }

        $bank_plan = $wallet->bankPlan($account);
        $dailySend = BalanceTransfer::where('account_id',$account->id)->whereDate('created_at', '=', date('Y-m-d'))->whereStatus(1)->sum('amount');
        $monthlySend = BalanceTransfer::where('account_id',$account->id)->whereMonth('created_at', '=', date('m'))->whereStatus(1)->sum('amount');

        if($dailySend > $bank_plan->daily_send){
            return redirect()->back()->with('unsuccess','Daily send limit over.');
        }

        if($monthlySend > $bank_plan->monthly_send){
            return redirect()->back()->with('unsuccess','Monthly send limit over.');
        }

        if($request->amount > $account->balance){
            return redirect()->back()->with('unsuccess','Insufficient Account Balance.');
        }


        if($request->account_number == $account->account_number){
            return redirect()->back()->with('unsuccess','You can not send money yourself!!');
        }

        if($request->amount < 0){
            return redirect()->back()->with('unsuccess','Request Amount should be greater than this!');
        }

        if($request->amount > $account->balance){
            return redirect()->back()->with('unsuccess','Insufficient Balance.');
        }


        if($receiverAccount = UserAccount::where('account_number',$request->account_number)->where('status','active')->first()){
            session([
                'pending_transaction' => [
                    'type' => 'send_money',
                    'title' => 'Send Money',
                    'amount' => $request->amount,
                    'data' => $request->only('account_number', 'account_name', 'amount') + ['account_id' => $account->id],
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
            'account_id' => 'required',
            'amount' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->route('send.money.create')->with('unsuccess','Invalid transfer details.');
        }

        $wallet = app(WalletService::class);
        $user = auth()->user()->fresh();
        $account = $wallet->accountFromRequest($user, $input['account_id'] ?? null);

        if ($message = $wallet->ensureActive($account)) {
            return redirect()->route('send.money.create')->with('unsuccess',$message);
        }

        if($message = $wallet->hasValidPlan($account)){
            return redirect()->route('send.money.create')->with('unsuccess',$message);
        }

        $bank_plan = $wallet->bankPlan($account);
        $dailySend = BalanceTransfer::where('account_id',$account->id)->whereDate('created_at', '=', date('Y-m-d'))->whereStatus(1)->sum('amount');
        $monthlySend = BalanceTransfer::where('account_id',$account->id)->whereMonth('created_at', '=', date('m'))->whereStatus(1)->sum('amount');

        if($dailySend > $bank_plan->daily_send){
            return redirect()->route('send.money.create')->with('unsuccess','Daily send limit over.');
        }

        if($monthlySend > $bank_plan->monthly_send){
            return redirect()->route('send.money.create')->with('unsuccess','Monthly send limit over.');
        }

        if($input['account_number'] == $account->account_number){
            return redirect()->route('send.money.create')->with('unsuccess','You can not send money yourself!!');
        }

        if($input['amount'] < 0){
            return redirect()->route('send.money.create')->with('unsuccess','Request Amount should be greater than this!');
        }

        if($input['amount'] > $account->balance){
            return redirect()->route('send.money.create')->with('unsuccess','Insufficient Balance.');
        }

        if(! $receiverAccount = UserAccount::where('account_number',$input['account_number'])->where('status','active')->first()){
            return redirect()->route('send.money.create')->with('unsuccess','Sender not found!');
        }
        $receiver = $receiverAccount->user;

        $gs = Generalsetting::first();
        $txnid = Str::random(4).time();
        $transfer = DB::transaction(function () use ($wallet, $user, $account, $receiver, $receiverAccount, $input, $txnid) {
            $data = new BalanceTransfer();
            $data->user_id = $user->id;
            $data->account_id = $account->id;
            $data->receiver_id = $receiver->id;
            $data->receiver_account_id = $receiverAccount->id;
            $data->transaction_no = $txnid;
            $data->type = 'own';
            $data->cost = 0;
            $data->amount = $input['amount'];
            $data->status = 1;
            $data->save();

            $wallet->debit($account,$input['amount']);
            $wallet->credit($receiverAccount,$input['amount']);

            $wallet->log($user, $account, $input['amount'], "Send Money", "minus", $txnid);
            $wallet->log($receiver, $receiverAccount, $input['amount'], "Receive Money", "plus", $txnid);

            return $data;
        });

        session(['sendstatus'=>1, 'saveData'=>$transfer]);

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
