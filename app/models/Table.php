<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{   
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'tables';
    protected $fillable = ['number', 'status', 'deleted_at'];

    public $timestamps = false;
    public $incrementing = true;
}
