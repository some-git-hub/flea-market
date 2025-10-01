<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemCommentTest extends TestCase
{
    /**
     * @test
     *
     *  ログイン済みのユーザーはコメントを送信できる
     */
    public function user_can_submit_comments()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();

        $response = $this->post(route('comment.store', $item->id), [
            'content' => 'テストコメントです',
        ])->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * @test
     *
     *  未認証ユーザーはコメントを送信できない
     */
    public function guest_cannot_submit_a_comment()
    {
        $item = Item::firstOrFail();

        $response = $this->post(route('comment.store', $item->id), [
            'content' => 'テストコメントです',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'テストコメントです',
        ]);
    }

    /**
     * @test
     *
     *  コメントが入力されていない場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_comment_is_empty()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();

        $response = $this->postJson(route('comment.store', $item->id), [
            'content' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     *
     *  コメントが255文字以上の場合はエラーメッセージが表示される
     */
    public function it_shows_error_when_comment_is_too_long()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();

        $longComment = str_repeat('あ', 256);

        $response = $this->postJson(route('comment.store', $item->id), [
            'content' => $longComment,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);
    }
}
