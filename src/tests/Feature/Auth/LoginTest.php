<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;


class LoginTest extends TestCase
{
    /**
     * @test
     *
     *  メールアドレスが入力されていない場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_email_is_empty()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * @test
     *
     *  パスワードが入力されていない場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_password_is_empty()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * @test
     *
     *  入力情報が登録されていない場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_credentials_are_invalid()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }

    /**
     * @test
     *
     *  正しい情報が入力された場合はログイン処理が実行される
     */
    public function it_logs_in_user_when_credentials_are_valid()
    {
        $user = User::where('email', 'test@example.com')->first();

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect('/');
    }

    /**
     * @test
     *
     *  ログアウト処理を実行できる
     */
    public function it_logs_out_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
