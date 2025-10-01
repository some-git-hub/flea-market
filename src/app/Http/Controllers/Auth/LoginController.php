<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    /**
     *  ログイン画面の表示
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }


    /**
     *  ログイン処理
     */
    public function store(LoginRequest $request)
    {
        $credentials = $request->validated();

        // 入力された認証情報でログイン処理
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('items.index'));
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
