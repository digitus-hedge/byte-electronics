<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Laravel\Fortify\Http\Requests\VerifyEmailRequest;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    public function verify(VerifyEmailRequest $request)
    {
        \Log::info('Verify method called', ['user_id' => $request->user()->id]);

        if ($request->user()->hasVerifiedEmail()) {
            \Log::info('Email already verified', ['user_id' => $request->user()->id]);
            return redirect()->route('dashboard')->with('success', 'Email already verified.');
        }

        if ($request->user()->markEmailAsVerified()) {
            \Log::info('Email verified', ['user_id' => $request->user()->id]);
            event(new Verified($request->user()));
        }

        return redirect()->route('dashboard')->with('success', 'Your email has been verified successfully.');
    }

    public function send(Request $request)
    {
        \Log::info('Send method called', ['user_id' => $request->user()->id]);

        if ($request->user()->hasVerifiedEmail()) {
            \Log::info('Email already verified', ['user_id' => $request->user()->id]);
            return redirect()->route('dashboard')->with('success', 'Email already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        \Log::info('Verification email sent', ['user_id' => $request->user()->id]);
        return back()->with('status', 'verification-link-sent');
    }

    public function show()
    {
        \Log::info('Show method called');
        return inertia('Auth/VerifyEmail', ['status' => session('status')]);
    }
}