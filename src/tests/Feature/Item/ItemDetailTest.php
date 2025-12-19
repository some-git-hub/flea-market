<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;
use App\Models\Comment;


class ItemDetailTest extends TestCase
{
    /**
     * @test
     *
     *  すべての情報が商品詳細ページに表示される
     */
    public function item_detail_page_displays_all_information()
    {
        $user = User::factory()->create();

        $item = Item::where('name', '腕時計')->with('categories')->firstOrFail();
        $categoryNames = $item->categories->pluck('name')->toArray();

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $comment = Comment::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'テストコメントです',
        ]);

        $response = $this->get(route('items.show', $item->id))->assertStatus(200);

        $response->assertSee('<img', false);
        $response->assertSee($item->item_image, false);

        $response->assertSee($item->name);
        $response->assertSee($item->brand);
        $response->assertSee(number_format($item->price));

        $response->assertSee($item->favorites->count());
        $response->assertSee($item->comments->count());

        $response->assertSee($item->description);

        foreach ($categoryNames as $categoryName) {
            $response->assertSee($categoryName);
        }

        $response->assertSee($item->condition_label);

        $response->assertSee($comment->user->name);
        $response->assertSee($comment->content);
    }

    /**
     * @test
     *
     *  複数選択されたカテゴリーのすべてが商品詳細ページに表示される
     */
    public function item_detail_page_displays_all_related_categories()
    {
        $item = Item::where('name', '腕時計')->with('categories')->firstOrFail();
        $categoryNames = $item->categories->pluck('name')->toArray();

        $response = $this->get(route('items.show', $item->id))->assertStatus(200);

        foreach ($categoryNames as $categoryName) {
            $response->assertSee($categoryName);
        }
    }
}
