<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Repositories\UserRepository;

class IsValidToken
{
    private $userRepo;
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->userRepo->isValidToken($request->header('token')))
            return response()->json([
                "message" => "Unathenticated request !"
            ], 401);

        return $next($request);
    }
}
