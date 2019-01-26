<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class UserController extends Controller
{
    public function get_role(Request $request)
    {
    	$data[0]['role'] = 2;
    	$data[0]['desc'] = 'user';

    	$data[1]['role'] = 3;
    	$data[1]['desc'] = 'galon vendor';

    	$data[2]['role'] = 4;
    	$data[2]['desc'] = 'laundry vendor';

    	$data[3]['role'] = 5;
    	$data[3]['desc'] = 'ac vendor';

    	$data[4]['role'] = 6;
    	$data[4]['desc'] = 'cctv vendor';

    	return ApiResponse::response(['success'=>1, 'data'=>$data]);
    }
}
