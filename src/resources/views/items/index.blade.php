@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/index.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">
    <div class="nav-tabs">
        <div class="nav-tab__recommend">
            <a href="{{ $keyword ? '/?keyword=' . urlencode($keyword) : '/' }}" class="nav-tab__link-recommend {{ request('tab', 'recommend') === 'recommend' ? 'active' : '' }}">
                おすすめ
            </a>
        </div>
        <div class="nav-tab__mylist">
            <a href="/?tab=mylist{{ $keyword ? '&keyword=' . urlencode($keyword) : '' }}" class="nav-tab__link-mylist {{ request('tab') === 'mylist' ? 'active' : '' }}">
                マイリスト
            </a>
        </div>
    </div>
    <div class="items-list" id="items-list">
        @if($tab === 'recommend')
            @foreach($recommendItems as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item->id) }}" class="item-card__link-show">
                    <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}" class="item-image">
                    @if($item->status !== 0)
                    <span class="item-card__badge-sold">Sold</span>
                    @endif
                </a>
                <p class="item-name">{{ $item->name }}</p>
            </div>
            @endforeach
        @elseif($tab === 'mylist')
            @auth
                @foreach($myListItems as $item)
                <div class="item-card">
                    <a href="{{ route('items.show', $item->id) }}" class="item-card__link-show">
                        <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}" class="item-image">
                        @if($item->status !== 0)
                        <span class="item-card__badge-sold">Sold</span>
                        @endif
                    </a>
                    <p class="item-name">{{ $item->name }}</p>
                </div>
                @endforeach
            @endauth
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
document.getElementById('search-box').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#items-list .item-card');

    items.forEach(item => {
        const name = item.querySelector('.item-name').textContent.toLowerCase();
        if(name.includes(query)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection