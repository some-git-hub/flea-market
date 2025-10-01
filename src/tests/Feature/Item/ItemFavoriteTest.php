<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemFavoriteTest extends TestCase
{
    /**
     * @test
     *
     *  ログイン済みのユーザーは商品をいいね登録でき、いいね合計値は増加表示される
     */
    public function user_can_favorite_an_item_and_favorite_count_increases()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();

        $response = $this->post(route('favorite.toggle', $item->id))->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(1, $item->favorites()->count());
    }

    /**
     * @test
     *
     *  商品をいいね登録すると、いいねアイコンの色が変わる
     */
    public function favorite_icon_changes_color_when_user_favorites_an_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();

        $response = $this->get(route('items.show', $item->id))->assertStatus(200);

        $response->assertSee('<img', false);
        $response->assertSee('storage/images/favorite_logo.png', false);

        $response = $this->post(route('favorite.toggle', $item->id))->assertStatus(200);

        $response = $this->get(route('items.show', $item->id))->assertStatus(200);

        $response->assertSee('<img', false);
        $response->assertSee('storage/images/favorite_logo_active.png', false);
    }

    /**
     * @test
     *
     *  いいねアイコンを再度押すと、いいねを解除できる
     */
    public function user_can_remove_a_favorite_by_pressing_favorite_icon_again()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();

        $response = $this->post(route('favorite.toggle', $item->id))->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->post(route('favorite.toggle', $item->id))->assertStatus(200);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('items.show', $item->id))->assertStatus(200);

        $response->assertSee('<img', false);
        $response->assertSee('storage/images/favorite_logo.png', false);
    }
}
