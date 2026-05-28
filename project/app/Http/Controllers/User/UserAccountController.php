<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankPlan;
use App\Models\UserAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;

class UserAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(WalletService $wallet)
    {
        $wallet->defaultAccount(auth()->user());
        $accounts = auth()->user()->accounts()->with('plan')->orderByDesc('is_default')->orderBy('id', 'desc')->get();
        $activeAccount = $wallet->activeAccount();

        return view('user.accounts.index', compact('accounts', 'activeAccount'));
    }

    public function create()
    {
        $plans = BankPlan::orderBy('id', 'desc')->get();
        return view('user.accounts.create', compact('plans'));
    }

    public function store(Request $request, WalletService $wallet)
    {
        $request->validate([
            'label' => 'nullable|string|max:100',
            'bank_plan_id' => 'nullable|exists:bank_plans,id',
        ]);

        $wallet->createRequestedAccount(auth()->user(), $request->only('label', 'bank_plan_id'));

        return redirect()->route('user.accounts.index')->with('success', __('Account request submitted. Admin approval is required before it can be used.'));
    }

    public function switch($id)
    {
        $account = UserAccount::where('user_id', auth()->id())->where('id', $id)->firstOrFail();

        if (!$account->isActive()) {
            return redirect()->back()->with('warning', __('You can only switch to an active account.'));
        }

        session(['active_user_account_id' => $account->id]);

        return redirect()->back()->with('success', __('Active account switched successfully.'));
    }

    public function show($id)
    {
        $account = UserAccount::where('user_id', auth()->id())->where('id', $id)->with('plan')->firstOrFail();
        $transactions = $account->transactions()->latest()->paginate(20);

        return view('user.accounts.show', compact('account', 'transactions'));
    }
}
