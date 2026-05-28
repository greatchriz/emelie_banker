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
    
    public function index(WalletService $wallet){
        $account = $wallet->activeAccount();
        $data['deposits'] = Deposit::orderby('id','desc')->whereUserId(auth()->id())
            ->when($account, function ($query) use ($account) {
                $query->where('account_id', $account->id);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->paginate(10);
        return view('user.deposit.index',$data);
    }

    public function create(){
        $data['availableGatways'] = ['flutterwave','authorize.net','razorpay','mollie','paytm','instamojo','stripe','paypal','paystack'];
        $data['gateways'] = PaymentGateway::OrderBy('id','desc')->whereStatus(1)->get();
        return view('user.deposit.create',$data);
    }
}
