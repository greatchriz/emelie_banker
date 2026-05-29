<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\DpsPlan;
use App\Models\InstallmentLog;
use App\Models\Transaction;
use App\Models\UserDps;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserDpsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, WalletService $wallet){
        $account = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'), false);
        $data['dps'] = UserDps::whereUserId(auth()->id())->when($account, fn ($query) => $query->where('account_id', $account->id))->orderby('id','desc')->paginate(10)->appends($request->only('account_id'));
        return view('user.dps.index',$data);
    }

    public function running(Request $request, WalletService $wallet){
        $account = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'), false);
        $data['dps'] = UserDps::whereStatus(1)->whereUserId(auth()->id())->when($account, fn ($query) => $query->where('account_id', $account->id))->orderby('id','desc')->paginate(10)->appends($request->only('account_id'));
        return view('user.dps.running',$data);
    }

    public function matured(Request $request, WalletService $wallet){
        $account = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'), false);
        $data['dps'] = UserDps::whereStatus(2)->whereUserId(auth()->id())->when($account, fn ($query) => $query->where('account_id', $account->id))->orderby('id','desc')->paginate(10)->appends($request->only('account_id'));
        return view('user.dps.matured',$data);
    }

    public function dpsPlan(){
        $data['plans'] = DpsPlan::orderBy('id','desc')->whereStatus(1)->orderby('id','desc')->paginate(12);
        return view('user.dps.plan',$data);
    }

    public function planDetails(Request $request, WalletService $wallet, $id){
        $data['data'] = DpsPlan::findOrFail($id);
        $data['accounts'] = $wallet->activeAccounts(auth()->user());
        $data['selectedAccount'] = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'));
        return view('user.dps.apply',$data);
    }

    public function dpsSubmit(Request $request, WalletService $wallet){
        $user = auth()->user();
        $request->validate(['account_id' => 'required']);
        $account = $wallet->accountFromRequest($user, $request->account_id);
        if ($message = $wallet->ensureActive($account)) {
            return redirect()->back()->with('warning', $message);
        }
        if($account->balance >= $request->per_installment){
            $data = new UserDps();

            $plan = DpsPlan::findOrFail($request->dps_plan_id);
            $data->transaction_no = Str::random(4).time();
            $data->user_id = auth()->id();
            $data->account_id = $account->id;
            $data->dps_plan_id = $plan->id;
            $data->per_installment = $plan->per_installment;
            $data->installment_interval = $plan->installment_interval;
            $data->total_installment = $plan->total_installment;
            $data->interest_rate = $plan->interest_rate;
            $data->given_installment = 1;
            $data->deposit_amount = $request->deposit_amount;
            $data->matured_amount = $request->matured_amount;
            $data->paid_amount = $request->per_installment;
            $data->status = 1;
            $data->next_installment = Carbon::now()->addDays($plan->installment_interval);
            $data->save();

            $wallet->debit($account,$request->per_installment);

            $log = new InstallmentLog();
            $log->user_id = auth()->id();
            $log->transaction_no = $data->transaction_no;
            $log->type = 'dps';
            $log->amount = $request->per_installment;
            $log->save();

            $wallet->log($user, $account, $request->per_installment, "Dps", "minus", $data->transaction_no);
            
            return redirect()->route('user.dps.index')->with('success','DPS application submitted');
        }else{
            return redirect()->back()->with('warning','You Don,t have sufficient balance');
        }
    }

    public function log($id){
        $loan = UserDps::findOrfail($id);
        $logs = InstallmentLog::whereTransactionNo($loan->transaction_no)->whereUserId(auth()->id())->orderby('id','desc')->orderby('id','desc')->paginate(20);
        $currency = Currency::whereIsDefault(1)->first();

        return view('user.dps.log',compact('logs','currency'));
    }
}
