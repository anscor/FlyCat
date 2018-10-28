<?php

use Illuminate\Http\Request;
use App\Purchaser;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'auth:merchant'], function(){
    Route::get('merchants/{muid}', 'MerchantController@show');
    Route::get('merchants/{muid}/logout', 'MerchantController@logout');
    Route::put('merchants/{muid}', 'MerchantController@update');
    Route::post('merchants/{muid}/commodities', 'MerchantController@insertCommodity');
    Route::get('merchants/{muid}/commodities', "MerchantController@getCommodities");
    Route::put('merchants/{muid}/commodities/{cid}', 'MerchantController@updataCommodity');
    Route::delete('merchants/{muid}/commodities/{cid}', 'MerchantController@deleteCommodity');
    Route::get('merchants/{muid}/log_records', 'MerchantController@getLogRecords');
    Route::get('merchants/{muid}/commodity_records', 'MerchantController@getCommodityRecords');
    Route::get('merchants/{muid}/order_records', 'MerchantController@getOrder');
});

Route::group(['middleware' => 'auth:purchaser'], function () {
    Route::get('purchasers/{puid}', 'PurchaserController@show');
    Route::get('purchasers/{puid}/logout', 'PurchaserController@logout');
    Route::put('purchasers/{puid}', 'PurchaserController@update');
    Route::post('purchasers/{puid}/order_records', 'PurchaserController@buy');
    Route::get('purchasers/{puid}/log_records', 'PurchaserController@getLog');
    Route::get('purchasers/{puid}/order_records', 'PurchaserController@getOrder');
});

Route::post('merchants/auth/register', 'MerchantController@register');

Route::post('merchants/auth/login', 'MerchantController@login');

Route::post('purchasers/auth/register', 'PurchaserController@register');

Route::post('purchasers/auth/login', 'PurchaserController@login');

Route::get('commodities', 'CommodityController@show');
