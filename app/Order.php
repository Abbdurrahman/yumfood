<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function detailorder()
    {
        return $this->hasMany('App\DetailOrder', 'order_id', 'id');
    }
}
