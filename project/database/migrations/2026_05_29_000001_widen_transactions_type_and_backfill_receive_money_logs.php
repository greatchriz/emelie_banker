<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions') || !Schema::hasColumn('transactions', 'type')) {
            return;
        }

        DB::statement("ALTER TABLE `transactions` MODIFY `type` VARCHAR(50) NOT NULL");

        if (
            !Schema::hasTable('balance_transfers') ||
            !Schema::hasTable('users') ||
            !Schema::hasColumn('balance_transfers', 'receiver_id') ||
            !Schema::hasColumn('balance_transfers', 'receiver_account_id') ||
            !Schema::hasColumn('transactions', 'account_id') ||
            !Schema::hasColumn('transactions', 'receiver_id')
        ) {
            return;
        }

        DB::statement("
            INSERT INTO `transactions` (
                `user_id`,
                `account_id`,
                `receiver_id`,
                `email`,
                `amount`,
                `type`,
                `profit`,
                `txnid`,
                `created_at`,
                `updated_at`
            )
            SELECT
                bt.`receiver_id`,
                bt.`receiver_account_id`,
                bt.`user_id`,
                users.`email`,
                bt.`amount`,
                'Receive Money',
                'plus',
                bt.`transaction_no`,
                bt.`created_at`,
                bt.`updated_at`
            FROM `balance_transfers` bt
            INNER JOIN `users` ON users.`id` = bt.`receiver_id`
            WHERE bt.`type` = 'own'
                AND bt.`status` = 1
                AND bt.`receiver_id` IS NOT NULL
                AND bt.`receiver_account_id` IS NOT NULL
                AND bt.`transaction_no` IS NOT NULL
                AND NOT EXISTS (
                    SELECT 1
                    FROM `transactions` tx
                    WHERE tx.`txnid` = bt.`transaction_no`
                        AND tx.`user_id` = bt.`receiver_id`
                        AND tx.`profit` = 'plus'
                )
        ");
    }

    public function down(): void
    {
        if (!Schema::hasTable('transactions') || !Schema::hasColumn('transactions', 'type')) {
            return;
        }

        if (
            Schema::hasTable('balance_transfers') &&
            Schema::hasColumn('balance_transfers', 'receiver_id') &&
            Schema::hasColumn('balance_transfers', 'receiver_account_id') &&
            Schema::hasColumn('transactions', 'account_id')
        ) {
            DB::statement("
                DELETE tx
                FROM `transactions` tx
                INNER JOIN `balance_transfers` bt
                    ON bt.`transaction_no` = tx.`txnid`
                    AND bt.`receiver_id` = tx.`user_id`
                    AND bt.`receiver_account_id` = tx.`account_id`
                WHERE tx.`type` = 'Receive Money'
                    AND tx.`profit` = 'plus'
                    AND bt.`type` = 'own'
                    AND bt.`status` = 1
            ");
        }

        DB::statement("
            ALTER TABLE `transactions`
            MODIFY `type` ENUM(
                'Deposit',
                'Payout',
                'Referral Bonus',
                'Send Money',
                'Request Money',
                'Subscription',
                'Loan',
                'Dps',
                'Fdr'
            ) NOT NULL
        ");
    }
};
