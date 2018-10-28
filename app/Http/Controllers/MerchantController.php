<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MerchantRormRequest;
use App\Merchant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Commodity;
use App\Commodity_record;
use App\Log_record;

class MerchantController extends Controller
{
    /**
     * 检查muid与Token
     */
    private function check($muid) {
        $res = ['status' => 'success'];
        if (! Merchant::where('id', $muid)->first()) {
            $res['status'] = 'failed';
            $res['msg'] = 'id does not exist.';
            $res['code'] = 404;
            return $res;
        }

        $id = Merchant::find(Auth::user()->id)->id;

        if ($id != $muid) {
            $res['status'] = 'failed';
            $res['msg'] = 'need authorization.';
            $res['code'] = 400;
        }

        return $res;
    }

    /**
     * 商人注册
     */
    public function register(MerchantRormRequest $request) {
        $merchant = new Merchant;
        $merchant->account = $request->account;
        $merchant->money = $request->money;
        $merchant->alias = $request->alias;
        $merchant->password = bcrypt($request->password);

        if (Merchant::where('account', $request->account)->first()) {
            return response([
                'status' => 'failed',
                'msg' => 'account exists.',
            ], 400);
        }

        $merchant->save();

        return response([
            'status' => 'success',
            'msg' => '',
        ], 200);
    }

    /**
     * 商人登录
     */
    public function login(Request $request) {
        $account = $request['account'];
        $password = $request['password'];

        if (!Merchant::where('account', $request->account)->first()) {
            return response([
                'status' => 'failed',
                'msg' => 'account does not exist.',
            ], 400);
        }

        if (!Hash::check($password, DB::table('merchants')->where('account', $account)->value('password'))) {
            return response([
                'status' => 'failed',
                'msg' => 'wrong password.',
            ], 400);
        }

        $user = Merchant::where('account', $account)->first();

        $token = JWTAuth::fromUser($user);

        $log = new Log_record;
        $log->user = 'merchant';
        $log->user_id = $user->id;
        $log->behaviour = 'login';
        $log->save();

        return response([
            'status' => 'success',
            'msg' => '',
        ], 200)
            ->header('Authorization', $token);
    }

    /**
     * 获取商人信息
     */
    public function show(Request $request, $muid) {
        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        $user = Merchant::find(Auth::user()->id);

        return response([
            'status' => 'success',
            'merchant' => $user,
        ], 200);
    }

    /**
     * 商人登出
     */
    public function logout($muid) {
        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        JWTAuth::setToken(JWTAuth::getToken())->invalidate();

        $log = new Log_record;
        $log->user = 'merchant';
        $log->user_id = $muid;
        $log->behaviour = 'logout';
        $log->save();

        return response([
            'status' => 'success',
            'msg' => ''
        ], 200);
    }

    /**
     * 更新商人信息
     */
    public function update(Request $request, $muid) {
        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        if ($request->money && ($request->money < 1000 || $request->money > 50000)) {
            return response([
                'status' => 'failed',
                'msg' => 'money out of range.',
            ], 200);
        }

        $user = Merchant::find($muid);

        $user->alias = $request->alias ? $request->alias : $user->alias;
        $user->money = $request->money ? $request->money : $user->money;
        $user->password = $request->password ? bcrypt($request->password) : $user->password;
        $user->save();

        return response([
            'status' => 'success',
            'msg' => '',
        ], 200);
    }

    /**
     * 商人添加货物
     */
    public function insertCommodity(Request $request, $muid) {
        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }
        
        if ($request->count < 1 || $request->count > 100) {
            return response([
                'status' => "failed",
                'msg' => 'count out of range.',
            ], 200);
        }

        if ($request->price < 10 || $request->price > 1000) {
            return response([
                'status' => "failed",
                'msg' => 'price out of range.',
            ], 200);
        }

        $commodity_id = DB::table('commodities')
            ->insertGetId([
                'count' => $request->count,
                'price' => $request->price,
                'name' => $request->name,
                'owner' => $muid,
            ]);
        
        $commodity_record = new Commodity_record;
        $commodity_record->commodity_id = $commodity_id;
        $commodity_record->merchant_id = $muid;
        $commodity_record->number = $request->count;
        $commodity_record->save();

