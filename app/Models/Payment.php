<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $guarded = ['id'];
    protected $fillable = ['customer_name', 'transaction_id', 'payment_amount', 'payment_status' ];
}
