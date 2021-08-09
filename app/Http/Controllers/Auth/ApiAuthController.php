<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{

    /**
     * @OA\Post(
     *      path="/login",
     *      operationId="login",
     *      tags={"Auth"},
     *      summary="Login",
     *      description="Login",
     *      @OA\RequestBody(
     *          required=true,
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="user email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="password",
     *                                      type="string",
     *                                      description="user password"
     *                                  )
     *                 )
     *             )
     *         }
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="token",
     *                                      type="string",
     *                                      description="Access Token"
     *                                  )
     *                 )
     *             )
     *         }
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      )
     *     )
     */
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

     /**
     * @OA\Post(
     *      path="/logout",
     *      operationId="logout",
     *      tags={"Auth"},
     *      security={ {"bearer": {} }},
     *      summary="Logout",
     *      description="Logout",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="message",
     *                                      type="string",
     *                                      description="message"
     *                                  )
     *                 )
     *             )
     *         }
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function logout (Request $request) {
    	$token = $request->user()->token();
    	$token->revoke();
    	$response = ['message'=> 'Logged Out'];
    	return response($response, 200);
    }
}
