<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PettyCashExpense extends Model
{
    protected $table = 'petty_cash_expenses';
    public $timestamps = true;

    protected $fillable = [
        'petty_cash_id',
        'details',
        'amount',
        'type', // 'expense' or 'debt_payment'
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function pettyCash()
    {
        return $this->belongsTo(PettyCash::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}