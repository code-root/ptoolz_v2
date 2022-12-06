<?php

namespace App\Models\transactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class withdrawaltransaction extends Model
{
    use HasFactory;
    public $table = 'withdrawaltransaction';
    public  $timestamps  =false;

}
