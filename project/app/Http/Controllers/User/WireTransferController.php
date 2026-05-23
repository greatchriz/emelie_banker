<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use App\Models\BankPlan;
use App\Models\WireTransfer;
use App\Models\WireTransferBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WireTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $data['transfers'] = WireTransfer::where('user_id',auth()->id())->orderBy('id','desc')->paginate(20);
        return view('user.wiretransfer.index',$data);
    }

    public function create(){
        $data['banks'] = WireTransferBank::whereStatus(1)->orderBy('id','desc')->get();
        return view('user.wiretransfer.create',$data);
    }

    public function store(Request $request){
        $request->validate([
            'wire_transfer_bank_id' => 'required',
            'currency' => 'required',
            'routing_number' => 'required',
            'country' => 'required',
            'account_number' => 'required',
            'account_holder_name' => 'required',
            'amount' => 'required|numeric|min:0',
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
                ),
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
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Invalid wire transfer details.');
        }

        $user = auth()->user()->fresh();

        if($user->bank_plan_id === null){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','You have to buy a plan to withdraw.');
        }

        if(now()->gt($user->plan_end_date)){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Plan Date Expired.');
        }

        $bank_plan = BankPlan::whereId($user->bank_plan_id)->first();
        $dailySend = BalanceTransfer::whereUserId(auth()->id())->whereDate('created_at', '=', date('Y-m-d'))->whereStatus(1)->sum('amount');
        $monthlySend = BalanceTransfer::whereUserId(auth()->id())->whereMonth('created_at', '=', date('m'))->whereStatus(1)->sum('amount');

        if($dailySend > $bank_plan->daily_send){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Daily send limit over.');
        }

        if($monthlySend > $bank_plan->monthly_send){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Monthly send limit over.');
        }
        
        if($input['amount'] > $user->balance){
            return redirect()->route('user.wire.transfer.create')->with('unsuccess','Insufficient Account Balance.');
        }

        $data = new WireTransfer();
        $data->transaction_no = Str::random(4).time();
        $data->user_id = auth()->id();
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

        $user->decrement('balance',$input['amount']);
       
        return redirect()->route('user.wire.transfer.create')->with('success','Wire Transfer Request Sent Successfully');
    }

    public function show($id){
        $data = WireTransfer::whereId($id)->first();
        return view('user.wiretransfer.show',compact('data'));
    }
}
