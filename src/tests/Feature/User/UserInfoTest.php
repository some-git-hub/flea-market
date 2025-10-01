<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\UserAddress;
use App\Models\DeliveryAddress;


class UserInfoTest extends TestCase
{
    /**
     * @test
     *
     *  プロフィールページではユーザー情報と出品または購入した商品一覧が表示される
     */
    public function profile_page_displays_user_info_and_items()
    {
        $user = User::where('name', 'テストユーザー')->firstOrFail();
        $this->actingAs($user);

        $user->update([
            'profile_image' => 'images/test.png',
        ]);

        $createdItem1 = Item::where('user_id', $user->id)->firstOrFail();
        $createdItem2 = Item::where('user_id', $user->id)->skip(1)->firstOrFail();

        $otherUser = User::where('name', '他のユーザー')->firstOrFail();

        $purchasedItem1 = Item::where('user_id', $otherUser->id)->firstOrFail();
        $purchasedItem2 = Item::where('user_id', $otherUser->id)->skip(1)->firstOrFail();

        $purchasedItem1->update(['buyer_id' => $user->id, 'status' => 1]);
        $purchasedItem2->update(['buyer_id' => $user->id, 'status' => 1]);

        $deliveryAddress = DeliveryAddress::create([
            'postal_code' => '111-1111',
            'address' => '東京都',
            'building' => 'テストビル111',
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem1->id,
            'delivery_address_id' => $deliveryAddress->id,
            'price' => $purchasedItem1->price,
            'payment_method' => 1,
        ]);
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem2->id,
            'delivery_address_id' => $deliveryAddress->id,
            'price' => $purchasedItem2->price,
            'payment_method' => 2,
        ]);

        $response = $this->get('/mypage?page=sell')->assertStatus(200);

        $response->assertSee('テストユーザー');
        $response->assertSee('/storage/images/test.png');

        $response->assertSee($createdItem1->name);
        $response->assertSee($createdItem2->name);

        $response = $this->get('/mypage?page=buy')->assertStatus(200);

        $response->assertSee($purchasedItem1->name);
        $response->assertSee($purchasedItem2->name);
    }

    /**
     * @test
     *
     *  プロフィールページには初期値が入力されている
     */
    public function profile_edit_page_displays_initial_values()
    {
        $user = User::factory()->create([
            'profile_image' => 'images/test.png',
            'name' => 'テストユーザー',
        ]);

        $userAddress = UserAddress::create([
            'user_id' => $user->id,
            'postal_code' => '111-1111',
            'address' => '東京都',
            'building' => 'テストビル111',
        ]);

        $response = $this->actingAs($user)->get(route('mypage.edit'))->assertStatus(200);

        $response->assertSee('<img', false);
        $response->assertSee('/storage/images/test.png', false);

        $response->assertSee('value="テストユーザー"', false);
        $response->assertSee('value="111-1111"', false);
        $response->assertSee('value="東京都"', false);
        $response->assertSee('value="テストビル111"', false);
    }
}
