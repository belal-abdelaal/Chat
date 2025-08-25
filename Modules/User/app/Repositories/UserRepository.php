<?php

namespace Modules\User\Repositories;

use Laravel\Sanctum\PersonalAccessToken;
use Modules\User\Models\User;

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

    public function findUserById($id)
    {
        return User::where('id', $id)->first();
    }

    public function issueToken(User $user, $abilities = [])
    {
        return $user->createToken($user->name . '\'s_token', $abilities, now()->addWeek());
    }

    public function isValidToken($token): null|string
    {
        if (empty($token) || !PersonalAccessToken::findToken($token))
            return null;
        return $token;
    }

    public function createUser($data)
    {
        return $this->userModel->create($data);
    }
}
