<?php

namespace App\Models\users\data;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mobileCode extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'usermobilecode';
    public $fillable = [
        "id",
        "code",
        "attempts",
        "last attempt",
        "type",
        "user_key"
     ];
}
