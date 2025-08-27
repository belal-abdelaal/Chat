<?php

namespace Modules\User\Services;

use Modules\User\Http\Requests\UserLoginRequest;
use Modules\User\Http\Requests\UserSignupRequest;
use Modules\User\Http\Requests\UserUpdateRequest;
use Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\QueryException;
use Modules\User\Models\User;

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
     * @param UserSignupRequest|UserLoginRequest|UserUpdateRequest $request
     * @return array Validated request data
     */
    public function validate(UserSignupRequest|UserLoginRequest|UserUpdateRequest $request)
    {
        $data = $request->validated();
        return $data ? $data : null;
    }

    /**
     * Parse a personal access token and return the corresponding user.
     *
     * @param string $token
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo 
     */
    public function parseToken($token)
    {
        $token = PersonalAccessToken::findToken($token);
        return $token->tokenable();
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

    /**
     * Attend to update a user and return a User ORM instance, QueryException 
     * if an error accoured, or null if the provided data is empty
     * @param array|null $data The replacement data for the account's old data
     * @param User $user The user being updated
     * @return User|QueryException|null 
     */
    public function update(array|null $data, $user): User|QueryException|null
    {
        if ($data)
            try {
                $user->update($data);
                return $user;
            } catch (QueryException $e) {
                return $e;
            }
        else
            return null;
    }
}
