<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\VerifyController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// ゲストのみアクセス可能
Route::middleware('guest')->group(function () {

    // 新規登録
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // ログイン
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

});



// メール認証画面
Route::get('/email/verify', [VerifyController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

// メール認証リンク
Route::get('/email/verify/{id}/{hash}', [VerifyController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

// 認証メール再送信
Route::post('/email/verification-notification', [VerifyController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');



// 商品一覧
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');



// ログインユーザーのみアクセス可能
Route::middleware('auth')->group(function () {

    // いいね・コメント
    Route::post('/item/{item_id}/favorite', [ItemController::class, 'toggle'])->name('favorite.toggle');
    Route::post('/item/{item_id}/comment', [ItemController::class, 'store'])->name('comment.store');

    // 商品の購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::post('/purchase/{item_id}/store', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'edit'])->name('address.edit');
    Route::put('/purchase/address/{item_id}/update', [PurchaseController::class, 'update'])->name('address.update');
    Route::post('/purchase/{item_id}/save-payment-method', [PurchaseController::class, 'savePaymentMethod'])->name('purchase.savePaymentMethod');

    // 商品の取引
    Route::get('/trade/{item}', [TradeController::class, 'show'])->name('items.trade');
    Route::post('/trade/{item}/message', [TradeController::class, 'store'])->name('message.store');
    Route::patch('/messages/{message}', [TradeController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [TradeController::class, 'destroy'])->name('messages.destroy');
    Route::post('/trade/{item}/complete', [TradeController::class, 'complete'])->name('trade.complete');
    Route::post('/items/{item}/review', [TradeController::class, 'storeReview'])->name('reviews.store');


    // 商品の出品
    Route::get('/sell', [ExhibitionController::class, 'create'])->name('items.create');
    Route::post('/sell/store', [ExhibitionController::class, 'store'])->name('items.store');

    // マイページ
    Route::get('/mypage', [MypageController::class, 'show'])->name('mypage.profile');
    Route::get('/mypage/profile', [MypageController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile/store', [MypageController::class, 'store'])->name('mypage.store');
    Route::put('/mypage/profile/update', [MypageController::class, 'update'])->name('mypage.update');

    // ログアウト
    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');

});




// GETメソッド以外のURLを直接入力された場合の処理
Route::get('/item/{item_id}/favorite', function ($item_id) {
    return redirect("/item/{$item_id}");
});
Route::get('/item/{item_id}/comment', function ($item_id) {
    return redirect("/item/{$item_id}");
});
Route::get('/purchase/{item_id}/store', function ($item_id) {
    return redirect("/purchase/{$item_id}");
});
Route::get('/purchase/address/{item_id}/update', function ($item_id) {
    return redirect("/purchase/address/{$item_id}");
});
Route::get('/purchase/{item_id}/save-payment-method', function ($item_id) {
    return redirect("/purchase/{$item_id}");
});
Route::get('/sell/store', function () {
    return redirect('/sell');
});
Route::get('/mypage/profile/store', function () {
    return redirect('/mypage/profile');
});
Route::get('/mypage/profile/update', function () {
    return redirect('/mypage/profile');
});
Route::get('/logout', function () {
    return redirect('/');
});
Route::get('/email/verification-notification', function () {
    return redirect('/');
});
