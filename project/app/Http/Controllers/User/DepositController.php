<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\PaymentGateway;
use App\Services\WalletService;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request, WalletService $wallet){
        $account = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'), false);
        $data['accounts'] = auth()->user()->accounts()->orderByDesc('created_at')->orderByDesc('id')->get();
        $data['selectedAccount'] = $account;
        $data['deposits'] = Deposit::with('account')->orderby('id','desc')->whereUserId(auth()->id())
            ->when($account, function ($query) use ($account) {
                $query->where('account_id', $account->id);
            })
            ->paginate(10)
            ->appends($request->only('account_id'));
        return view('user.deposit.index',$data);
    }

    public function create(Request $request, WalletService $wallet){
        $data['availableGatways'] = ['flutterwave','authorize.net','razorpay','mollie','paytm','instamojo','stripe','paypal','paystack'];
        $data['gateways'] = PaymentGateway::OrderBy('id','desc')->whereStatus(1)->get();
        $data['accounts'] = $wallet->activeAccounts(auth()->user());
        $data['selectedAccount'] = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'));
        return view('user.deposit.create',$data);
    }
}
