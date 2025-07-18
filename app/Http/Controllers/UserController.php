<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSignupRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

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
}
