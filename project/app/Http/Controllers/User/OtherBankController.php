<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use App\Models\BankPlan;
use App\Models\Beneficiary;
use App\Models\Generalsetting;
use App\Models\OtherBank;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OtherBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['beneficiaries'] = Beneficiary::where('user_id',auth()->user()->id)->orderBy('id','desc')->paginate(10);
        return view('user.otherbank.index',$data);
    }

    public function othersend($id){
        $data['data'] = Beneficiary::findOrFail($id);
        return view('user.otherbank.send',$data);
    }

    public function store(Request $request, WalletService $wallet){
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);

        $user = auth()->user();
        $account = $wallet->activeAccount($user);
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
        

        $gs = Generalsetting::first();
        $otherBank = OtherBank::whereId($request->other_bank_id)->first();
        $dailyTransactions = BalanceTransfer::whereType('other')->where('account_id',$account->id)->whereDate('created_at', now())->get();
        $monthlyTransactions = BalanceTransfer::whereType('other')->where('account_id',$account->id)->whereMonth('created_at', now()->month())->get();

        if ($otherBank ) {
            $cost = $otherBank->fixed_charge + ($request->amount/100) * $otherBank->percent_charge;
            $finalAmount = $request->amount + $cost;

            if($otherBank->min_limit > $request->amount){
                return redirect()->back()->with('unsuccess','Request Amount should be greater than this');
            }
    
            if($otherBank->max_limit < $request->amount){
                return redirect()->back()->with('unsuccess','Request Amount should be less than this');
            }

            if($account->balance<0 && $finalAmount > $account->balance){
                return redirect()->back()->with('unsuccess','Insufficient Balance!');
            }

            if($otherBank->daily_maximum_limit <= $finalAmount){
                return redirect()->back()->with('unsuccess','Your daily limitation of transaction is over.');
            }

            if($otherBank->daily_maximum_limit <= $dailyTransactions->sum('final_amount')){
                return redirect()->back()->with('unsuccess','Your daily limitation of transaction is over.');
            }

            if($otherBank->daily_total_transaction <= count($dailyTransactions)){
                return redirect()->back()->with('unsuccess','Your daily number of transaction is over.');
            }

            if($otherBank->monthly_maximum_limit < $monthlyTransactions->sum('final_amount')){
                return redirect()->back()->with('unsuccess','Your monthly limitation of transaction is over.');
            }
 
            if($otherBank->monthly_total_transaction <= count($monthlyTransactions)){
                return redirect()->back()->with('unsuccess','Your monthly number of transaction is over!');
            }

            if($request->amount > $account->balance){
                return redirect()->back()->with('unsuccess','Insufficient Account Balance.');
            }

            $txnid = Str::random(4).time();

            $data = new BalanceTransfer();
            $data->user_id = auth()->user()->id;
            $data->account_id = $account->id;
            $data->transaction_no = $txnid;
            $data->other_bank_id = $request->other_bank_id;
            $data->beneficiary_id = $request->beneficiary_id;
            $data->type = 'other';
            $data->cost = $cost;
            $data->amount = $request->amount;
            $data->final_amount = $finalAmount;
            $data->status = 0;
            $data->save();

            $wallet->log($user, $account, $finalAmount, "Send Money", "minus", $txnid);
    
            $wallet->debit($account,$finalAmount);
            
            return redirect()->back()->with('success','Money Send successfully.');

        }

    }
}
