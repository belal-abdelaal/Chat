<?php

namespace Modules\User\Http\Controllers;

use Modules\User\Http\Requests\UserLoginRequest;
use Modules\User\Http\Requests\UserSignupRequest;
use Modules\User\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Http\Requests\UserUpdateRequest;
use Modules\User\Transformers\UserResource;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a new user.
     *
     * @param UserSignupRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(UserSignupRequest $request)
    {
        $data = $this->userService->validate($request);
        if ($this->userService->create($data)) {
            return response()->json(["message" => "Account created successfuly"], 201);
        }
        return response()->json(["message" => "Email already exists"], 422);
    }

    /**
     * Authenticate a user and issue a token.
     *
     * @param UserLoginRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function login(UserLoginRequest $request)
    {
        $data = $this->userService->validate($request);
        if ($token = $this->userService->login($data))
            return response()->json(["token" => $token->plainTextToken]);
        return response()->json(["message" => "Invalid email or password"]);
    }

    /**
     * Get the authenticated user from the token.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        if (!$user = $this->userService->parseToken($request->header("token"))) {
            return response()->json([
                "message" => "Invalid or expired token"
            ], 401);
        }
        return response()->json(new UserResource($user));
    }

    /**
     * Responsible for updating accound data
     * @param UserUpdateRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(UserUpdateRequest $request)
    {
        $newData = $this->userService->validate($request);
        $updatedUser = $this->userService->update(
            $newData,
            $this->userService->parseToken($request->header("token"))
        );
        if ($updatedUser)
            return response()->json([
                "message" => "Account updated successfuly",
                "user" => new UserResource($updatedUser)
            ]);
        else if ($updatedUser == null)
            return response()->json(["message" => "Empty request !"], 422);
        else
            return response()->json(["message" => "Internal server error !"], 500);
    }
}
