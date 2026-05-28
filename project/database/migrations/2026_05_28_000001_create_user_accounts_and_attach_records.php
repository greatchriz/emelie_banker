<?php

use App\Models\Generalsetting;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountsAndAttachRecords extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('user_accounts')) {
            Schema::create('user_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('bank_plan_id')->nullable()->index();
                $table->string('account_number')->unique();
                $table->string('label')->nullable();
                $table->decimal('balance', 18, 2)->default(0);
                $table->boolean('is_default')->default(false)->index();
                $table->string('status')->default('pending')->index();
                $table->timestamp('plan_end_date')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('rejected_by')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->unsignedBigInteger('disabled_by')->nullable();
                $table->timestamp('disabled_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();
            });
        }

        $this->addAccountId('transactions');
        $this->addAccountId('deposits');
        $this->addAccountId('withdraws');
        $this->addAccountId('wire_transfers');
        $this->addAccountId('user_loans');
        $this->addAccountId('user_dps');
        $this->addAccountId('user_fdr');
        $this->addAccountId('money_requests');

        if (Schema::hasTable('balance_transfers')) {
            Schema::table('balance_transfers', function (Blueprint $table) {
                if (!Schema::hasColumn('balance_transfers', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable()->after('user_id')->index();
                }
                if (!Schema::hasColumn('balance_transfers', 'receiver_account_id')) {
                    $table->unsignedBigInteger('receiver_account_id')->nullable()->after('receiver_id')->index();
                }
            });
        }

        if (Schema::hasTable('money_requests') && !Schema::hasColumn('money_requests', 'receiver_account_id')) {
            Schema::table('money_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('receiver_account_id')->nullable()->after('receiver_id')->index();
            });
        }

        $this->backfillDefaultAccounts();
        $this->backfillAccountIds();
    }

    public function down()
    {
        $this->dropColumnIfExists('balance_transfers', 'receiver_account_id');
        $this->dropColumnIfExists('balance_transfers', 'account_id');
        $this->dropColumnIfExists('money_requests', 'receiver_account_id');

        foreach (['transactions', 'deposits', 'withdraws', 'wire_transfers', 'user_loans', 'user_dps', 'user_fdr', 'money_requests'] as $table) {
            $this->dropColumnIfExists($table, 'account_id');
        }

        Schema::dropIfExists('user_accounts');
    }

    private function addAccountId(string $tableName): void
    {
        if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'account_id')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('user_id')->index();
        });
    }

    private function dropColumnIfExists(string $tableName, string $column): void
    {
        if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, $column)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($column) {
            $table->dropColumn($column);
        });
    }

    private function backfillDefaultAccounts(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('user_accounts')) {
            return;
        }

        $gs = Generalsetting::first();
        User::query()->orderBy('id')->chunk(100, function ($users) use ($gs) {
            foreach ($users as $user) {
                if (DB::table('user_accounts')->where('user_id', $user->id)->where('is_default', 1)->exists()) {
                    continue;
                }

                $accountNumber = $user->account_number ?: $this->generateAccountNumber($gs);
                while (DB::table('user_accounts')->where('account_number', $accountNumber)->exists()) {
                    $accountNumber = $this->generateAccountNumber($gs);
                }

                DB::table('user_accounts')->insert([
                    'user_id' => $user->id,
                    'bank_plan_id' => $user->bank_plan_id,
                    'account_number' => $accountNumber,
                    'label' => 'Default Account',
                    'balance' => $user->balance ?? 0,
                    'is_default' => 1,
                    'status' => 'active',
                    'plan_end_date' => $user->plan_end_date,
                    'approved_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    private function backfillAccountIds(): void
    {
        $defaultAccountSql = '(select id from user_accounts where user_accounts.user_id = {table}.user_id and is_default = 1 limit 1)';

        foreach (['transactions', 'deposits', 'withdraws', 'wire_transfers', 'user_loans', 'user_dps', 'user_fdr'] as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'account_id')) {
                continue;
            }

            DB::statement("update {$table} set account_id = " . str_replace('{table}', $table, $defaultAccountSql) . " where account_id is null");
        }

        if (Schema::hasTable('balance_transfers') && Schema::hasColumn('balance_transfers', 'account_id')) {
            DB::statement("update balance_transfers set account_id = " . str_replace('{table}', 'balance_transfers', $defaultAccountSql) . " where account_id is null");
            DB::statement("update balance_transfers set receiver_account_id = (select id from user_accounts where user_accounts.user_id = balance_transfers.receiver_id and is_default = 1 limit 1) where receiver_account_id is null and receiver_id is not null");
        }

        if (Schema::hasTable('money_requests') && Schema::hasColumn('money_requests', 'account_id')) {
            DB::statement("update money_requests set account_id = " . str_replace('{table}', 'money_requests', $defaultAccountSql) . " where account_id is null");
            DB::statement("update money_requests set receiver_account_id = (select id from user_accounts where user_accounts.user_id = money_requests.receiver_id and is_default = 1 limit 1) where receiver_account_id is null and receiver_id is not null");
        }
    }

    private function generateAccountNumber($gs): string
    {
        $prefix = $gs->account_no_prefix ?? 'AC';
        return $prefix . date('ydis') . random_int(100000, 999999);
    }
}
