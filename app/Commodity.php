<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commodity extends Model
{
    protected $fillable = ['count', 'price', 'name', 'owner'];
    
    public $timestamps = false;
}
