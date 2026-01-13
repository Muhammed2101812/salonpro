<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Yetkilendirme gerekli.'], 401);
        }

        // Super Admin and Org Admin can access all branches
        if ($user->hasAnyRole(['Super Admin', 'Organization Admin'])) {
            return $next($request);
        }

        // Check if user has branch_id
        if (! $user->branch_id) {
            return response()->json([
                'message' => 'Kullanıcıya şube atanmamış.',
            ], 403);
        }

        // For route model binding, check if the model belongs to user's branch
        $route = $request->route();

        if ($route) {
            $routeParameters = $route->parameters();

            foreach ($routeParameters as $parameter) {
                if (is_object($parameter) && method_exists($parameter, 'getAttribute')) {
                    $branchId = $parameter->getAttribute('branch_id');

                    if ($branchId && $branchId !== $user->branch_id) {
                        return response()->json([
                            'message' => 'Bu kaynağa erişim yetkiniz bulunmamaktadır.',
                        ], 403);
                    }
                }
            }
        }

        return $next($request);
    }
}
