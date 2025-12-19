<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Favorite;

class ItemIndexTest extends TestCase
{
    /**
     * @test
     *
     *  すべての商品が商品一覧ページに表示される
     */
    public function index_page_displays_all_items()
    {
        $response = $this->get(route('items.index'))->assertStatus(200);

        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /**
     * @test
     *
     *  商品一覧ページの購入済み商品には Sold ラベルが表示される
     */
    public function purchased_items_show_sold_label_in_index_page()
    {
        $item = Item::firstOrFail();
        $item->update(['status' => 2]);

        $response = $this->get(route('items.index'))->assertStatus(200);

        $response->assertSee('Sold');
    }

    /**
     * @test
     *
     *  自分が出品した商品は商品一覧ページに表示されない
     */
    public function user_cannot_see_their_own_items_in_index_page()
    {
        $user = User::where('email', 'test1@example.com')->firstOrFail();
        $otherUser = User::where('email', 'test2@example.com')->firstOrFail();

        $myItem = Item::where('user_id', $user->id)->firstOrFail();
        $otherItem = Item::where('user_id', $otherUser->id)->firstOrFail();

        $response = $this->actingAs($user)->get(route('items.index'))->assertStatus(200);

        $response->assertDontSee($myItem->name);
        $response->assertSee($otherItem->name);
    }

    /**
     * @test
     *
     *  いいねした商品がマイリストに表示される
     */
    public function mylist_page_displays_only_favorite_items()
    {
        $user = User::factory()->create();

        $item1 = Item::firstOrFail();
        $item2 = Item::skip(1)->firstOrFail();
        $item3 = Item::skip(2)->firstOrFail();

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);
        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item2->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist')->assertStatus(200);

        $response->assertSee($item1->name);
        $response->assertSee($item2->name);
        $response->assertDontSee($item3->name);
    }

    /**
     * @test
     *
     *  マイリストの購入済み商品には Sold ラベルが表示される
     */
    public function purchased_items_show_sold_label_in_mylist_page()
    {
        $user = User::factory()->create();

        $item = Item::firstOrFail();
        $item->update(['status' => 2]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist')->assertStatus(200);

        $response->assertSee($item->name);
        $response->assertSee('Sold');
    }

    /**
     * @test
     *
     *  未認証ユーザーはマイリストに何も表示されない
     */
    public function guest_sees_no_items_in_mylist_page()
    {
        $item1 = Item::firstOrFail();
        $item2 = Item::skip(1)->firstOrFail();

        $response = $this->get('/?tab=mylist')->assertStatus(200);

        $response->assertDontSee($item1->name);
        $response->assertDontSee($item2->name);
    }

    /**
     * @test
     *
     *  「商品名」で部分一致検索できる
     */
    public function it_can_search_items_by_partial_name()
    {
        $user = User::factory()->create();

        $item1 = Item::where('name', '腕時計')->firstOrFail();
        $item2 = Item::where('name', 'マイク')->firstOrFail();
        $item3 = Item::where('name', 'ショルダーバッグ')->firstOrFail();

        $response = $this->actingAs($user)->get('/?keyword=バッグ')->assertStatus(200);

        $response->assertDontSee($item1->name);
        $response->assertDontSee($item2->name);
        $response->assertSee($item3->name);
    }

    /**
     * @test
     *
     *  検索キーワードがマイリストでも保持されている
     */
    public function search_keyword_is_preserved_on_mylist_page()
    {
        $user = User::factory()->create();

        $item1 = Item::where('name', '腕時計')->firstOrFail();
        $item2 = Item::where('name', 'マイク')->firstOrFail();
        $item3 = Item::where('name', 'ショルダーバッグ')->firstOrFail();

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);
        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item2->id,
        ]);
        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item3->id,
        ]);

        $response1 = $this->actingAs($user)->get('/?keyword=バッグ')->assertStatus(200);

        $response1->assertDontSee($item1->name);
        $response1->assertDontSee($item2->name);
        $response1->assertSee($item3->name);

        $response2 = $this->actingAs($user)->get('/?tab=mylist&keyword=バッグ')->assertStatus(200);

        $response2->assertDontSee($item1->name);
        $response2->assertDontSee($item2->name);
        $response2->assertSee($item3->name);
    }
}