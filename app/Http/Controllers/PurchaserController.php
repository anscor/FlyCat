<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchaser;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Commodity;
use App\Commodity_record;
use App\Log_record;
use App\Merchant;
use App\Order_record;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PurchaserRormRequest;

class PurchaserController extends Controller
{
    //
    /**
     * 检查muid与Token
     */
    private function check($puid) {
        $res = ['status' => 'success'];
        if (! Purchaser::where('id', $puid)->first()) {
            $res['status'] = 'failed';
            $res['msg'] = 'id does not exist.';
            $res['code'] = 404;
            return $res;
        }

        $id = Purchaser::find(Auth::user()->id)->id;

        if ($id != $puid) {
            $res['status'] = 'failed';
            $res['msg'] = 'need authorization.';
            $res['code'] = 400;
        }

        return $res;
    }

    /**
     * 买家注册
     */
    public function register(PurchaserRormRequest $request) {
        if (Purchaser::where('account', $request->account)->first()) {
            return response([
                'status' => 'failed',
                'msg' => 'account exists.',
            ], 400);
        }

        $purchaser = new Purchaser;
        $purchaser->account = $request->account;
        $purchaser->password = bcrypt($request->password);
        $purchaser->alias = $request->alias;
        $purchaser->blance = $request->blance;
        $purchaser->save();

        return response([
            'status' => 'success',
            'msg' => '',
        ], 200);
    }

    /**
     * 买家登录
     */
    public function login(Request $request) {
        if (! $user = Purchaser::where('account', $request->account)->first()) {
            return response([
                'status' => 'failed',
                'msg' => 'account does not exist.',
            ], 400);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response([
                'status' => 'failed',
                'msg' => 'wrong password.',
            ], 400);
        }

        $token = JWTAuth::fromUser($user);

        $log = new Log_record;
        $log->user = 'purchaser';
        $log->user_id = $user->id;
        $log->behaviour = 'login';
        $log->save();

        return response([
            'status' => 'success',
            'purchaser' => $user,
        ], 200)
            ->header('Authorization', $token);
    }

    /**
     * 获取买家信息
     */
    public function show($puid) {
        $res = $this->check($puid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        $user = Purchaser::find($puid);

        return response([
            'status' => 'success',
            'purchaser' => $user,
        ], 200);
    }

    /**
     * 买家登出
     */
    public function logout($puid) {
        $res = $this->check($puid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        JWTAuth::setToken(JWTAuth::getToken())->invalidate();

        $log = new Log_record;
        $log->user = 'purchaser';
        $log->user_id = $puid;
        $log->behaviour = 'logout';
        $log->save();

        return response([
            'status' => 'success',
            'msg' => ''
        ], 200);
    }

    /**
     * 更新买家信息
     */
    public function update(Request $request, $puid) {
        $res = $this->check($puid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        $data = json_decode($request->getContent());

        if ($data->blance && ($data->blance < 1000 || $data->blance > 50000)) {
            return response([
                'status' => 'failed',
                'msg' => 'blance out of range.',
            ], 200);
        }

        $user = Purchaser::find($puid);

        $user->alias = $data->alias ? $data->alias : $user->alias;
        $user->blance = $data->blance ? $data->blance : $user->blance;
        $user->password = $data->password ? bcrypt($data->password) : $user->password;
        $user->save();

        return response([
            'status' => 'success',
            'msg' => '',
        ], 200);
    }

    /**
     * 买家购买商品
     */
    public function buy(Request $request, $puid) {
        $res = $this->check($puid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        $user = Purchaser::find($puid);

        if (! $commodity = Commodity::find($request->commodity_id)) {
            return response([
                'status' => 'failed',
                'msg' => 'commodity does not exist.',
            ], 404);
        }

        if ($commodity->count == 0) {
            return response([
                'status' => 'failed',
                'msg' => 'commodity does not exist.',
            ], 404);
        }

        if ($commodity->count < $request->count) {
            return response([
                'status' => 'failed',
                'msg' => 'count is so large.',
            ], 400);
        }

        if ($user->blance < $commodity->price * $request->count) {
            return response([
                'status' => 'failed',
                'msg' => 'blance is not enough.',
            ], 400);
        }

        $user->blance -= $commodity->price * $request->count;
        $user->save();

        $merchant = Merchant::find($commodity->owner);
        $merchant->money += $commodity->price * $request->count;
        $merchant->save();

        $commodity->count -= $request->count;
        $commodity->save();

        $order = new Order_record;
        $order->purchaser_id = $puid;
        $order->merchant_id = $commodity->owner;
        $order->number = $request->count;
        $order->commodity_id = $commodity->id;
        $order->save();

        return response([
            'status' => "success",
            'msg' => '',
        ], 201);
    }

    /**
     * 买家查看登录信息
     */
    public function getLog($puid) {
        $res = $this->check($puid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        return response([
            'status' => 'success',
            'log_records' => DB::table('log_records')
                ->where([
                    ['user', 'purchaser'],
                    ['user_id', $puid]
                ])
                ->select('time', 'behaviour')->get(),
        ], 200);
    }

    /**
     * 买家查看订单信息
     */
    public function getOrder($puid) {
        $res = $this->check($puid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        return response([
            'status' => 'success',
            'order_records' => DB::table('order_records')
                ->join('commodities', 'commodities.id', '=', 'order_records.commodity_id')
                ->join('merchants', 'merchants.id', '=', 'order_records.merchant_id')
                ->where('purchaser_id', $puid)
                ->select('merchants.alias as merchant_alias', 'merchants.account as merchant_account', 'commodities.name as commodity_name', 'commodities.price as commodity_price', 'number', 'time')
                ->distinct()->get()
        ], 200);
    }
}
