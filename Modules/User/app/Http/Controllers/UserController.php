<?php

namespace Modules\User\Http\Controllers;

use Modules\User\Http\Requests\UserLoginRequest;
use Modules\User\Http\Requests\UserSignupRequest;
use Modules\User\Services\UserService;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create(UserSignupRequest $request)
    {
        $data = $this->userService->validate($request);
        if ($this->userService->create($data)) {
            return response()->json(["message" => "Account created successfuly"], 201);
        }
        return response()->json(["message" => "Email already exists"], 422);
    }
    public function login(UserLoginRequest $request)
    {
        $data = $this->userService->validate($request);
        if ($token = $this->userService->login($data))
            return response()->json(["token" => $token->plainTextToken]);
        return response()->json(["message" => "Invalid email or password"]);
    }
}
