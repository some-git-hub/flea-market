<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;
use App\Models\Item;

class MypageController extends Controller
{
    /**
     *  マイページ画面の表示
     */
    public function show(Request $request)
    {
        $user = auth()->user();

        if ($user->profile_image) {
            $previewSrc = asset('storage/' . $user->profile_image);
        } else {
            $previewSrc = asset('storage/images/default_user.png');
        }

        $page = $request->input('page', 'sell');

        $sellItems = $user->items()->get();
        $buyItems = Item::where('buyer_id', $user->id)
            ->whereIn('status', [1, 2])
            ->get();

        return view('mypage.profile', compact('user', 'previewSrc', 'page', 'sellItems', 'buyItems'));
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
            $previewSrc = asset('storage/images/default_user.png');
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
