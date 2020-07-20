<?php

namespace App\Http\Middleware;

use App\SimpleResponse;
use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


class VisitAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (empty($user)) {
            abort(401);
        }


        if ($this->hasPermission($request, $user)) {
            return $next($request);
        } else {
            abort(403);
        }
    }


    protected function hasPermission(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) { // 超级账号有所以的权限
            return true;
        }

        $routeInfo = get_route_information(Route::current());

        $uri = $routeInfo['method'] . ':' . $routeInfo['uri'];

        return false;
    }


    protected function getPermissions(User $user) {

    }
}
