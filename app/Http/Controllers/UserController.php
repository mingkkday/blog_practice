<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Services\MailService;
use App\Http\Services\UserService;
use App\Http\Requests\UserAuthenticateRequest;
use App\Http\Requests\UserStoreRequest;

class UserController extends Controller
{
    public function __construct(
        private UserService $service,
        private MailService $mailService
    ) {
    }

    public function check()
    {
        return response()->json([], 200);
    }

    public function store(UserStoreRequest $request)
    {
        $formFields = $request->validated();
        $formFields['password'] = bcrypt($formFields['password']);
        $user = User::create($formFields);

        $this->mailService->sendVerifyEmail($formFields['email']);
        return response()->json([], 204);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([], 204);
    }

    public function authenticate(UserAuthenticateRequest $request)
    {
        $formFields = $request->validated();
        if (!auth()->attempt($formFields)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        if (auth()->user()->email_verified_at === null) {
            return response()->json(['message' => 'Unverified email.'], 401);
        }

        $request->session()->regenerate();
        return response()->json([], 204);
    }

    public function verify(string $verifyCode)
    {
        $this->service->verifyUser($verifyCode);
        return response()->json([], 204);
    }
}
