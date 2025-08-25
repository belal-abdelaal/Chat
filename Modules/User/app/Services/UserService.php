<?php

namespace Modules\User\Services;

use Modules\User\Http\Requests\UserLoginRequest;
use Modules\User\Http\Requests\UserSignupRequest;
use Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Class UserService
 *
 * Handles business logic for user-related operations.
 *
 * @package Modules\User\Services
 */
class UserService
{
    /**
     * Repository for user database operations.
     *
     * @var UserRepository
     */
    protected $userRepo;


    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Validate signup or login request data.
     *
     * @param UserSignupRequest|UserLoginRequest $request
     * @return array Validated request data
     */
    public function validate(UserSignupRequest|UserLoginRequest $request)
    {
        $data = $request->validated();
        return $data;
    }

    /**
     * Parse a personal access token and return the corresponding user.
     *
     * @param string $token
     * @return mixed|null The authenticated user or null if invalid
     */
    public function parseToken($token)
    {
        if (!$this->userRepo->isValidToken($token))
            return null;
        $token = PersonalAccessToken::findToken($token);
        return $token->tokenable;
    }

    /**
     * Create a new user if the email is unique.
     *
     * @param array $data
     * @return mixed|null The created user or null if email already exists
     */
    public function create($data)
    {
        if ($this->userRepo->isUniqueEmail($data['email'])) {
            return $this->userRepo->createUser($data);
        }
        return null;
    }

    /**
     * Attempt to log in a user using provided credentials.
     *
     * @param array $data
     * @return mixed|null Personal access token or null if authentication fails
     */
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
