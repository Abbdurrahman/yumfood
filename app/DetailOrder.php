<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    public function order()
    {
        return $this->belongsTo('App\Order', 'id', 'order_id');
    }
}
