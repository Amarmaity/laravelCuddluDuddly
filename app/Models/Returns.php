<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'order_id',
        'order_item_id',
        'user_id',
        'reason',
        'status',
        'refund_method',
        'refund_amount',
    ];

    // 🔗 Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🧩 Helper: generate unique return number
    public static function generateReturnNumber($userId)
    {
        $datePart = now()->format('ymd');
        $userHash = strtoupper(substr(base_convert($userId * 54321, 10, 36), 0, 3));
        $sequence = str_pad(self::count() + 1, 4, '0', STR_PAD_LEFT);
        $timeHash = strtoupper(substr(md5(microtime(true)), 0, 3));
        $digitsOnly = preg_replace('/\D/', '', $datePart . $sequence);
        $checksum = substr(array_sum(str_split($digitsOnly)) % 9, 0, 1);

        return "RT{$datePart}{$userHash}X{$sequence}{$timeHash}X{$checksum}";
    }
}
