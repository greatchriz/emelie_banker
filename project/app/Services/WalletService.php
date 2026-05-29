<?php

namespace App\Services;

use App\Models\BankPlan;
use App\Models\Generalsetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    public function defaultAccount(User $user): UserAccount
    {
        $account = $user->accounts()->where('is_default', 1)->first();

        if ($account) {
            return $account;
        }

        return $this->createDefaultAccount($user);
    }

    public function activeAccount(?User $user = null): ?UserAccount
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return null;
        }

        return $user->accounts()->active()->orderByDesc('is_default')->orderBy('id')->first();
    }

    public function accountByNumber(string $accountNumber): ?UserAccount
    {
        return UserAccount::where('account_number', $accountNumber)->first();
    }

    public function accountFromRequest(User $user, $accountId, bool $requireActive = true): ?UserAccount
    {
        if (!$accountId) {
            return null;
        }

        $account = $user->accounts()->where('id', $accountId)->first();

        if (!$account) {
            return null;
        }

        if ($requireActive && !$account->isActive()) {
            return null;
        }

        return $account;
    }

    public function activeAccounts(User $user)
    {
        return $user->accounts()->active()->orderByDesc('is_default')->orderBy('label')->orderBy('id')->get();
    }

    public function ensureActive(?UserAccount $account): ?string
    {
        if (!$account) {
            return __('Please select an active account to continue.');
        }

        if (!$account->isActive()) {
            return __('This account is not active. Please select an active account or contact support.');
        }

        return null;
    }

    public function hasValidPlan(UserAccount $account): ?string
    {
        if ($account->bank_plan_id === null) {
            return __('You have to buy a plan to use this account.');
        }

        if ($account->plan_end_date && now()->gt($account->plan_end_date)) {
            return __('Plan Date Expired.');
        }

        return null;
    }

    public function bankPlan(UserAccount $account): ?BankPlan
    {
        return $account->bank_plan_id ? BankPlan::find($account->bank_plan_id) : null;
    }

    public function debit(UserAccount $account, $amount): void
    {
        DB::transaction(function () use ($account, $amount) {
            $account = UserAccount::whereKey($account->id)->lockForUpdate()->firstOrFail();
            $account->decrement('balance', $amount);
            $this->mirrorDefault($account->fresh());
        });
    }

    public function credit(UserAccount $account, $amount): void
    {
        DB::transaction(function () use ($account, $amount) {
            $account = UserAccount::whereKey($account->id)->lockForUpdate()->firstOrFail();
            $account->increment('balance', $amount);
            $this->mirrorDefault($account->fresh());
        });
    }

    public function mirrorDefault(UserAccount $account): void
    {
        if (!$account->is_default) {
            return;
        }

        User::whereKey($account->user_id)->update([
            'balance' => $account->balance,
            'account_number' => $account->account_number,
            'bank_plan_id' => $account->bank_plan_id,
            'plan_end_date' => $account->plan_end_date,
        ]);
    }

    public function createDefaultAccount(User $user): UserAccount
    {
        $accountNumber = $user->account_number;
        if (!$accountNumber || UserAccount::where('account_number', $accountNumber)->exists()) {
            $accountNumber = $this->generateAccountNumber();
        }

        $account = UserAccount::create([
            'user_id' => $user->id,
            'bank_plan_id' => $user->bank_plan_id,
            'account_number' => $accountNumber,
            'label' => 'Default Account',
            'balance' => $user->balance ?? 0,
            'is_default' => true,
            'status' => 'active',
            'plan_end_date' => $user->plan_end_date,
            'approved_at' => now(),
        ]);

        $this->mirrorDefault($account);

        return $account;
    }

    public function createRequestedAccount(User $user, array $input): UserAccount
    {
        return UserAccount::create([
            'user_id' => $user->id,
            'bank_plan_id' => $input['bank_plan_id'] ?? $user->bank_plan_id,
            'account_number' => $this->generateAccountNumber(),
            'label' => $input['label'] ?? 'Additional Account',
            'balance' => 0,
            'is_default' => false,
            'status' => 'pending',
            'plan_end_date' => $user->plan_end_date,
        ]);
    }

    public function generateAccountNumber(): string
    {
        $gs = Generalsetting::first();
        $prefix = $gs->account_no_prefix ?? 'AC';

        do {
            $accountNumber = $prefix . date('ydis') . random_int(100000, 999999);
        } while (UserAccount::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }

    public function log(User $user, UserAccount $account, $amount, string $type, string $profit, ?string $txnid = null): Transaction
    {
        $transaction = new Transaction();
        $transaction->email = $user->email;
        $transaction->amount = $amount;
        $transaction->type = $type;
        $transaction->profit = $profit;
        $transaction->txnid = $txnid ?: Str::random(12);
        $transaction->user_id = $user->id;
        $transaction->account_id = $account->id;
        $transaction->save();

        return $transaction;
    }
}
