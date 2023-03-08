<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \DateTimeInterface;

class limmiter extends Model
{
    use HasFactory;
    public $table = 'limmiter';
    protected $fillable = [
        'minamount',
        'maxamount',
        'maxtrans',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
