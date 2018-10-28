<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_record extends Model
{
    protected $fillable = ['purchaser_id', 'merchant_id', 'number', 'commodity_id'];

    const CREATED_AT = 'time';
    const UPDATED_AT = null;
}
