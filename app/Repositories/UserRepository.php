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

    public function createUser($data)
    {
        return $this->userModel->create($data);
    }
}