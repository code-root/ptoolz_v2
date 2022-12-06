<?php

namespace App\Models\transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chargetransaction extends Model
{
    use HasFactory;
    public $table = 'chargetransaction';
    public  $timestamps  =false;

    public $fillable = [
        'user_id' ,
        'value',
            ]
;
}
