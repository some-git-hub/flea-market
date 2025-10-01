<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * @test
     *
     *  名前が入力されていない場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_name_is_empty()
    {
        $email = 'user'.time().'@example.com';
        $response = $this->post('/register', [
            'name' => '',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /**
     * @test
     *
     *  メールアドレスが入力されていない場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_email_is_empty()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
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
        $email = 'user'.time().'@example.com';
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => $email,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * @test
     *
     *  パスワードが7文字以下の場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_password_is_too_short()
    {
        $email = 'user'.time().'@example.com';
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => $email,
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * @test
     *
     *  パスワードが確認用と一致しない場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_password_confirmation_does_not_match()
    {
        $email = 'user'.time().'@example.com';
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません']);
    }

    /**
     * @test
     *
     *  全て正しく入力するとユーザーが登録されプロフィール設定画面に遷移する
     */
    public function it_registers_user_and_redirects_to_profile_page_when_valid()
    {
        $email = 'user'.time().'@example.com';
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);

        $response->assertRedirect('/email/verify');
    }
}
