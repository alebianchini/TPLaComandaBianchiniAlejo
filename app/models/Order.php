<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'orders';
    protected $fillable = ['number', 'eta', 'completed_time', 'status', 'waiter', 'associated_table', 'amount', 'customer_name', 'picture_path', 'deleted_at'];

    public $timestamps = false;
    public $incrementing = true;
}
