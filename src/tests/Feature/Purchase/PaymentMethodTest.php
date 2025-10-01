<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class PaymentMethodTest extends TestCase
{
    /**
     * @test
     *
     *  選択した支払い方法が購入ページに反映される
      */
    public function checkout_page_reflects_selected_payment_method()
    {
        $user = User::factory()->create();

        $item = Item::firstOrFail();

        $this->actingAs($user)->withSession([
            "checkout_payment_method_{$item->id}" => 1,
        ]);

        $response = $this->get(route('purchase.checkout', $item->id))->assertStatus(200);

        $response->assertSee('コンビニ支払い');
    }
}
