<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'survey';
    protected $fillable = ['table_points', 'restaurant_points', 'cook_points', 'waiter_points', 'comment', 'associated_table', 'associated_order', 'deleted_at'];

    public $timestamps = false;
    public $incrementing = true;
}
