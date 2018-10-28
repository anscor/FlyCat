<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_record extends Model
{
    protected $fillable = ['user_id'];

    const CREATED_AT = 'time';
    const UPDATED_AT = null;
}
