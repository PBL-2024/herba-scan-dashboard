<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;
use Validator;

class AuthController extends BaseController
{
    protected $otpService;
    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }
    /**
     * User Login
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
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * User Register
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
            return $this->sendError('Validation Error.', ['error' => 'Email already exists'], 400);
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
     * Google callback for login and register
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function googleCallback(Request $request)
    {
        // Check if the user already exists
        $existingUser = User::where('google_id', $request->id)
            ->orWhere('email', $request->email)
            ->first();

        if ($existingUser) {
            // If the user exists, log them in without updating their information
            Auth::login($existingUser);

            $user = Auth::user();
            $success['token'] = $user->createToken('api-token')->plainTextToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success, 'User login successfully.');
        }

        // If the user does not exist, create a new user
        $user = User::create([
            'google_id' => $request->id,
            'name' => $request->displayName,
            'email' => $request->email,
            'password' => Str::random(12),
            'email_verified_at' => now(),
            'image_url' => $request->photoUrl,
        ]);

        $this->assignUserRole($user, 'user');

        Auth::login($user);

        $user = Auth::user();
        $success['token'] = $user->createToken('api-token')->plainTextToken;
        $success['name'] = $user->name;
        return $this->sendResponse($success, 'User login successfully.');
    }

    /**
     * Logout user (Revoke the token)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'User logged out successfully.');
    }

    /**
     * Send OTP to user email
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|mixed
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 409);
        }

        if (!User::where('email', $request->email)->exists()) {
            return $this->sendError('Validation Error.', ['error' => 'Email tidak terdaftar'], 409);
        }

        $this->otpService->generateOTP($request->email);
        return $this->sendResponse([], 'OTP berhasil dikirim, cek email anda.');
    }

    /**
     * Send OTP to authenticated user email to change password
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function sendOTPAuthenticatedUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 409);
        }
        
        // check email is match with authenticated user
        if ($request->user()->email != $request->email) {
            return $this->sendError('Validation Error.', ['error' => 'Email tidak sesuai dengan user yang sedang login'], 409);
        }

        $this->otpService->generateOTP($request->email);
        return $this->sendResponse([], 'OTP berhasil dikirim, cek email anda.');
    }
    
    /**
     * Send OTP to user email for SignUp
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|mixed
     */
    public function sendOTPSignUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 409);
        }

        $this->otpService->generateOTP($request->email);
        return $this->sendResponse([], 'OTP berhasil dikirim, cek email anda.');
    }

    /**
     * Verify OTP
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|mixed
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 409);
        }

        $isValid = $this->otpService->verifyOTP($request->email, $request->otp);

        if ($isValid) {
            return $this->sendResponse([], 'OTP valid.');
        } else {
            return $this->sendError('Validation Error.', ['error' => 'OTP tidak valid'], 409);
        }
    }

    /**
     * Change user password
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 409);
        }

        $token = $request->input('token');
        $userToken = $this->otpService->getTokenByEmail($request->email);

        if ($userToken != $token) {
            return $this->sendError('Validation Error', 'Token tidak valid', 409);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('Validation Error.', ['error' => 'Email tidak terdaftar'], 409);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return $this->sendResponse([], 'Password berhasil diubah.');
    }
}
