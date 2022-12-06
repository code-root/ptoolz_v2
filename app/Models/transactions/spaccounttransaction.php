<?php

namespace App\Models\transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class spaccounttransaction extends Model
{
    use HasFactory;
    public $table = 'spaccounttransaction';
    public  $timestamps  =false;
    public $fillable = [
        "id", "offer_id", "user_id", "currency", "value", "type_id", "created_at"
    ] ;

}
