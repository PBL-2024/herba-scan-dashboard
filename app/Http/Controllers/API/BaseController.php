<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Assign a role to a user.
     * @param \App\Models\User $user
     * @param string $role
     * @return void
     */
    public function assignUserRole(User $user, string $role): void
    {
        $user->assignRole($role);
    }

    /**
     * Check if a user has a role or permission.
     * @param \App\Models\User $user
     * @param array $roles
     * @param array $permissions
     * @return bool
     */
    public function checkPermission(Authenticatable $user, array $roles, array $permissions): bool
    {
        $hasRole = false;
        $hasPermission = false;

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermissionTo($permission)) {
                $hasPermission = true;
                break;
            }
        }

        return $hasRole && $hasPermission;
    }
}
