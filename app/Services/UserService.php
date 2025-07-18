<?php

namespace App\Services;

use App\Http\Requests\UserSignupRequest;
use App\Repositories\UserRepository;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function validate(UserSignupRequest $request)
    {
        $data = $request->only(['name', 'email', 'passwrd', 'gender']);
        return $data;
    }

    public function create($data)
    {
        if ($this->userRepo->isUniqueEmail($data['email'])) {
            return $this->userRepo->createUser($data);
        }
        return null;
    }
}