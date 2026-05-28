<?php

namespace App\Http\Controllers\User;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\BankPlan;
use App\Models\Generalsetting;
use App\Models\MoneyRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MoneyRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(WalletService $wallet){
        $account = $wallet->activeAccount(auth()->user());
        $data['requests'] = MoneyRequest::orderby('id','desc')
            ->whereUserId(auth()->id())
            ->when($account, fn ($query) => $query->where('account_id', $account->id))
            ->when(!$account, fn ($query) => $query->whereRaw('1 = 0'))
            ->paginate(10);
        return view('user.requestmoney.index',$data);
    }

    public function receive(WalletService $wallet){
        $account = $wallet->activeAccount(auth()->user());
        $data['requests'] = MoneyRequest::orderby('id','desc')
            ->whereReceiverId(auth()->id())
            ->when($account, fn ($query) => $query->where('receiver_account_id', $account->id))
            ->when(!$account, fn ($query) => $query->whereRaw('1 = 0'))
            ->paginate(10);
        return view('user.requestmoney.receive',$data);
    }

    public function create(){
        return view('user.requestmoney.create');
    }

    public function store(Request $request, WalletService $wallet){
        $request->validate([
            'account_name' => 'required',
            'amount' => 'required|gt:0',
        ]);

        $requester = auth()->user();
        $requesterAccount = $wallet->activeAccount($requester);

        if(!$requesterAccount){
            return redirect()->back()->with('unsuccess','No active account available.');
        }

        if(!$wallet->hasValidPlan($requesterAccount)){
            return redirect()->back()->with('unsuccess','Your selected account has no active plan.');
        }

        $bank_plan = $wallet->bankPlan($requesterAccount);
        $dailyRequests = MoneyRequest::whereUserId(auth()->id())->where('account_id', $requesterAccount->id)->whereDate('created_at', '=', date('Y-m-d'))->whereStatus(1)->sum('amount');
        $monthlyRequests = MoneyRequest::whereUserId(auth()->id())->where('account_id', $requesterAccount->id)->whereMonth('created_at', '=', date('m'))->whereStatus(1)->sum('amount');

        $gs = Generalsetting::first();

        if($request->account_number == $requesterAccount->account_number){
            return redirect()->back()->with('unsuccess','You can not send money yourself!');
        }

        $receiverAccount = UserAccount::where('account_number', $request->account_number)->active()->first();
        if($receiverAccount === null){
            return redirect()->back()->with('unsuccess','No register user with this email!');
        }

        if($dailyRequests > $bank_plan->daily_receive){
            return redirect()->back()->with('unsuccess','Daily request limit over.');
        }

        if($monthlyRequests > $bank_plan->monthly_receive){
            return redirect()->back()->with('unsuccess','Monthly request limit over.');
        }

        $cost = $gs->fixed_request_charge + ($request->amount/100) * $gs->percentage_request_charge;
        $finalAmount = $request->amount + $cost;


        $receiver = $receiverAccount->user;

        $txnid = Str::random(4).time();

        $data = new MoneyRequest();
        $data->user_id = auth()->user()->id;
        $data->receiver_id = $receiver->id;
        $data->account_id = $requesterAccount->id;
        $data->receiver_account_id = $receiverAccount->id;
        $data->receiver_name = $receiver->name;
        $data->transaction_no = $txnid;
        $data->cost = $cost;
        $data->amount = $request->amount;
        $data->status = 0;
        $data->details = $request->details;
        $data->save();

        $trans = new Transaction();
        $trans->email = $receiver->email;
        $trans->amount = $finalAmount;
        $trans->type = "Request Money";
        $trans->profit = "plus";
        $trans->txnid = $txnid;
        $trans->user_id = $receiver->id;
        $trans->account_id = $receiverAccount->id;
        $trans->save();

        return redirect()->back()->with('success','Request Money Send Successfully.');
        
    }

    public function send($id, WalletService $wallet){
        $data = MoneyRequest::findOrFail($id);
        $gs = Generalsetting::first();
    
        $sender = User::whereId($data->receiver_id)->first();
        $receiver = User::whereId($data->user_id)->first();
        $senderAccount = $data->receiver_account_id
            ? UserAccount::where('id', $data->receiver_account_id)->where('user_id', auth()->id())->first()
            : $wallet->activeAccount(auth()->user());
        $receiverAccount = $data->account_id
            ? UserAccount::where('id', $data->account_id)->where('user_id', $receiver->id)->first()
            : $wallet->defaultAccount($receiver);

        if(!$senderAccount || !$senderAccount->isActive()){
            return back()->with('warning','The requested source account is not active.');
        }

        if(!$receiverAccount || !$receiverAccount->isActive()){
            return back()->with('warning','The receiver account is not active.');
        }

        if($data->amount > $senderAccount->balance){
            return back()->with('warning','You don,t have sufficient balance!');
        }

        $finalAmount = $data->amount - $data->cost;

        $wallet->debit($senderAccount, $data->amount);
        $wallet->credit($receiverAccount, $finalAmount);

        $data->update(['status'=>1]);

        $trans = new Transaction();
        $trans->email = auth()->user()->email;
        $trans->amount = $data->amount;
        $trans->type = "Request Money";
        $trans->profit = "minus";
        $trans->txnid = $data->transaction_no;
        $trans->user_id = auth()->id();
        $trans->account_id = $senderAccount->id;
        $trans->save();

        $trans = new Transaction();
        $trans->email = $receiver->email;
        $trans->amount = $data->amount;
        $trans->type = "Request Money";
        $trans->profit = "plus";
        $trans->txnid = $data->transaction_no;
        $trans->user_id = $receiver->id;
        $trans->account_id = $receiverAccount->id;
        $trans->save();

        if($gs->is_smtp == 1)
        {
            $data = [
                'to' => $receiver->email,
                'type' => "request money",
                'cname' => $receiver->name,
                'oamount' => $finalAmount,
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

        return back()->with('message','Successfully Money Send.');
    }

    public function details($id){
        $data = MoneyRequest::findOrFail($id);
        $from = User::whereId($data->user_id)->first();
        $to = User::whereId($data->receiver_id)->first();
        return view('user.requestmoney.details',compact('data','from','to'));
    }
}
