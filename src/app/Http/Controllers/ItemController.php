<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;



class ItemController extends Controller
{
    /**
     *  商品一覧画面の表示
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'recommend');
        $keyword = $request->input('keyword', '');

        // おすすめアイテムの取得
        $recommendItems = Item::orderBy('created_at', 'desc')
            ->when(auth()->check(), function($query) {
                $query->where('items.user_id', '!=', auth()->id());
            })
            ->when($keyword, function($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->get();

        // マイリストアイテムの取得
        $myListItems = collect();
        if ($tab === 'mylist' && auth()->check()) {
            $myListItems = auth()->user()->myListItems()
                ->where('items.user_id', '!=', auth()->id())
                ->when($keyword, function($query, $keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                })
                ->get();
        }

        return view('items.index', compact('tab', 'recommendItems', 'myListItems', 'keyword'));
    }


    /**
     *  商品詳細画面の表示
     */
    public function show($id)
    {
        $item = Item::with(['user', 'categories', 'favorites', 'comments'])->findOrFail($id);

        return view('items.show', compact('item'));
    }


    /**
     *  いいね処理
     */
    public function toggle($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        // 既にいいねしているかチェック
        if ($item->favorites()->where('user_id', $user->id)->exists()) {
            $item->favorites()->where('user_id', $user->id)->delete();
            $status = 'unliked';
        } else {
            $item->favorites()->create(['user_id' => $user->id]);
            $status = 'liked';
        }

        // Ajax用にJSONで返す
        return response()->json([
            'status' => $status,
            'count' => $item->favorites()->count(),
        ]);
    }


    /**
     *  コメント処理
     */
    public function store(CommentRequest $request, $item_id)
    {
        $data = $request->validated();

        $comment = Comment::create([
            'item_id' => $item_id,
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ]);

        // Ajax用にJSONで返す
        return response()->json([
            'id' => $comment->id,
            'content' => $comment->content,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'profile_image' => $comment->user->profile_image,
            ],
        ]);
    }
}
