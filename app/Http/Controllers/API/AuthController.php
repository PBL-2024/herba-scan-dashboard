<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Str;
use Validator;

class AuthController extends BaseController
{
    /**
     * Login api
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('api-token')->plainTextToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 409);
        }

        $input = $request->all();
        if (User::where('email', $input['email'])->exists()) {
            return $this->sendError('Validation Error.', ['error' => 'Email already exists']);
        }
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        // Berikan role 'user' secara otomatis
        $this->assignUserRole($user, 'user');
        $success['token'] = $user->createToken('api-token')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Summary of googleCallback
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function googleCallback(Request $request)
    {
        $user = User::updateOrCreate(
            ['google_id' => $request->id],
            [
                'name' => $request->displayName,
                'email' => $request->email,
                'password' => Str::random(12),
                'email_verified_at' => now(),
                'image_url' => $request->photoUrl,
            ]
        );

        $this->assignUserRole($user, 'user');

        Auth::login($user);

        $user = Auth::user();
        $success['token'] = $user->createToken('api-token')->plainTextToken;
        $success['name'] = $user->name;
        return $this->sendResponse($success, 'User login successfully.');
    }

    /**
     * logout api
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'User logged out successfully.');
    }
}
