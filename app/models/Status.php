<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{   
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'status';
    protected $fillable = ['name', 'type', 'deleted_at'];

    public $timestamps = false;
    public $incrementing = true;
}
