<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commodity_record extends Model
{
    protected $fillable = ['commodity_id', 'merchant_id', 'number'];

    const CREATED_AT = 'time';
    const UPDATED_AT = null;
}
