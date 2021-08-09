<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Repositories\UserRepository;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $user;
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * @OA\Get(
     *      path="/users",
     *      tags={"Users"},
     *      operationId="get all users ",
     *      summary="Get list of users",
     *      security={ {"bearer": {} }},
     *      description="Returns list of users",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="users",
     *                         type="array",
     *                         collectionFormat="multi",
     *                         @OA\Items(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="user id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="user name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="user email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email_verified_at",
     *                                      type="string",
     *                                      description="user email verified timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="user creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="user updated timestamp"
     *                                  )
     *                          )
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Message"
     *                     )
     *                 )
     *             )
     *         }
     *       ),
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
    public function index()
    {
        $users = User::all();

        return response([ 
            'users' => UserResource::collection($users),
            'message' => 'Successful'
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/users",
     *      operationId="create user",
     *      tags={"Users"},
     *      security={ {"bearer": {} }},
     *      summary="Create new user",
     *      description="Create new user",
     *      @OA\RequestBody(
     *          required=true,
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="user name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="user email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="password",
     *                                      type="string",
     *                                      description="user password"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="password_confirmation",
     *                                      type="string",
     *                                      description="user password confirmation"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="role",
     *                                      type="string",
     *                                      description="user role"
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
     *                                @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="user id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="user name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="user email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email_verified_at",
     *                                      type="string",
     *                                      description="user email verified timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="user creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="user updated timestamp"
     *                                  )
     *                 )
     *             )
     *         }
     *       ),
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
    public function create(UserRegistrationRequest $request)
    {
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

    /**
     * @OA\Get(
     *      path="/users/{id}",
     *      operationId="get user details ",
     *      tags={"Users"},
     *      summary="Get detail of user object",
     *      security={ {"bearer": {} }},
     *      description="Returns user detail object",
     *      @OA\Parameter(
     *          name="id",
     *          description="user id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="user id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="user name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="user email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email_verified_at",
     *                                      type="string",
     *                                      description="user email verified timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="user creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="user updated timestamp"
     *                                  )
     *                 )
     *             )
     *         }
     *       ),
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
    public function show(User $user)
    {
        return $user;
    }

    /**
     * @OA\Put(
     *      path="/users/{id}",
     *      operationId="Update user details ",
     *      tags={"Users"},
     *      summary="Update detail of user object",
     *      security={ {"bearer": {} }},
     *      description="Update user detail object",
     *      @OA\RequestBody(
     *          required=true,
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="user name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="user email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="password",
     *                                      type="string",
     *                                      description="user password"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="role",
     *                                      type="string",
     *                                      description="user role"
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
     *                                @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="user id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="user name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="user email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email_verified_at",
     *                                      type="string",
     *                                      description="user email verified timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="user creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="user updated timestamp"
     *                                  )
     *                 )
     *             )
     *         }
     *       ),
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
    public function update(Request $request,User $user)
    {
        $user->update($request->all());
            return response()->json($user,200);
    }

    /**
     * @OA\Delete(
     *      path="/users/{id}",
     *      operationId="Delete user object ",
     *      tags={"Users"},
     *      summary="Delete user object",
     *      security={ {"bearer": {} }},
     *      description="Delete user object",
     *      @OA\Parameter(
     *          name="id",
     *          description="user id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                                @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      description="user id"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="name",
     *                                      type="string",
     *                                      description="user name"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email",
     *                                      type="string",
     *                                      description="user email"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="email_verified_at",
     *                                      type="string",
     *                                      description="user email verified timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="created_at",
     *                                      type="string",
     *                                      description="user creation timestamp"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="updated_at",
     *                                      type="string",
     *                                      description="user updated timestamp"
     *                                  )
     *                 )
     *             )
     *         }
     *       ),
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
    public function destroy(User $user)
    {
        $user -> delete();
            return response()->json(null, 204);
    }
}
