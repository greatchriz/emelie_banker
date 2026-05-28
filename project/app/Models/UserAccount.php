<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $fillable = [
        'user_id',
        'bank_plan_id',
        'account_number',
        'label',
        'balance',
        'is_default',
        'status',
        'plan_end_date',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'disabled_by',
        'disabled_at',
        'admin_note',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'plan_end_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'disabled_at' => 'datetime',
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function plan()
    {
        return $this->belongsTo(BankPlan::class, 'bank_plan_id')->withDefault();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'account_id');
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'account_id');
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(BalanceTransfer::class, 'account_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(BalanceTransfer::class, 'receiver_account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
