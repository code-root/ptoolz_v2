<?php

namespace App\Models\transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class handovertransaction extends Model
{
    use HasFactory;
    public $table ='handovertransaction';
        public  $timestamps  =false;

    public $fillable=[
        "id"
        , "offer_id"
        , "sp_percentage"
        , "admin_percentage"
        , "currency_share"
        , "sp_share"
        , "admin_share"
        , "hold_value"
        , "created_at"
    ];

}
