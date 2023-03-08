<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class faspay extends Model
{
    use HasFactory;
    public $table = 'faspay';

    protected $fillable = [
        'merchanname',
        'userid',
        'password',
        'redirectUrl',
        
    ];
}
