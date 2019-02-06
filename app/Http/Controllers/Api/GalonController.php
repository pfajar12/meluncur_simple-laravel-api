<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use JWTAuth;
use Carbon\Carbon;
use DB;
use App\User;
use App\GalonType;
use App\GalonOrder;

class GalonController extends Controller
{
    public function get_galon_type(Request $request)
    {
        $data = GalonType::select('id', 'galon_type_name')->where('status', 1)->get();
        return ApiResponse::response(['success'=>1, 'galon_type'=>$data]);
    }

    public function set_galon_type(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'galon_type'    => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

    	$id = $request->user()->id;
    	$galon_type = $request->json('galon_type');
    	$dataLength = count($galon_type);

    	for($i=0; $i<$dataLength; $i++){
    		DB::table('galon_type_vendor')
    			->insert([
    				'user_id' => $id,
    				'galon_type_id' => $galon_type[$i],
					'created_at' => Carbon::now()
				]);
    	}

        return ApiResponse::response(['success'=>1, 'message'=>'set tipe galon berhasil']);
    }

    function search_galon_vendor(Request $request)
    {
    	if($request->json('current_location')==1){
            $validator = Validator::make($request->all(), [
                'current_location'   => 'required',
                'lat'                => 'required',
                'lng'                => 'required',
                'galon_type'         => 'required',
                'qty'                => 'required',
                'address'            => 'required',
            ]);

    		$lat 	 	= $request->json('lat');
    		$long 	 	= $request->json('lng');
    		$galon_type = $request->json('galon_type');
        	$qty        = $request->json('qty');
        	$address 	= $request->json('address');
        }
        else{
            $validator = Validator::make($request->all(), [
                'current_location'   => 'required',
                'galon_type'         => 'required',
                'qty'                => 'required'
            ]);

    		$lat 		= $request->user()->latitude;
    		$long 	 	= $request->user()->longitude;
    		$address 	= $request->user()->address;
    		$galon_type = $request->json('galon_type');
        	$qty        = $request->json('qty');
        }

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $minDeposit = $qty * 500;

        $getId = DB::table('galon_type_vendor')->select('user_id')->where('galon_type_id', $galon_type)->pluck('user_id');

        $notExceedThanLimit = DB::table('ac_order')
                                    ->select('ac_vendor_id', DB::raw('count(ac_vendor_id) as total'))
                                    ->groupBy('ac_vendor_id')
                                    ->havingRaw('total >= 3')
                                    ->where(function($query){
                                                $query->where('status', 0)
                                                    ->orWhere('status', 1);
                                            })
                                    ->pluck('ac_vendor_id');

        $data = User::query()
                    ->select('*', DB::raw('( 6371 * acos( cos( radians('.$lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$long.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))        
                    ->where('status', 1)
                    ->where('role', 3)
                    ->where('deposit', '>=', $minDeposit)
                    ->whereIn('id', $getId)
                    ->whereNotIn('id', $notExceedThanLimit)
                    ->orderBy('distance', 'asc')
                    ->first();
        return ApiResponse::response([
        						'success'=>0, 
        						'data'=>[
        							'galon_vendor' => $data,
        							'your_order_data' =>[
        								'delivered_latitude'=>$lat,
        								'delivered_longitude'=>$long,
    									'delivered_address'=>$address,
    									'galon_type'=>$galon_type,
    									'qty'=>$qty
        							]
        						]
        					]);
    }

    function create_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'galon_vendor_id' 	 => 'required',
            'galon_type'   		 => 'required',
            'address'            => 'required',
            'lat'                => 'required',
            'lng'                => 'required',
            'qty'                => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

		$galon_vendor_id = $request->json('galon_vendor_id');
		$galon_type 	 = $request->json('galon_type');
		$qty 	 		 = $request->json('qty');
		$lat 	 		 = $request->json('lat');
		$long 	 		 = $request->json('lng');
    	$address 		 = $request->json('address');

    	$order = new GalonOrder;
    	$order->user_id 			= $request->user()->id;
    	$order->galon_vendor_id 	= $galon_vendor_id;
    	$order->galon_type 			= $galon_type;
    	$order->qty 				= $qty;
    	$order->delivered_address 	= $address;
    	$order->delivered_lat 		= $lat;
    	$order->delivered_lng 		= $long;
    	$order->status 				= 0;
    	$order->save();

