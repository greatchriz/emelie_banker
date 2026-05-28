<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $fillable = ['txnid','user_id', 'account_id', 'method', 'acc_email', 'iban', 'country', 'acc_name', 'address', 'swift', 'reference', 'amount', 'fee','details', 'created_at', 'updated_at', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function account()
    {
        return $this->belongsTo(UserAccount::class, 'account_id')->withDefault();
    }
}
