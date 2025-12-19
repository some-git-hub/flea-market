<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\StripeObject;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\DeliveryAddress;

class StripeWebhookController extends Controller
{
    /**
     *  購入処理 (Stripe)
     */
    public function handle(Request $request)
    {
        $payload        = $request->getContent();
        $sigHeader      = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            Log::error('Stripe Webhook error: ' . $e->getMessage());
            return response($e->getMessage(), 400);
        }

        // ユーザー情報・配送先情報の抽出
        $metadata = [];
        if (!empty($event->data->object->metadata)) {
            $metadata = $event->data->object->metadata instanceof StripeObject
                ? $event->data->object->metadata->toArray()
                : (array) $event->data->object->metadata;
        }

        $itemId = $metadata['item_id'] ?? null;
        if (!$itemId) {
            Log::error('Stripe webhook missing item_id', $metadata);
            return response('item_id missing', 400);
        }

        $item          = Item::find($itemId);
        if (!$item) {
            Log::error("Stripe Webhook: item not found", ['item_id' => $itemId, 'metadata' => $metadata]);
            return response('Item not found', 400);
        }

        $userId        = $metadata['user_id'] ?? null;
        $paymentMethod = $metadata['payment_method'] ?? 0;
        $postalCode    = $metadata['postal_code'] ?? null;
        $address       = $metadata['address'] ?? null;
        $building      = $metadata['building'] ?? null;


        // checkout.session.completed
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            if (($paymentMethod) == '1') {
                // コンビニ支払いの場合は「入金待ち/status=1」
                Item::where('id', $itemId)->update([
                    'status'   => 1,
                    'buyer_id' => $userId,
                ]);

                Log::info('Item status set to 2 (pending, konbini)', $metadata);
            } else {
                $deliveryAddress = DeliveryAddress::firstOrCreate([
                    'postal_code' => $postalCode ?? '不明',
                    'address'     => $address ?? '不明',
                    'building'    => $building ?? '',
                ]);

                Purchase::firstOrCreate(
                    [
                        'user_id'        => $userId,
                        'item_id'        => $itemId,
                        'payment_method' => $paymentMethod,
                    ],
                    [
                        'delivery_address_id' => $deliveryAddress->id,
                        'price'               => $session->amount_total ?? 0,
                    ]
                );

                // カード支払いの場合は「購入済み/status=2」
                Item::where('id', $itemId)->update([
                    'status' => 2,
                    'buyer_id' => $userId,
                ]);

                Log::info('Purchase saved and item status updated (card)', $metadata);
            }
        }


        // payment_intent.succeeded （コンビニ入金完了時）
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $metadata = (array) $paymentIntent->metadata;

            if (($paymentMethod) == '1') {
                $deliveryAddress = DeliveryAddress::firstOrCreate([
                    'postal_code' => $postalCode ?? '不明',
                    'address'     => $address ?? '不明',
                    'building'    => $building ?? '',
                ]);

                Purchase::firstOrCreate(
                    [
                        'user_id'        => $userId,
                        'item_id'        => $itemId,
                        'payment_method' => $paymentMethod,
                    ],
                    [
                        'delivery_address_id' => $deliveryAddress->id,
                        'price'               => $item->price ?? 0,
                    ]
                );

                // コンビニ支払いの入金が確認された場合は「購入済み/status=2」
                Item::where('id', $itemId)->update([
                    'status' => 2,
                    'buyer_id' => $userId,
                ]);

                Log::info('Purchase saved and item status updated (konbini completed)', $metadata);
            }
        }


        // checkout.session.expired
        if ($event->type === 'checkout.session.expired') {
            $session  = $event->data->object;
            $metadata = (array) $session->metadata;

            if (isset($itemId)) {
                // コンビニ支払いの入金が期限内に確認されなかった場合は「出品中/status=0」
                Item::where('id', $itemId)->where('status', 1)->update([
                    'status' => 0,
                    'buyer_id' => null,
                ]);

                Log::info('Item status reverted to 0 (checkout.session.expired)', $metadata);
            }
        }

        return response('Webhook received', 200);
    }
}
