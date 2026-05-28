<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function account()
    {
        return $this->belongsTo(UserAccount::class, 'account_id')->withDefault();
    }
}
