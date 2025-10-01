<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\DeliveryAddress;
use Mockery;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\App;

class PurchaseTest extends TestCase
{
    /**
     * @test
     *
     *  「購入する」を押すとstripeの決済画面に遷移する
     */
    public function purchase_store_redirects_to_stripe_checkout_session()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();

        // Stripe Checkout Session をモック
        \Mockery::mock('alias:Stripe\Checkout\Session')
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'id'  => 'cs_test_123',
                'url' => 'https://example.com/checkout',
            ]);

        // リクエスト送信
        $response = $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 2,
            'postal_code'    => '000-0000',
            'address'        => '東京都',
            'building'       => 'テストビル',
        ]);

        // リダイレクト確認
        $response->assertStatus(302);
        $response->assertRedirect('https://example.com/checkout');
    }

    /**
     * @test
     *
     *  商品を購入すると商品一覧ページで Sold ラベルが表示される
     */
    public function purchased_items_show_sold_label_in_index_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();
        $deliveryAddress = DeliveryAddress::create([
            'postal_code' => '000-0000',
            'address' => '東京都',
            'building' => 'テストビル',
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'delivery_address_id' => $deliveryAddress->id,
            'price' => $item->price,
            'payment_method' => 2,
        ]);

        $item->update(['status' => 1]);

        $response = $this->get(route('items.index'))->assertStatus(200);

        $response->assertSee('Sold');
    }

    /**
     * @test
     *
     *  購入した商品がマイページに表示される
     */
    public function purchased_item_is_displayed_on_mypage()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::firstOrFail();
        $deliveryAddress = DeliveryAddress::create([
            'postal_code' => '000-0000',
            'address' => '東京都',
            'building' => 'テストビル',
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'delivery_address_id' => $deliveryAddress->id,
            'price' => $item->price,
            'payment_method' => 2,
        ]);

        $item->update([
            'buyer_id' => $user->id,
            'status' => 1,
        ]);

        $response = $this->get('/mypage?page=buy')->assertStatus(200);

        $response->assertSeeText($item->name);
    }
}
