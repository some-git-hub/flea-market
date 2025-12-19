@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">

    <!-- マイページ -->
    <div class="mypage-profile">

        <!-- プロフィール画像 -->
        <div class="mypage-profile__image-area">
            <img id="preview" class="mypage-profile__image" src="{{ $previewSrc }}">
        </div>

        <!-- ユーザー情報 -->
        <div class="mypage-profile__user-info">
            <h1 class="mypage-profile__user-name">
                {{ $user->name }}
            </h1>
            <p class="mypage-profile__user-rating">
                @if($roundedRating)
                    @for ($i = 1; $i <= 5; $i++)
                        <img class="mypage-profile__rating-star" src="{{ asset($i <= $roundedRating ? 'images/star_active.png' : 'images/star_inactive.png') }}" alt="star">
                    @endfor
                @endif
            </p>
        </div>

        <!-- プロフィール編集ページ遷移リンク -->
        <div class="mypage-profile-link-area">
            <a class="mypage-profile__link-edit" href="{{ route('mypage.edit') }}">
                プロフィールを編集
            </a>
        </div>
    </div>

    <!-- ナビゲーションリンク -->
    <div class="nav-tabs">

        <!-- 出品した商品 -->
        <div class="nav-tab__sell">
            <a href="/mypage?page=sell" class="nav-tab__link {{ request('page', 'sell') === 'sell' ? 'active' : '' }}">
                出品した商品
            </a>
        </div>

        <!-- 購入した商品 -->
        <div class="nav-tab__buy">
            <a href="/mypage?page=buy" class="nav-tab__link {{ request('page') === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>

        <!-- 取引中の商品 -->
        <div class="nav-tab__trade">
            <a href="/mypage?page=trade" class="nav-tab__link {{ request('page') === 'trade' ? 'active' : '' }}">
                取引中の商品
            </a>

            <!-- 通知メッセージ数 -->
            @if($totalUnreadCount > 0)
                <span class="item-card__badge-notification-total">{{ $totalUnreadCount }}</span>
            @endif
        </div>
    </div>

    <!-- 出品・購入・取引中の商品一覧 -->
    <div class="mypage-items" id="items-list">
        @if($page === 'sell')
            @foreach($sellItems as $sellItem)
            <!-- 出品した商品 -->
            <div class="item-card">
                <a href="{{ route('items.show', $sellItem->id) }}" class="item-card__link-show">
                    <img src="{{ asset('storage/' . $sellItem->item_image) }}" alt="{{ $sellItem->name }}" class="item-image">
                    @if($sellItem->status !== 0)
                    <span class="item-card__badge-sold">Sold</span>
                    @endif
                </a>
                <p class="item-name">{{ $sellItem->name }}</p>
            </div>
            @endforeach
        @elseif($page === 'buy')
            @foreach($buyItems as $buyItem)
            <!-- 購入した商品 -->
            <div class="item-card">
                <a href="{{ route('items.show', $buyItem->id) }}" class="item-card__link-show">
                    <img src="{{ asset('storage/' . $buyItem->item_image) }}" alt="{{ $buyItem->name }}" class="item-image">
                    @if($buyItem->status !== 0)
                        <span class="item-card__badge-sold">Sold</span>
                    @endif
                </a>
                <p class="item-name">{{ $buyItem->name }}</p>
            </div>
            @endforeach
        @elseif($page === 'trade')
            @foreach($tradeItems as $tradeItem)
            <!-- 取引中の商品 -->
            <div class="item-card">
                <a href="{{ route('items.trade', $tradeItem->id) }}" class="item-card__link-show">
                    <img src="{{ asset('storage/' . $tradeItem->item_image) }}" alt="{{ $tradeItem->name }}" class="item-image">
                    @if($tradeItem->unread_count > 0)
                        <span class="item-card__badge-notification">{{ $tradeItem->unread_count }}</span>
                    @endif
                </a>
                <p class="item-name">{{ $tradeItem->name }}</p>
            </div>
            @endforeach
        @endif
    </div>
</div>
@endsection