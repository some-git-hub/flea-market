<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\DeliveryAddress;

class DeliveryAddressTest extends TestCase
{
    /**
     * @test
     *
     *  配達先変更ページで登録された配達先が商品購入ページに反映される
     */
    public function checkout_page_reflects_registered_delivery_address()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();
        DeliveryAddress::create([
            'postal_code' => '111-1111',
            'address' => '東京都',
            'building' => 'テストビル111',
        ]);

        $this->put(route('address.update', $item->id), [
            'postal_code' => '222-2222',
            'address' => '東京都',
            'building' => 'テストビル222',
        ])->assertRedirect();

        $response = $this->get(route('purchase.checkout', $item->id))->assertStatus(200);

        $response->assertSee('〒222-2222');
        $response->assertSee('東京都');
        $response->assertSee('テストビル222');
    }

    /**
     * @test
     *
     *  登録された配達先が購入した商品に紐づく
     */
    public function delivery_address_is_linked_to__purchased_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();
        $deliveryAddress = DeliveryAddress::create([
            'postal_code' => '111-1111',
            'address' => '東京都',
            'building' => 'テストビル111',
        ]);

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'delivery_address_id' => $deliveryAddress->id,
            'price' => $item->price,
            'payment_method' => 2,
        ]);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'user_id' => $user->id,
            'item_id' => $item->id,
            'delivery_address_id' => $deliveryAddress->id,
            'price' => $item->price,
            'payment_method' => 2,
        ]);

        $this->assertEquals('111-1111', $purchase->deliveryAddress->postal_code);
        $this->assertEquals('東京都', $purchase->deliveryAddress->address);
        $this->assertEquals('テストビル111', $purchase->deliveryAddress->building);
    }
}
