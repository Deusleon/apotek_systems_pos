<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $table = 'purchase_returns';
    public $timestamps = false;

    protected $fillable = ['goods_receiving_id', 'quantity', 'reason', 'date', 'created_by', 'status'];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Status mapping for database values
    const STATUS_MAP = [
        2 => self::STATUS_PENDING,
        3 => self::STATUS_APPROVED,
        4 => self::STATUS_REJECTED,
        5 => 'partially_returned' // This is a goods receiving status, not a return status
    ];

    public function goodsReceiving(){
        return $this->belongsTo(GoodsReceiving::class,'goods_receiving_id','id')
                ->with(['product', 'supplier']);
    }

    public function creator(){
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper method to get status label
    public function getStatusLabelAttribute()
    {
        $status = $this->attributes['status'] ?? $this->goodsReceiving->status ?? 2;
        return self::STATUS_MAP[$status] ?? 'unknown';
    }

    // Check if return is pending
    public function isPending()
    {
        $status = $this->attributes['status'] ?? $this->goodsReceiving->status ?? 2;
        return $status == 2; // 2 = pending
    }

    // Check if return is approved
    public function isApproved()
    {
        $status = $this->attributes['status'] ?? $this->goodsReceiving->status ?? 2;
        return $status == 3; // 3 = approved
    }

    // Check if return is rejected
    public function isRejected()
    {
        $status = $this->attributes['status'] ?? $this->goodsReceiving->status ?? 2;
        return $status == 4; // 4 = rejected
    }
}