        return response([
            'status' => "success",
            'msg' => '',
        ], 200);
    }

    /**
     * 商人获取货物信息
     */
    public function getCommodities($muid) {
        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        $commodities = DB::table('commodities')
            ->where([
                ['owner', $muid],
                ['count', '<>', 0]
            ])
            ->get();

        return response([
            'status' => 'success',
            'commodities' => $commodities,
        ], 200);
    }

    /**
     * 商人更新货物
     */
    public function updataCommodity(Request $request, $muid, $cid) {

        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        if (!DB::table('commodities')
            ->where('id', $cid)->exists()) {
            return response([
                'status' => 'failed',
                'msg' => 'commodity does not exist.',
            ], 404);
        }

        $number =$request->count - DB::table('commodities')
            ->where([
                ['id', $cid],
                ['owner', $muid]
            ])->first()->count;
        
        if ($request->count < 1 || $request->count > 100) {
            return response([
                'status' => "failed",
                'msg' => 'count out of range.',
            ], 200);
        }

        if ($request->price < 10 || $request->price > 1000) {
            return response([
                'status' => "failed",
                'msg' => 'price out of range.',
            ], 200);
        }

         DB::table('commodities')
            ->where([
                ['id', $cid],
                ['owner', $muid]
            ])->update([
                'count' => $request->count,
                'price' => $request->price,
                'name' => $request->name,
            ]);
        
        if ($number != 0) {
            $commodity_record = new Commodity_record;
            $commodity_record->commodity_id = $cid;
            $commodity_record->merchant_id = $muid;
            $commodity_record->number = $number;
            $commodity_record->save();
        }
        
        return response([
            'status' => "success",
            'msg' => '',
        ], 200);
    }

    /**
     * 商人删除货物
     */
    public function deleteCommodity(Request $request, $muid, $cid) {
        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        if (!DB::table('commodities')
            ->where('id', $cid)->exists()) {
            return response([
                'status' => 'failed',
                'msg' => 'commodity does not exist.',
            ], 404);
        }

        $number = 0 - DB::table('commodities')
            ->where([
                ['id', $cid],
                ['owner', $muid]
            ])->first()->count;
        
        DB::table('commodities')
            ->where([
                ['id', $cid],
                ['owner', $muid]
            ])->update(['count' => 0]);
        
        $commodity_record = new Commodity_record;
        $commodity_record->commodity_id = $cid;
        $commodity_record->merchant_id = $muid;
        $commodity_record->number = $number;
        $commodity_record->save();

        return response([
            'status' => "success",
            'msg' => '',
        ], 200);
    }

    /**
     * 商人查看登录信息
     */
    public function getLogRecords($muid) {
        $res = $this->check($muid);

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
                    ['user', 'merchant'],
                    ['user_id', $muid]
                ])
                ->select('time', 'behaviour')->get()
        ], 200);
    }

    /**
     * 商人查看进货记录
     */
    public function getCommodityRecords($muid) {
        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        return response([
            'status' => 'success',
            'commodity_records' => DB::table('commodity_records')
                ->join('commodities', 'commodities.id', '=', 'commodity_records.commodity_id')
                ->select('commodities.name as commodity_name', 'commodities.price as commodity_price', 'commodity_records.number as number', 'commodity_records.time as time')
                ->distinct()->get()
        ], 200);
    }

    /**
     * 商人查看订单信息
     */
    public function getOrder($muid) {
        $res = $this->check($muid);

        if ($res['status'] == 'failed') {
            return response([
                'status' => 'failed',
                'msg' => $res['msg'],
            ], $res['code']);
        }

        $orders = DB::table('order_records')
            ->where('merchant_id', $muid)
            ->get();

        return response([
            'status' => 'success',
            'order_records' => DB::table('order_records')
                ->join('commodities', 'commodities.id', '=', 'order_records.commodity_id')
                ->join('purchasers', 'purchasers.id', '=', 'order_records.purchaser_id')
                ->where('merchant_id', $muid)
                ->select('purchasers.alias as purchaser_alias', 'purchasers.account as purchaser_account', 'commodities.name as commodity_name', 'commodities.price as commodity_price', 'number', 'time')
                ->distinct()->get()
        ], 200);
    }
}
