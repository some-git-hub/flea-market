<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\DeliveryAddress;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;


class PurchaseController extends Controller
{
    /**
     *  購入画面の表示
     */
    public function checkout($id)
    {
        $user = auth()->user();
        $item = Item::with(['categories', 'user', 'favorites', 'comments'])->findOrFail($id);

        return view('purchase.checkout', compact('item', 'user',));
    }


    /**
     *  配達先変更画面の表示
     */
    public function edit(Request $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        return view('purchase.address.edit', compact('user', 'item'));
    }


    /**
     *  配達先更新処理
     */
    public function update(AddressRequest $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        $data = $request->validated();

        session([
            "checkout_postal_code_{$user->id}_{$item->id}" => $data['postal_code'],
            "checkout_address_{$user->id}_{$item->id}"     => $data['address'],
            "checkout_building_{$user->id}_{$item->id}"    => $data['building'],
        ]);

        return redirect()->route('purchase.checkout', ['item_id' => $item_id]);
    }


    /**
     *  支払い方法の保存処理
     */
    public function savePaymentMethod(Request $request, $item_id)
    {
        $user = auth()->user();

        session(["checkout_payment_method_{$user->id}_{$item_id}" => $request->payment_method]);

        return response()->json(['status' => 'ok']);
    }


    /**
     *  購入処理
     */
    public function store(PurchaseRequest $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        $data = $request->validated();

        $postal_code = session("checkout_postal_code_{$user->id}_{$item->id}") ?? $user->address?->postal_code;
        $address     = session("checkout_address_{$user->id}_{$item->id}") ?? $user->address?->address;

        if (session()->exists("checkout_building_{$user->id}_{$item->id}")) {
            $building = session("checkout_building_{$user->id}_{$item->id}");
        } else {
            $building = $user->address?->building;
        }

        session()->forget([
            "checkout_postal_code_{$user->id}_{$item->id}",
            "checkout_address_{$user->id}_{$item->id}",
            "checkout_building_{$user->id}_{$item->id}",
            "checkout_payment_method_{$user->id}_{$item->id}",
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));
        $stripePaymentMethod = $data['payment_method'] == 1 ? 'konbini' : 'card';

        // Stripe Checkout セッションの作成
        $session = Session::create([
            'payment_method_types' => [$stripePaymentMethod],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('items.index'),
            'cancel_url'  => route('purchase.checkout', ['item_id' => $item->id]),
            'metadata' => [
                'user_id'        => $user->id,
                'item_id'        => $item->id,
                'payment_method' => $data['payment_method'],
                'postal_code'    => $postal_code,
                'address'        => $address,
                'building'       => $building,
            ],
        ]);

        return redirect($session->url);
    }
}
