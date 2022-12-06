<?php

namespace App\Models\transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class holdtransaction extends Model
{
    use HasFactory;
    public $table = 'holdtransaction';
    public  $timestamps  =false;

    public $fillable=[
        "id",
         "offer_id",
         "currency",
         "value",
         "holded",
         "created_at"
    ];
}