        return ApiResponse::response(['success'=>1, 'message'=>'order berhasil']);
    }

    public function order_list_user_view(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'limit'   => 'required',
            'offset'  => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $data = DB::table('galon_order AS a')
    				->join('users AS b', 'a.galon_vendor_id', '=', 'b.id')
    				->join('galon_type AS c', 'a.galon_type', '=', 'c.id')
    				->select('a.id', 'a.qty', 'a.delivered_address', 'a.created_at', 'a.status', 'b.fullname AS vendor_name', 'c.galon_type_name')
    				->where('a.user_id', $request->user()->id)
    				->where(function($query){
					        	$query->where('a.status', 0)
					            	->orWhere('a.status', 1);
					    	})
    				->orderBy('a.created_at', 'desc')
                    ->skip($request->offset)
                    ->take($request->limit)
    				->get();

        return ApiResponse::response(['success'=>1, 'data'=>$data]);
    }

    public function order_list_vendor_view(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'limit'   => 'required',
            'offset'  => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $data = DB::table('galon_order AS a')
    				->join('users AS b', 'a.user_id', '=', 'b.id')
    				->join('galon_type AS c', 'a.galon_type', '=', 'c.id')
    				->select('a.id', 'a.qty', 'a.delivered_address', 'a.created_at', 'a.status', 'b.fullname AS user_name', 'c.galon_type_name')
    				->where('a.galon_vendor_id', $request->user()->id)
    				->where(function($query){
					        	$query->where('a.status', 0)
					            	->orWhere('a.status', 1);
					    	})
    				->orderBy('a.created_at', 'desc')
                    ->skip($request->offset)
                    ->take($request->limit)
    				->get();

        return ApiResponse::response(['success'=>1, 'data'=>$data]);
    }

    public function galon_order_accept(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $id = $request->json('id');
        $order = GalonOrder::find($id);
        $order->status = 1;
        $order->save();

        return ApiResponse::response(['success'=>1, 'message'=>'order berhasil diterima']);
    }

    public function galon_order_cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'reason_for_cancel' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $id = $request->json('id');
        $order = GalonOrder::find($id);
        $order->status = -1;
        $order->reason_for_cancel = $request->json('reason_for_cancel');
        $order->save();

        return ApiResponse::response(['success'=>1, 'message'=>'order berhasil dicancel']);
    }

    public function galon_order_approve(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $id = $request->json('id');
        $order = GalonOrder::find($id);
        $qty = $order->qty;
        $vendor_id = $order->galon_vendor_id;

        $user = User::find($vendor_id);
        $deposit = $user->deposit;

        $depositMin = $qty * 500;

        $depositTotal = $deposit - $depositMin;
        
        if($depositMin > $deposit){
            $message = 'approve order gagal. saldo tidak cukup';
        }
        else{
            $user->deposit = $depositTotal;
            $user->save();

            $message = 'approve order berhasil';
            $order->status = 2;
            $order->save();
        }

        return ApiResponse::response(['success'=>1, 'message'=>$message]);
    }

    public function order_log_vendor_view(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit'   => 'required',
            'offset'  => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $data = DB::table('galon_order AS a')
                    ->join('users AS b', 'a.user_id', '=', 'b.id')
                    ->join('galon_type AS c', 'a.galon_type', '=', 'c.id')
                    ->select('a.id', 'a.qty', 'a.delivered_address', 'a.created_at', 'a.status', 'b.fullname AS user_name', 'c.galon_type_name')
                    ->where('a.galon_vendor_id', $request->user()->id)
                    ->where(function($query){
                                $query->where('a.status', -1)
                                    ->orWhere('a.status', 2);
                            })
                    ->orderBy('a.created_at', 'desc')
                    ->skip($request->offset)
                    ->take($request->limit)
                    ->get();

        return ApiResponse::response(['success'=>1, 'data'=>$data]);
    }

    public function order_log_user_view(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit'   => 'required',
            'offset'  => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $data = DB::table('galon_order AS a')
                    ->join('users AS b', 'a.galon_vendor_id', '=', 'b.id')
                    ->join('galon_type AS c', 'a.galon_type', '=', 'c.id')
                    ->select('a.id', 'a.qty', 'a.delivered_address', 'a.created_at', 'a.status', 'b.fullname AS vendor_name', 'c.galon_type_name')
                    ->where('a.user_id', $request->user()->id)
                    ->where(function($query){
                                $query->where('a.status', -1)
                                    ->orWhere('a.status', 2);
                            })
                    ->orderBy('a.created_at', 'desc')
                    ->skip($request->offset)
                    ->take($request->limit)
                    ->get();

        return ApiResponse::response(['success'=>1, 'data'=>$data]);
    }
}
