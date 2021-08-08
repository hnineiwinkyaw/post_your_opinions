<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function login (LoginRequest $request) {
    	
        // validate data
    	$validated_data = $request->validated();

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
