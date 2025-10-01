<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyController extends Controller
{
    /**
     *  メール認証誘導画面の表示
     */
    public function show()
    {
        $user = auth()->user();

        return view('auth.verify-email');
    }


    /**
     *  メール認証処理
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('mypage.edit');
    }


    /**
     *  メール認証用通知の再送信
     */
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
