<?php

namespace App\Services;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserSignupRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function validate(UserSignupRequest|UserLoginRequest $request)
    {
        $data = $request->validated();
        return $data;
    }

    public function create($data)
    {
        if ($this->userRepo->isUniqueEmail($data['email'])) {
            return $this->userRepo->createUser($data);
        }
        return null;
    }

    public function login($data)
    {
        if (!$this->userRepo->isUniqueEmail($data['email'])) {
            $user = $this->userRepo->findUserByEmail($data['email']);
            if (!Hash::check($data['password'], $user->password)) 
                return null;
            return $this->userRepo->issueToken($user);
        }
        return null;
    }
}
