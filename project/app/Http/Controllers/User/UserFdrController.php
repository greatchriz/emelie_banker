<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\FdrPlan;
use App\Models\Transaction;
use App\Models\UserFdr;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserFdrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(WalletService $wallet){
        $account = $wallet->activeAccount();
        $data['fdr'] = UserFdr::whereUserId(auth()->id())->when($account, function ($query) use ($account) {
            $query->where('account_id', $account->id);
        }, function ($query) {
            $query->whereRaw('1 = 0');
        })->orderby('id','desc')->paginate(10);
        return view('user.fdr.index',$data);
    }

    public function running(WalletService $wallet){
        $account = $wallet->activeAccount();
        $data['fdr'] = UserFdr::whereStatus(1)->whereUserId(auth()->id())->when($account, function ($query) use ($account) {
            $query->where('account_id', $account->id);
        }, function ($query) {
            $query->whereRaw('1 = 0');
        })->orderby('id','desc')->paginate(10);
        return view('user.fdr.running',$data);
    }

    public function closed(WalletService $wallet){
        $account = $wallet->activeAccount();
        $data['fdr'] = UserFdr::whereStatus(2)->whereUserId(auth()->id())->when($account, function ($query) use ($account) {
            $query->where('account_id', $account->id);
        }, function ($query) {
            $query->whereRaw('1 = 0');
        })->orderby('id','desc')->paginate(10);
        return view('user.fdr.closed',$data);
    }

    public function fdrPlan(){
        $data['plans'] = FdrPlan::orderBy('id','desc')->whereStatus(1)->orderby('id','desc')->paginate(12);
        return view('user.fdr.plan',$data);
    }

    public function fdrAmount(Request $request){
        $plan = FdrPlan::whereId($request->planId)->first();
        $amount = $request->amount;

        if($amount >= $plan->min_amount && $amount <= $plan->max_amount){
            $data['data'] = $plan;
            $data['fdrAmount'] = $amount;
            $data['currency'] = Currency::whereIsDefault(1)->first();

            return view('user.fdr.apply',$data);
        }else{
            return redirect()->back()->with('warning','Request Money should be between minium and maximum amount!');
        }
    }

    public function fdrRequest(Request $request, WalletService $wallet){
        $user = auth()->user();
        $account = $wallet->activeAccount($user);
        if ($message = $wallet->ensureActive($account)) {
            return redirect()->back()->with('warning', $message);
        }
        if($account->balance >= $request->fdr_amount){

            $data = new UserFdr();
            $plan = FdrPlan::findOrFail($request->plan_id);

            $data->transaction_no = Str::random(4).time();
            $data->user_id = auth()->id();
            $data->account_id = $account->id;
            $data->fdr_plan_id = $plan->id;
            $data->amount = $request->fdr_amount;
            $data->profit_type = $plan->interval_type;
            $data->profit_amount = $request->profit_amount;
            $data->interest_rate = $plan->interest_rate;
            
            if($plan->interval_type == 'partial'){
                $data->next_profit_time = Carbon::now()->addDays($plan->interest_interval);
            }
            $data->matured_time = Carbon::now()->addDays($plan->matured_days);
            $data->status = 1;
            $data->save();

            $wallet->debit($account,$request->fdr_amount);

            $wallet->log($user, $account, $request->fdr_amount, "Fdr", "minus", $data->transaction_no);

            return redirect()->route('user.fdr.index')->with('success','Loan Requesting Successfully');
        }else{
            return redirect()->back()->with('warning','You Don,t have sufficient balance');
        }
    }
}
