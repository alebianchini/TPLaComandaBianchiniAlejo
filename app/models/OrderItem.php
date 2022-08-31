<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'order_items';
    protected $fillable = ['product', 'status', 'order_id', 'eta', 'completed_time','deleted_at'];

    public $timestamps = false;
    public $incrementing = true;
}
