<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Item;
use App\Models\User;
use App\Models\Message;
use App\Models\Review;
use App\Http\Requests\MessageRequest;
use App\Http\Requests\ReviewRequest;


class TradeController extends Controller
{
    /**
     * 取引チャット画面の表示
     */
    public function show(Item $item)
    {
        $user  = auth()->user();
        $userId = $user->id;
        abort_if($item->buyer_id === null, 404);

        $isSeller = $item->user_id  === $userId;
        abort_unless($isSeller || $item->buyer_id === $userId, 403);

        // ---- 取引中の商品 ----
        $tradeItems = Item::where(function ($query) use ($userId) {

            // ---- 取引が完了していない商品 ----
            $query->where('status', 2)
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->orWhere('buyer_id', $userId);
                });
            })

            // ---- 取引完了後に取引相手の評価をしていない商品 ----
            ->orWhere(function ($query) use ($userId) {
                $query->where('status', 3)
                    ->where(function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                            ->orWhere('buyer_id', $userId);
                    })
                    ->whereDoesntHave('reviews', function ($q) use ($userId) {
                        $q->where('reviewer_id', $userId);
                    });
            })

            // 未読メッセージ数
            ->withCount(['messages as unread_count' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)
                    ->whereNull('read_at');
            }])

            // 相手の最新メッセージ日時
            ->withMax(['messages as partner_last_message_at' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId);
            }], 'created_at')

            // 未読・新着メッセージ順
            ->orderByRaw('unread_count > 0 DESC')
            ->orderByDesc('partner_last_message_at')
            ->take(10)
            ->get();

        $partner = $isSeller
            ? User::findOrFail($item->buyer_id)
            : User::findOrFail($item->user_id);

        $messages = Message::where('item_id', $item->id)
            ->orderBy('created_at')
            ->get();

        $hasReviewed = Review::where('item_id', $item->id)
            ->where('reviewer_id', auth()->id())
            ->exists();

        Message::where('item_id', $item->id)
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('items.trade', compact('item', 'user', 'tradeItems', 'partner', 'isSeller','messages', 'hasReviewed'));
    }


    /**
     * 取引メッセージの送信処理
     */
    public function store(MessageRequest $request, Item $item)
    {
        $user = auth()->user();

        abort_unless(
            $user->id === $item->user_id || $user->id === $item->buyer_id,
            403
        );

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('message_images', 'public');
        }

        Message::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'image'   => $imagePath,
        ]);

        return redirect()
            ->route('items.trade', $item->id)
            ->with('success', 'メッセージを送信しました');
    }


    /**
     * 送信済みメッセージの編集処理
     */
    public function update(MessageRequest $request, Message $message)
    {
        abort_if($message->user_id !== auth()->id(), 403);

        $message->update([
            'content'   => $request->validated()['content'],
            'edited_at' => now(),
        ]);

        return response()->json();
    }


    /**
     * 送信済みメッセージの削除処理
     */
    public function destroy(Message $message)
    {
        abort_if($message->user_id !== auth()->id(), 403);

        $message->delete();

        return response()->json(['status' => 'deleted']);
    }


    /**
     * 取引完了処理
     */
    public function complete(Request $request, Item $item)
    {
        $user = auth()->user();

        abort_unless($item->fresh()->status === 2, 403);
        abort_unless($item->buyer_id === $user->id, 403);

        $partner = User::findOrFail($item->user_id);

        $item->update([
            'status' => 3, // 取引完了
        ]);

        Mail::raw("
            {$user->name}様

            ご出品中の商品についての取引が完了いたしました。

            商品名：{$item->name}

            商品内容や取引状況をご確認のうえ、
            取引画面から取引相手の評価をお願いいたします。

            今後ともよろしくお願いいたします。",
            function ($message) use ($partner) {
                $message->to($partner->email)->subject('【取引通知】商品購入者が取引完了しました');
            }
        );

        return redirect()
            ->route('items.trade', $item->id)
            ->with('completed', true);
    }


    /**
     * 取引相手の評価処理
     */
    public function storeReview(ReviewRequest $request, Item $item)
    {
        if ($item->status !== 3) {
            abort(403);
        }

        if (!$item->buyer_id) {
            return back()->withErrors('取引相手が確定していません');
        }

        if (Review::where('item_id', $item->id)
            ->where('reviewer_id', auth()->id())
            ->exists()) {
            return back()->withErrors('すでに評価済みです');
        }

        $reviewer = auth()->user();

        $revieweeId = $item->user_id === $reviewer->id
            ? $item->buyer_id
            : $item->user_id;

        Review::create([
            'item_id'     => $item->id,
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $revieweeId,
            'rating'      => $request->rating,
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', '評価を送信しました');
    }
}
