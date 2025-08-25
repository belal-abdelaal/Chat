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

    /**
     * Check if an email is unique (not registered).
     *
     * @param string $email
     * @return bool True if unique, false otherwise
     */
    public function isUniqueEmail(string $email): bool
    {
        return $this->userModel->where('email', $email)->doesntExist();
    }

    /**
     * Find a user by email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findUserById($id)
    {
        return User::where('id', $id)->first();
    }

    /**
     * Issue a Laravel Sanctum personal access token for a user.
     *
     * @param User $user
     * @param array $abilities
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function issueToken(User $user, $abilities = [])
    {
        return $user->createToken($user->name . '\'s_token', $abilities, now()->addWeek());
    }

    /**
     * Check if a given personal access token is valid.
     *
     * @param string $token
     * @return string|null The token if valid, null otherwise
     */
    public function isValidToken($token): null|string
    {
        if (empty($token) || !PersonalAccessToken::findToken($token))
            return null;
        return $token;
    }

    /**
     * Create a new user record.
     *
     * @param array $data
     * @return User
     */
    public function createUser($data)
    {
        return $this->userModel->create($data);
    }
}
