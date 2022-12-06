<?php

namespace App\Models\transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientaccounttransaction extends Model
{
    use HasFactory;
    public $table ='clientaccounttransaction';
    public  $timestamps  =false;

    public $fillable = [
        "id", "offer_id", "user_id", "currency", "value", "type_id", "Created_at"
    ];
}
