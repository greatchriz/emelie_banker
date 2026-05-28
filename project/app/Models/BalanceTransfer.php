<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceTransfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'account_id',
        'receiver_id',
        'receiver_account_id',
        'other_bank_id',
        'beneficiary_id',
        'transaction_no',
        'cost',
        'amount',
        'final_amount',
        'type',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class)->withDefault();
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id')->withDefault();
    }

    public function account()
    {
        return $this->belongsTo(UserAccount::class, 'account_id')->withDefault();
    }

    public function receiverAccount()
    {
        return $this->belongsTo(UserAccount::class, 'receiver_account_id')->withDefault();
    }

    public function bank(){
        return $this->belongsTo(OtherBank::class,'other_bank_id')->withDefault();
    }

    public function beneficiary(){
        return $this->belongsTo(Beneficiary::class,'beneficiary_id')->withDefault();
    }
}
