<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;
use App\Models\Item;
use App\Models\Review;
use App\Models\Message;

class MypageController extends Controller
{
    /**
     *  マイページ画面の表示
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $page = $request->input('page', 'sell');

        if ($user->profile_image) {
            $previewSrc = asset('storage/' . $user->profile_image);
        } else {
            $previewSrc = asset('images/default_user.png');
        }

        // ---- 出品した商品 ----
        $sellItems = $user->items()->get();

        // ---- 購入した商品 ----
        $buyItems = Item::where('buyer_id', $userId)
            ->where('status', '!=', 0)
            ->get();

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
            ->get();

        $tradeItemIds = $tradeItems->pluck('id');

        $totalUnreadCount = Message::whereIn('item_id', $tradeItemIds)
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();

        $averageRating = Review::where('reviewee_id', $user->id)->avg('rating');
        $roundedRating = $averageRating ? round($averageRating) : null;

        return view('mypage.profile', compact('user', 'previewSrc', 'page', 'sellItems', 'buyItems', 'tradeItems', 'totalUnreadCount', 'roundedRating'));
    }


    /**
     *  プロフィール編集画面の表示
     */
    public function edit()
    {
        $user = Auth::user();

        if ($user->profile_image) {
            $previewSrc = asset('storage/' . $user->profile_image);
        } else {
            $previewSrc = asset('images/default_user.png');
        }

        return view('mypage.edit', compact('user', 'previewSrc'));
    }


    /**
     *  プロフィール編集処理
     */
    public function store(ProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            // 古い画像を削除
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            // 新しい画像を保存
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $data['profile_image'] = $path;
        }

        $user->update([
            'name' => $data['name'],
            'profile_image' => $data['profile_image'] ?? $user->profile_image,
        ]);

        $user->address()->create([
            'user_id' => $user->id,
            'postal_code' => $data['postal_code'],
            'address' => $data['address'],
            'building' => $data['building'] ?? null,
        ]);

        return redirect()->route('items.index');
    }


    /**
     *  プロフィール更新処理
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            // 古い画像を削除
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            // 新しい画像を保存
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $data['profile_image'] = $path;
        }

        $user->update([
            'name' => $data['name'],
            'profile_image' => $data['profile_image'] ?? $user->profile_image,
        ]);

        $user->address()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $data['postal_code'],
                'address' => $data['address'],
                'building' => $data['building'] ?? null,
            ]
        );

        return redirect()->route('mypage.profile');
    }
}
