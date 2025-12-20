<?php

namespace Tests\Feature\Auth;


use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\User;
use Tests\TestCase;

class VerifyTest extends TestCase
{
    /**
     * @test
     *
     *  ユーザー登録時に認証メールが送信される
     */
    public function user_registration_sends_verification_email()
    {
        Notification::fake();

        $email = 'user'.time().'@example.com';
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(302);

        $user = User::where('email', $email)->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * @test
     *
     *  「認証はこちらから」を押すと認証サイトに遷移する
     */
    public function verification_link_redirects_to_mailhog()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $this->actingAs($user);

        $response = $this->get('/email/verify')->assertStatus(200);

        // DOMを解析
        $crawler = new Crawler($response->getContent());

        $link = $crawler->filter('.verify-email__link-verification')->link();

        $this->assertEquals('http://localhost:8025/', $link->getUri());
    }

    /**
     * @test
     *
     *  メール認証を完了するとプロフィール編集画面に遷移する
     */
    public function email_verification_redirects_to_profile_edit()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('mypage.edit'));
    }
}
