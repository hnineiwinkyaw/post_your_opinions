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

class UserController extends Controller
{
    public $user;
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,User $user)
    {
        $user->update($request->all());
            return response()->json($user,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user -> delete();
            return response()->json(null, 204);
    }
}
