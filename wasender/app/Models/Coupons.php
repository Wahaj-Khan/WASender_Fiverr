<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'coupon_code',
        'discount',
        'plan_id',
    ];
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}