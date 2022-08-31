<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeType extends Model
{   
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'employee_type';
    protected $fillable = ['name', 'deleted_at'];

    public $timestamps = false;
    public $incrementing = true;
}
