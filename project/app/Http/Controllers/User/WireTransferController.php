<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use App\Models\BankPlan;
use App\Models\WireTransfer;
use App\Models\WireTransferBank;
use App\Models\UserAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WireTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request, WalletService $wallet){
        $account = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'), false);
        $data['accounts'] = auth()->user()->accounts()->orderByDesc('created_at')->orderByDesc('id')->get();
        $data['selectedAccount'] = $account;
        $data['transfers'] = WireTransfer::where('user_id',auth()->id())
            ->when($account, function ($query) use ($account) {
                $query->where('account_id', $account->id);
            })
            ->orderBy('id','desc')
            ->paginate(20)
            ->appends($request->only('account_id'));
        return view('user.wiretransfer.index',$data);
    }

    public function create(Request $request, WalletService $wallet){
        $data['banks'] = WireTransferBank::whereStatus(1)->orderBy('id','desc')->get();
        $data['accounts'] = $wallet->activeAccounts(auth()->user());
        $data['selectedAccount'] = $wallet->accountFromRequest(auth()->user(), $request->query('account_id'));
        return view('user.wiretransfer.create',$data);
    }

    public function store(Request $request, WalletService $wallet){
        $request->validate([
            'wire_transfer_bank_id' => 'required',
            'currency' => 'required',
            'routing_number' => 'required',
            'country' => 'required',
            'account_number' => 'required',
            'account_holder_name' => 'required',
            'account_id' => 'required',
            'amount' => 'required|numeric|min:0',
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

        session([
            'pending_transaction' => [
                'type' => 'wire_transfer',
                'title' => 'Wire Transfer',
                'amount' => $request->amount,
                'data' => $request->only(
                    'wire_transfer_bank_id',
                    'currency',
                    'routing_number',
                    'country',
                    'swift_code',
                    'account_number',
                    'account_holder_name',
                    'amount',
                    'note'
                ) + ['account_id' => $account->id],
            ],
        ]);

        if (!$user->transaction_pin) {
            return redirect()->route('user.transaction.pin.setup');
        }

        return redirect()->route('user.transaction.pin.verify');
    }

    public function completePendingTransfer(array $input)
    {
        $validator = Validator::make($input, [
            'wire_transfer_bank_id' => 'required',
            'currency' => 'required',
            'routing_number' => 'required',
            'country' => 'required',
            'account_number' => 'required',
            'account_holder_name' => 'required',
            'account_id' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Invalid wire transfer details.');
        }

        $wallet = app(WalletService::class);
        $user = auth()->user()->fresh();
        $account = $wallet->accountFromRequest($user, $input['account_id'] ?? null);

        if ($message = $wallet->ensureActive($account)) {
            return redirect()->route('user.wire.transfer.create')->with('unsuccess', $message);
        }

        if($message = $wallet->hasValidPlan($account)){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess',$message);
        }

        $bank_plan = $wallet->bankPlan($account);
        $dailySend = BalanceTransfer::where('account_id',$account->id)->whereDate('created_at', '=', date('Y-m-d'))->whereStatus(1)->sum('amount');
        $monthlySend = BalanceTransfer::where('account_id',$account->id)->whereMonth('created_at', '=', date('m'))->whereStatus(1)->sum('amount');

        if($dailySend > $bank_plan->daily_send){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Daily send limit over.');
        }

        if($monthlySend > $bank_plan->monthly_send){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Monthly send limit over.');
        }
        
        if($input['amount'] > $account->balance){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Insufficient Account Balance.');
        }

        $data = new WireTransfer();
        $data->transaction_no = Str::random(4).time();
        $data->user_id = auth()->id();
        $data->account_id = $account->id;
        $data->wire_transfer_bank_id = $input['wire_transfer_bank_id'];
        $data->currency = $input['currency'];
        $data->routing_number = $input['routing_number'];
        $data->country = $input['country'];
        $data->swift_code = $input['swift_code'] ?? null;
        $data->account_number = $input['account_number'];
        $data->account_holder_name = $input['account_holder_name'];
        $data->amount = $input['amount'];
        $data->note = $input['note'] ?? null;
        $data->save();

        $wallet->debit($account,$input['amount']);
       
        return redirect()->route('user.wire.transfer.create')->with('success','Wire Transfer Request Sent Successfully');
    }

    public function show($id){
        $data = WireTransfer::whereId($id)->first();
        return view('user.wiretransfer.show',compact('data'));
    }
}
