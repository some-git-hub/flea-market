@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">
    <div class="mypage-profile">
        <div class="mypage-profile__image-area">
            <img id="preview" class="mypage-profile__image" src="{{ $previewSrc }}">
        </div>
        <h2 class="mypage-profile__user-name">
            {{ Auth::user()->name }}
        </h2>
        <div class="mypage-profile-link-area">
            <a class="mypage-profile__link-edit" href="{{ route('mypage.edit') }}">
                プロフィールを編集
            </a>
        </div>
    </div>
    <div class="nav-tabs">
        <div class="nav-tab__sell">
            <a href="/mypage?page=sell" class="nav-tab__link-sell {{ request('page', 'sell') === 'sell' ? 'active' : '' }}">
                出品した商品
            </a>
        </div>
        <div class="nav-tab__buy">
            <a href="/mypage?page=buy" class="nav-tab__link-buy {{ request('page') === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>
    </div>
    <div class="mypage-items" id="items-list">
        @if($page === 'sell')
            @foreach($sellItems as $sellItem)
            <div class="item-card">
                <a href="{{ route('items.show', $sellItem->id) }}" class="item-card__link-show">
                    <img src="{{ asset('storage/' . $sellItem->item_image) }}" alt="{{ $sellItem->name }}" class="item-image">
                    @if($sellItem->status !== 0)
                    <span class="item-card__badge-sold">Sold</span>
                    @endif
                </a>
                <p class="item-name">
                    {{ $sellItem->name }}
                </p>
            </div>
            @endforeach
        @elseif($page === 'buy')
            @foreach($buyItems as $buyItem)
            <div class="item-card">
                <a href="{{ route('items.show', $buyItem->id) }}" class="item-card__link-show">
                    <img src="{{ asset('storage/' . $buyItem->item_image) }}" alt="{{ $buyItem->name }}" class="item-image">
                    @if($buyItem->status !== 0)
                    <span class="item-card__badge-sold">Sold</span>
                    @endif
                </a>
                <p class="item-name">
                    {{ $buyItem->name }}
                </p>
            </div>
            @endforeach
        @endif
    </div>
</div>
@endsection