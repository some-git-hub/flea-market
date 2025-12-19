<?php

namespace Tests\Feature\Item;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;

class ItemStoreTest extends TestCase
{
    /**
     * @test
     *
     *  商品出品ページで商品の情報を保存できる
     */
    public function user_can_create_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $categories = Category::all()->random(3);

        $response = $this->post(route('items.store'), [
            'user_id' => $user->id,
            'item_image' => UploadedFile::fake()->create('test.png', 100, 'image/png'),
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => '説明文',
            'price' => 1000,
            'category' => $categories->pluck('id')->toArray(),
            'condition' => 1,
            'status' => 0,
        ])->assertRedirect();

        $item = Item::where('name', 'テスト商品')->firstOrFail();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => '説明文',
            'price' => 1000,
            'condition' => 1,
            'status' => 0,
        ]);

        Storage::disk('public')->assertExists($item->item_image);

        foreach ($categories as $category) {
            $this->assertDatabaseHas('item_category', [
                'item_id' => $item->id,
                'category_id' => $category->id,
            ]);
        }
    }
}
