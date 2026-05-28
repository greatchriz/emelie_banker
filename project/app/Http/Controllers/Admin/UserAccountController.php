<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankPlan;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $accounts = UserAccount::with(['user', 'plan'])
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->user_id, function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            })
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.useraccounts.index', compact('accounts'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $plans = BankPlan::orderBy('id', 'desc')->get();
        return view('admin.useraccounts.create', compact('users', 'plans'));
    }

    public function store(Request $request, WalletService $wallet)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'label' => 'nullable|string|max:100',
            'bank_plan_id' => 'nullable|exists:bank_plans,id',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,active,disabled,rejected',
        ]);

        $user = User::findOrFail($request->user_id);
        $account = $wallet->createRequestedAccount($user, $request->only('label', 'bank_plan_id'));
        $account->balance = $request->balance ?? 0;
        $account->status = $request->status;
        if ($request->status === 'active') {
            $account->approved_by = auth('admin')->id();
            $account->approved_at = now();
        }
        $account->save();

        return redirect()->route('admin.user.accounts.index')->with('success', __('Account created successfully.'));
    }

    public function edit($id)
    {
        $account = UserAccount::with(['user', 'plan'])->findOrFail($id);
        $plans = BankPlan::orderBy('id', 'desc')->get();
        return view('admin.useraccounts.edit', compact('account', 'plans'));
    }

    public function update(Request $request, $id, WalletService $wallet)
    {
        $request->validate([
            'label' => 'nullable|string|max:100',
            'bank_plan_id' => 'nullable|exists:bank_plans,id',
            'balance' => 'required|numeric|min:0',
            'status' => 'required|in:pending,active,disabled,rejected',
            'plan_end_date' => 'nullable|date',
            'admin_note' => 'nullable|string',
        ]);

        $account = UserAccount::findOrFail($id);
        $oldStatus = $account->status;
        $account->label = $request->label;
        $account->bank_plan_id = $request->bank_plan_id;
        $account->balance = $request->balance;
        $account->status = $request->status;
        $account->plan_end_date = $request->plan_end_date ? Carbon::parse($request->plan_end_date) : null;
        $account->admin_note = $request->admin_note;

        if ($oldStatus !== 'active' && $request->status === 'active') {
            $account->approved_by = auth('admin')->id();
            $account->approved_at = now();
        }
        if ($request->status === 'disabled') {
            $account->disabled_by = auth('admin')->id();
            $account->disabled_at = now();
        }
        if ($request->status === 'rejected') {
            $account->rejected_by = auth('admin')->id();
            $account->rejected_at = now();
        }

        $account->save();
        $wallet->mirrorDefault($account);

        return redirect()->route('admin.user.accounts.index')->with('success', __('Account updated successfully.'));
    }

    public function status($id, $status, WalletService $wallet)
    {
        abort_unless(in_array($status, ['active', 'disabled', 'rejected', 'pending']), 404);

        $account = UserAccount::findOrFail($id);
        $account->status = $status;
        if ($status === 'active') {
            $account->approved_by = auth('admin')->id();
            $account->approved_at = now();
        }
        if ($status === 'disabled') {
            $account->disabled_by = auth('admin')->id();
            $account->disabled_at = now();
        }
        if ($status === 'rejected') {
            $account->rejected_by = auth('admin')->id();
            $account->rejected_at = now();
        }
        $account->save();
        $wallet->mirrorDefault($account);

        return redirect()->back()->with('success', __('Account status updated successfully.'));
    }
}
