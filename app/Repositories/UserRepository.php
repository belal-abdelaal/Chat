<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function isUniqueEmail(string $email): bool
    {
        return $this->userModel->where('email', $email)->doesntExist();
    }

    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function issueToken(User $user, $abilities = [])
    {
        return $user->createToken($user->name . '\'s_token', $abilities, now()->addWeek());
    }

    public function createUser($data)
    {
        return $this->userModel->create($data);
    }
}
