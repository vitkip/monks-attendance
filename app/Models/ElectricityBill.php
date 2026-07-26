<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectricityBill extends Model
{
    protected $fillable = [
        'account_number', 'province', 'customer_name', 'bill_month', 'amount', 'image', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'bill_month' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public static function provinces(): array
    {
        return [
            'ນະຄອນຫຼວງວຽງຈັນ', 'ຜົ້ງສາລີ', 'ຫຼວງນ້ຳທາ', 'ບໍ່ແກ້ວ', 'ອຸດົມໄຊ',
            'ໄຊຍະບູລີ', 'ຫຼວງພະບາງ', 'ຫົວພັນ', 'ຊຽງຂວາງ', 'ວຽງຈັນ',
            'ບໍລິຄຳໄຊ', 'ຄຳມ່ວນ', 'ສະຫວັນນະເຂດ', 'ສາລະວັນ', 'ເຊກອງ',
            'ຈຳປາສັກ', 'ອັດຕະປື', 'ໄຊສົມບູນ',
        ];
    }
}
