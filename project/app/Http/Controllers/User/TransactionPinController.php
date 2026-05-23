<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TransactionPinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function setupForm()
    {
        return view('user.transactionpin.setup');
    }

    public function setup(Request $request)
    {
        $request->validate([
            'transaction_pin' => 'required|digits:4|confirmed',
        ]);

        $user = auth()->user();
        $user->transaction_pin = Hash::make($request->transaction_pin);
        $user->save();

        if (session()->has('pending_transaction')) {
            return redirect()->route('user.transaction.pin.verify')->with('success', 'Transaction PIN setup successfully.');
        }

        return redirect()->route('user.dashboard')->with('success', 'Transaction PIN setup successfully.');
    }

    public function verifyForm()
    {
        $pending = session('pending_transaction');

        if (!$pending) {
            return redirect()->route('user.dashboard')->with('warning', 'No pending transaction found.');
        }

        if (!auth()->user()->transaction_pin) {
            return redirect()->route('user.transaction.pin.setup');
        }

        return view('user.transactionpin.verify', compact('pending'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'transaction_pin' => 'required|digits:4',
        ]);

        $user = auth()->user();
        $pending = session('pending_transaction');

        if (!$pending) {
            return redirect()->route('user.dashboard')->with('warning', 'No pending transaction found.');
        }

        if (!$user->transaction_pin) {
            return redirect()->route('user.transaction.pin.setup');
        }

        if (!Hash::check($request->transaction_pin, $user->transaction_pin)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['transaction_pin' => 'Wrong Transaction PIN. Please try again.'])
                ->with('unsuccess', 'Wrong Transaction PIN. Please try again.');
        }

        session()->forget('pending_transaction');

        if ($pending['type'] == 'send_money') {
            return app(SendController::class)->completePendingTransfer($pending['data']);
        }

        if ($pending['type'] == 'wire_transfer') {
            return app(WireTransferController::class)->completePendingTransfer($pending['data']);
        }

        return redirect()->route('user.dashboard')->with('warning', 'Invalid pending transaction.');
    }

    public function cancel()
    {
        $pending = session('pending_transaction');
        session()->forget('pending_transaction');

        if (($pending['type'] ?? null) == 'wire_transfer') {
            return redirect()->route('user.wire.transfer.create')->with('warning', 'Transaction cancelled.');
        }

        return redirect()->route('send.money.create')->with('warning', 'Transaction cancelled.');
    }
}
