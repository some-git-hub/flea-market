@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/show.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">
    <div class="item-image-area">
        <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}" class="item-image">
        @if($item->status !== 0)
        <span class="item-card__badge-sold">Sold</span>
        @endif
    </div>
    <div class="item-detail__container">
        <h2 class="item-name">
            {{ $item->name }}
        </h2>
        <p class="item-brand">
            {{ $item->brand }}
        </p>
        <p class="item-price">
            <span class="item-price-yen">￥</span>
            <span class="item-price-number">{{ number_format($item->price) }}</span>
            <span class="item-price-tax">(税込)</span>
        </p>
        <div class="item-reaction">
            <form class="item-reaction__contents" id="favorite-form">
                @csrf
                @auth
                <button type="button" id="favorite-icon" class="favorite-button {{ auth()->user() && $item->favorites->contains('user_id', auth()->id()) ? 'liked' : '' }}">
                    <img class="item-reaction__logo" id="favorite-img" alt="favorite_logo"
                        src="{{ $item->favorites->contains('user_id', auth()->id())
                            ? asset('storage/images/favorite_logo_active.png')
                            : asset('storage/images/favorite_logo.png') }}">
                </button>
                @else
                <a href="{{ route('login') }}">
                    <img class="item-reaction__logo" alt="favorite_logo"
                        src="{{ asset('storage/images/favorite_logo.png') }}">
                </a>
                @endauth
                <span id="favorite-count">{{ $item->favorites->count() }}</span>
            </form>
            <div class="item-reaction__contents">
                <img class="item-reaction__logo" src="{{ asset('storage/images/comment_logo.png') }}" alt="comment_logo">
                <span class="item-reaction__number" id="comment-count-reaction">{{ $item->comments->count() }}</span>
            </div>
        </div>
        <div class="item-purchase__link-area">
            <a href="{{ route('purchase.checkout', $item->id) }}" class="item-purchase__link-checkout">購入手続きへ</a>
        </div>
        <div class="item-description">
            <h3 class="item-description__title">商品説明</h3>
            <p class="item-description__content">{{ $item->description }}</p>
        </div>
        <div class="item-info">
            <h3 class="item-info__title">商品の情報</h3>
            <div class="item-category">
                <h4 class="item-category__title">カテゴリー</h4>
                <p class="item-category__contents">
                    @foreach($item->categories as $category)
                    <span class="item-category__content">{{ $category->name }}</span>
                    @endforeach
                </p>
            </div>
            <div class="item-condition">
                <h4 class="item-condition__title">商品の状態</h4>
                <p class="item-condition__content">
                    {{ config('const.item.conditions')[$item->condition] ?? '不明' }}
                </p>
            </div>
        </div>
        <div class="item-comment">
            <h3 class="item-comment__title">
                コメント(<span id="comment-count-title">{{ $item->comments->count() }}</span>)
            </h3>
            <div class="item-comment__contents" id="comments">
                @foreach($item->comments as $comment)
                <div class="item-comment__content">
                    <div class="item-comment__user">
                        <img class="item-comment__user-image"
                            src="{{ $comment->user && $comment->user->profile_image
                                ? asset('storage/' . $comment->user->profile_image)
                                : asset('storage/images/default_user.png') }}">
                        <h4 class="item-comment__user-name">
                            {{ $comment->user->name }}
                        </h4>
                    </div>
                    <div class="item-comment__content-text">
                        {{ $comment->content }}
                    </div>
                </div>
                @endforeach
            </div>
            <form id="comment-form" class="comment-form" method="POST" action="{{ route('comment.store', $item->id) }}">
                @csrf
                <h4 class="comment-form__title">商品へのコメント</h4>
                <div class="comment-form__textarea-area">
                    <textarea class="comment-form__textarea" maxlength="255" name="content" id="comment-content"></textarea>
                </div>
                <div id="comment-error" class="comment-form__error-message"></div>
                <div class="comment-form__button-area">
                    <button class="comment-form__button-submit" type="submit">コメントを送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // いいね機能
    const favoriteIcon  = document.getElementById('favorite-icon');
    const favoriteImg   = document.getElementById('favorite-img');
    const favoriteCount = document.getElementById('favorite-count');

    favoriteIcon.addEventListener('click', function() {
        fetch("{{ route('favorite.toggle', $item->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })

        .then(response => response.json())
        .then(data => {
            favoriteCount.textContent = data.count;
            favoriteImg.src = data.status === 'liked'
                ? "{{ asset('storage/images/favorite_logo_active.png') }}"
                : "{{ asset('storage/images/favorite_logo.png') }}";
        });
    });


    // コメント投稿機能
    const commentForm = document.getElementById('comment-form');
    if(commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let response = await fetch(this.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value
                },
                body: formData
            });

            if (response.ok) {
                let data = await response.json();
                const commentsDiv = document.getElementById('comments');
                const newComment  = document.createElement('div');
                newComment.classList.add('item-comment__content');
                newComment.innerHTML = `
                    <div class="item-comment__user">
                        <img class="item-comment__user-image"
                            src="${data.user.profile_image
                                    ? '/storage/' + data.user.profile_image
                                    : '/storage/images/default_user.png'}">
                        <h4 class="item-comment__user-name">${data.user.name}</h4>
                    </div>
                    <div class="item-comment__content-text">${data.content}</div>
                `;
                commentsDiv.appendChild(newComment);

                // コメント数を更新
                const commentCountReaction = document.getElementById('comment-count-reaction');
                const commentCountTitle    = document.getElementById('comment-count-title');
                const newCount             = parseInt(commentCountReaction.textContent) + 1;
                commentCountReaction.textContent = newCount;
                commentCountTitle.textContent    = newCount;

                // フォームリセット & エラーメッセージ消す
                document.getElementById('comment-content').value     = '';
                document.getElementById('comment-error').textContent = '';
            } else if (response.status === 422) {
                let errorData = await response.json();

                if (errorData.errors && errorData.errors.content) {
                    document.getElementById('comment-error').textContent = errorData.errors.content[0];
                }
            }
        });
    }
});
</script>
@endsection