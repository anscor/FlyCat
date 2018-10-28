<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commodity;
use Illuminate\Support\Facades\DB;

class CommodityController extends Controller
{
    /**
     * 获取商品信息
     */
    public function show() {
        return response([
            'status' => 'success',
            'commodities' => DB::table('commodities')
                ->where('count', '<>', 0)
                ->select('id', 'count', 'price', 'name')
                ->get(),
        ], 200);
    }
}
