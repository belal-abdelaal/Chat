<?php

namespace Modules\User\Http\Controllers;

use Modules\User\Http\Requests\UserLoginRequest;
use Modules\User\Http\Requests\UserSignupRequest;
use Modules\User\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Models\User;
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
}
