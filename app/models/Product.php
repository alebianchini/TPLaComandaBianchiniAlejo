<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'product';
    protected $fillable = ['name', 'price', 'eta', 'employee_type', 'deleted_at'];

    public $timestamps = false;
    public $incrementing = true;
}
