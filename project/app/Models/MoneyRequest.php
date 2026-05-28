<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'transaction_no',
        'user_id',
        'account_id',
        'receiver_id',
        'receiver_account_id',
        'receiver_name',
        'cost',
        'amount',
        'status',
    ];

    public function account()
    {
        return $this->belongsTo(UserAccount::class, 'account_id')->withDefault();
    }

    public function receiverAccount()
    {
        return $this->belongsTo(UserAccount::class, 'receiver_account_id')->withDefault();
    }
}
