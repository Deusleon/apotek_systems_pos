<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    protected $table = 'petty_cash';
    public $timestamps = true;

    protected $fillable = [
        'date',
        'opening_balance',
        'amount_received',
        'expenses_total',
        'closing_balance',
        'debts',
        'store_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date' => 'date',
        'opening_balance' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'expenses_total' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'debts' => 'decimal:2'
    ];

    public function expenses()
    {
        return $this->hasMany(PettyCashExpense::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Calculate closing balance
    public function calculateClosingBalance()
    {
        $this->closing_balance = $this->opening_balance - $this->expenses_total;
        if ($this->closing_balance < 0) {
            $this->debts = abs($this->closing_balance);
            $this->closing_balance = 0;
        } else {
            $this->debts = 0;
        }
        return $this;
    }

    // Get previous day's record
    public static function getPreviousDay($date, $store_id)
    {
        return self::where('date', '<', $date)
            ->where('store_id', $store_id)
            ->orderBy('date', 'desc')
            ->first();
    }

    // Get next day's opening balance
    public function getNextDayOpening()
    {
        $nextDay = self::where('date', '>', $this->date)
            ->where('store_id', $this->store_id)
            ->orderBy('date', 'asc')
            ->first();

        if ($nextDay) {
            return $nextDay->opening_balance;
        }

        return 0;
    }
}