<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;

class ExhibitionController extends Controller
{
    /**
     *  出品画面の表示
     */
    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }


    /**
     *  出品処理
     */
    public function store(ExhibitionRequest $request)
    {
        $data = $request->validated();

        // 画像アップロード
        $imagePath = null;
        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('items', 'public');
        }

        $item = Item::create([
            'user_id'     => Auth::id(),
            'name'        => $data['name'],
            'price'       => $data['price'],
            'brand'       => $data['brand'],
            'description' => $data['description'],
            'item_image'  => $imagePath,
            'condition'   => $data['condition'],
            'status'      => 0, // 出品中:0, 入金待ち:1, 取引中:2, 取引完了:3
        ]);

        $item->categories()->attach($data['category']);

        return redirect()->route('items.index');
    }
}
