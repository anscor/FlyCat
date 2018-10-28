<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commodity_record;

class CommodityRecordConroller extends Controller
{
    public function insert($data) {
        // DB::table('commodity_record')
        //     ->insert($data);
        $t = new Commodity_record;
        $t->commodity_id = $data->commodity_id;
        $t->merchant_id = $data->merchant_id;
        $t->number = $data->number;
        $t->save();
    }
}
