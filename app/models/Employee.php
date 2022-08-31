<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{   
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'employee';
    protected $fillable = ['uuid', 'full_name', 'password', 'type', 'deleted_at'];
    
    public $timestamps = false;
    public $incrementing = true;
}