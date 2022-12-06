<?php

namespace App\Models\transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class acceptedtransaction extends Model
{
    use HasFactory;
    public $table = 'acceptedtransaction';
    public  $timestamps  =false;

    public $fillable = [
        "id",
        "offer_id",
         "sp_percentage",
         "admin_percentage",
         "currency",
         "sp_share",
         "admin_share",
         "created_at"
    ];

}
