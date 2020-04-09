<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user', [
            'except' => [
                'store',
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\UserCollection
     */
    public function index()
    {
        $user = Auth::guard('api')->user();

        if ($user->isAdmin()) {
            $users = User::notAdmin()
                ->where('id', '!=', $user->id)
                ->get();
        } else {
            $users = User::select('id', 'first_name', 'last_name', 'role')
                ->where('role', 'admin')
                ->get();
        }

        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserStoreRequest  $request
     * @return \App\Http\Resources\UserResource
     */
    public function store(UserStoreRequest $request)
    {
        $user = new User();
        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->job = $request->job;
        $user->last_name = $request->last_name;
        $user->password = $request->password;
        $user->phone = $request->phone;
        $user->role = 'user';
        $user->save();

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \App\Http\Resources\UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserUpdateRequest  $request
     * @param  \App\User  $user
     * @return \App\Http\Resources\UserResource
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->fill($request->validated());
        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([], 204);
    }
}
