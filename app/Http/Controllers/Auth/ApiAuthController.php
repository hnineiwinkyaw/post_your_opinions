<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function register (Request $request) {
    	$validator = Validator::make($request->all(),[
    		'name' => 'required|string|max:255',
    		'email' => 'required|string|email|max:255|unique:users',
    		'password' => 'required|string|min:6|confirmed',
            'type' => 'integer',
    	]);
    	if ($validator->fails()) {
    		return response(['errors'=>$validator->errors()->all()],400);
    	}
    	$request['password']=Hash::make($request['password']);
    	$request['remember_token'] = Str::random(10);
        $request['type'] = $request['type'] ? $request['type']  : 0;
    	$user = User::create($request->toArray());
    	$token = $user->createToken('Secret')->accessToken;
    	$response = ['token'=>$token];
    	return response($response,200);
    }

    public function login (Request $request) {
    	$validator = Validator::make($request->all(), [
    		'email'=> 'required|string|email|max:255',
    		'password' => 'required|string|min:6',
    	]);
    	if ($validator->fails()) {
    		return response(['errors'=>$validator->errors()->all()],400);
    	}
    	$user = User::where('email',$request->email)->first();
    	if ($user) {
    		if(Hash::check($request->password, $user->password)) {
    			$token = $user->createToken('Secret')->accessToken;
    			$response = ['token' => $token];
    			return response($response, 200);
    		}
    	} else {
    		$response = ["message"=>"User does not exist"];
    		return response($response, 400);
    	}
    }

    public function logout (Request $request) {
    	$token = $request->user()->token();
    	$token->revoke();
    	$response = ['message'=> 'Logged Out'];
    	return response($response, 200);
    }
}
