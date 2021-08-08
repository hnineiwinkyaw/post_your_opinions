<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class ApiAuthController extends Controller
{
    public $user;
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function register (UserRegistrationRequest $request) {

        // validate data
        $validated_data = $request->validated();
       
       // validate role to make sure role exists.  
        $assigned_role = Role::where('name', $validated_data['role'])->first();
        if( !$assigned_role ) {
            Log::error("Role does not exists");
            return response()->json([
                'error' => 'Role does not exist.',
                'status'  => 400,
            ]);
        }
        
        // hash password
        $validated_data['password']=Hash::make($validated_data['password']);
        $validated_data['remember_token'] = Str::random(10);

        // store data
        $user = $this->user->store($validated_data);
        $user->assignRole($validated_data['role']);

        // create token for response
    	$token = $user->createToken('Secret')->accessToken;
    	$response = ['token'=>$token];
    	return response($response,200);
    }

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
