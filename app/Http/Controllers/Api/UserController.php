<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [    
            'fullname'  => 'required|string|max:191',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:6|max:32',
            'role'      => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        if($request->json('lat')==null){
            $latitude   = 0;
            $longitude  = 0;
        }
        else{
            $latitude   = $request->json('lat');
            $longitude  = $request->json('lng');
        }

        $user = new User;
        $user->fullname     = $request->json('fullname');
        $user->email        = $request->json('email');
        $user->password     = bcrypt($request->json('password'));
        $user->role         = $request->json('role');
        $user->latitude     = $latitude;
        $user->longitude    = $longitude;
        $user->status       = 0;
        $user->deposit      = 0;
        $user->save();

        return ApiResponse::response(['success'=>1, 'message'=>'register success']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [    
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $credentials = [
            'email' => strtolower($request->email),
            'password' => $request->password
        ];

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return ApiResponse::response(['success'=>0, 'message'=>'invalid credentials']);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return ApiResponse::response(['success'=>-2, 'message'=>'server error']);
        }

        // check user active
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->status != 1) {
                return ApiResponse::response(['success'=>-1, 'message'=>'Your account has not been activated yet. Please wait or contact admin']);
            }
        }

        // all good so return the token
        return ApiResponse::response(['success'=>1, 'token'=>$token, 'data'=>Auth::user()]);
    }

    public function profile(Request $request)
    {   
        $id = $request->user()->id;
        $user = User::find($id);
        return ApiResponse::response(['success'=>1, 'user'=>$user]);
    }

    public function profile_update(Request $request)
    {
        $validator = Validator::make($request->all(), [    
            'fullname'  => 'required|string',
            'address'   => 'required|string',
            'phone'     => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $id = $request->user()->id;

        $user = User::find($id);
        $user->fullname = $request->json('fullname');
        $user->address = $request->json('address');
        $user->phone = $request->json('phone');
        $user->save();

        return ApiResponse::response(['success'=>1, 'message'=>'update profile berhasil']);
    }

    public function set_address(Request $request)
    {
        $validator = Validator::make($request->all(), [    
            'address'   => 'required|string',
            'lat'       => 'required',
            'lng'       => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $id = $request->user()->id;

        $user = User::find($id);
        $user->address = $request->json('address');
        $user->latitude = $request->json('lat');
        $user->longitude = $request->json('lng');
        $user->save();

        return ApiResponse::response(['success'=>1, 'message'=>'update lokasi berhasil']);
    }

    public function change_password(Request $request)
    {
        $id = $request->user()->id;
        $validator = Validator::make($request->all(), [    
            'old_password'               => 'required',
            'new_password'               => 'required|confirmed',
            'new_password_confirmation'  => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        if(Hash::check($request->old_password, Auth::user()->password)){
            $user = User::find($id);
            $user->password = bcrypt($request->new_password);
            $user->save();

            return ApiResponse::response(['success'=>1, 'message'=>'password successfully updated']);
        }
        else{
            return ApiResponse::response(['success'=>-1, 'message'=>'Your old password not matched']);
        }
    }

    public function view_client_data(Request $request)
    {
        $validator = Validator::make($request->all(), [    
            'id'   => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::response(['success'=>-1, 'message'=>$validator->errors()->getMessages()]);
        }

        $id = $request->json('id');

        $user = User::select('fullname', 'address', 'phone', 'latitude', 'longitude')->find($id);

        return ApiResponse::response(['success'=>1, 'data'=>$user]);
    }

    public function upload_business_licence(Request $request)
    {
        $cover = $request->file('upload');
        $extension = $cover->getClientOriginalExtension();
        Storage::disk('public')->put($cover->getFilename().'.'.$extension,  File::get($cover));

        $filename = $cover->getFilename().'.'.$extension;

        $id = $request->user()->id;
        $user = User::find($id);
        $user->business_license_photo = $filename;
        $user->save();

        return ApiResponse::response(['success'=>1, 'message'=>'upload izin bisnis berhasil']);
    }

    public function upload_business_place(Request $request)
    {
        $cover = $request->file('upload');
        $extension = $cover->getClientOriginalExtension();
        Storage::disk('public')->put($cover->getFilename().'.'.$extension,  File::get($cover));

        $filename = $cover->getFilename().'.'.$extension;

        $id = $request->user()->id;
        $user = User::find($id);
        $user->business_place_photo = $filename;
        $user->save();

        return ApiResponse::response(['success'=>1, 'data'=>'upload foto tempat bisnis berhasil']);
    }
}
