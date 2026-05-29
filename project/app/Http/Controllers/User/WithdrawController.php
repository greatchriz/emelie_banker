<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BankPlan;
use Auth;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use App\Models\WithdrawMethod;
use App\Services\WalletService;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Validator;

class WithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index(Request $request, WalletService $wallet)
    {
        $account = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'), false);
        $accounts = auth()->user()->accounts()->orderByDesc('created_at')->orderByDesc('id')->get();
        $selectedAccount = $account;
        $withdraws = Withdraw::whereUserId(auth()->id())
            ->when($account, function ($query) use ($account) {
                $query->where('account_id', $account->id);
            })
            ->orderBy('id','desc')
            ->paginate(10)
            ->appends($request->only('account_id'));
        return view('user.withdraw.index',compact('withdraws','accounts','selectedAccount'));
    }

    public function create(Request $request, WalletService $wallet)
    {
        $data['sign'] = Currency::whereIsDefault(1)->first();
        $data['methods'] = WithdrawMethod::whereStatus(1)->orderBy('id','desc')->get();
        $data['accounts'] = $wallet->activeAccounts(auth()->user());
        $data['selectedAccount'] = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'));
        return view('user.withdraw.create' ,$data);
    }


    public function store(Request $request, WalletService $wallet)
    {
        $request->validate([
            'account_id' => 'required',
            'amount' => 'required|gt:0',
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
        $dailyWithdraws = Withdraw::where('account_id',$account->id)->whereDate('created_at', '=', date('Y-m-d'))->whereStatus('completed')->sum('amount');
        $monthlyWithdraws = Withdraw::where('account_id',$account->id)->whereMonth('created_at', '=', date('m'))->whereStatus('completed')->sum('amount');

        if($dailyWithdraws > $bank_plan->daily_withdraw){
            return redirect()->back()->with('unsuccess','Daily withdraw limit over.');
        }

        if($monthlyWithdraws > $bank_plan->monthly_withdraw){
            return redirect()->back()->with('unsuccess','Monthly withdraw limit over.');
        }
        
        if($request->amount > $account->balance){
            return redirect()->back()->with('unsuccess','Insufficient Account Balance.');
        }

        $withdrawcharge = WithdrawMethod::whereMethod($request->methods)->first();
        $charge = $withdrawcharge->fixed;

        $messagefee = (($withdrawcharge->percentage / 100) * $request->amount) + $charge;
        $messagefinal = $request->amount - $messagefee;

        $currency = Currency::whereId($request->currency_id)->first();
        $amountToAdd = $request->amount/$currency->value;

        $amount = $amountToAdd;
        $fee = (($withdrawcharge->percentage / 100) * $amount) + $charge;
        $finalamount = $amount - $fee;
        
        if($finalamount < 0){
            return redirect()->back()->with('unsuccess','Request Amount should be greater than this '.$amountToAdd.' (USD)');
        }

        if($finalamount > $account->balance){
            return redirect()->back()->with('unsuccess','Insufficient Balance.');
        }

        $finalamount = number_format((float)$finalamount,2,'.','');

        $wallet->debit($account, $amount);

        $txnid = Str::random(12);
        $newwithdraw = new Withdraw();
        $newwithdraw['user_id'] = auth()->id();
        $newwithdraw['account_id'] = $account->id;
        $newwithdraw['method'] = $request->methods;
        $newwithdraw['txnid'] = $txnid;

        $newwithdraw['amount'] = $finalamount;
        $newwithdraw['fee'] = $fee;
        $newwithdraw['details'] = $request->details;
        $newwithdraw->save();

        $total_amount = $newwithdraw->amount + $newwithdraw->fee;

        $wallet->log($user, $account, $finalamount, "Payout", "minus", $txnid);

        return redirect()->back()->with('success','Withdraw Request Amount : '.$request->amount.' Fee : '.$messagefee.' = '.$messagefinal.' ('.$currency->name.') Sent Successfully.');

    }

    public function details(Request $request, $id){
        $data['data'] = Withdraw::findOrFail($id);
        $data['currency'] = Currency::whereIsDefault(1)->first();
        
        return view('user.withdraw.details',$data);
    }
}
