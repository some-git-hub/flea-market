@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/trade.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">
    <div class="trade-chat">

        <!-- 見出し -->
        <div class="trade-chat__heading-wrapper">
            <div class="trade-chat__heading-area">
                <img class="trade-chat__heading-user-image"
                    src="{{ $partner->profile_image
                        ? asset('storage/' . $partner->profile_image)
                        : asset('images/default_user.png') }}"
                    alt="UserImage">
                <h1 class="trade-chat__heading">「{{ $partner->name }}」さんとの取引画面</h1>
            </div>

            <!-- 取引完了ボタン -->
            @if(!$isSeller)
                @if($item->status === 2)
                    <form class="trade-chat__complete-form" action="{{ route('trade.complete', $item->id) }}" method="post">
                        @csrf
                        <button class="trade-chat__button-complete">取引を完了する</button>
                    </form>
                @elseif($item->status === 3)
                    <div class="trade-chat__complete-form is-disabled">
                        <a href="{{ url()->current() }}" class="trade-chat__button-complete is-disabled">取引を完了する</a>
                    </div>
                @endif
            @endif
        </div>

        <!-- 商品情報 -->
        <div class="trade-chat__item-info">
            <img class="trade-chat__item-image" src="{{ asset('storage/' . $item->item_image) }}" alt="ItemImage">
            <div class="trade-chat__item-info-inner">
                <p class="trade-chat__item-name">{{ $item->name }}</p>
                <p class="trade-chat__item-price">￥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <!-- チャット -->
        <div class="trade-chat__messages">
            @foreach ($messages as $message)
                @if($message->user_id === $user->id)

                    <!-- 自分のメッセージ -->
                    <div class="trade-chat__message my-message">

                        <!-- ユーザー情報 -->
                        <div class="trade-chat__user-info">
                            <p class="trade-chat__user-name">{{ $message->user->name }}</p>
                            <img class="trade-chat__user-image"
                                src="{{ $message->user->profile_image
                                    ? asset('storage/' . $message->user->profile_image)
                                    : asset('images/default_user.png') }}"
                                alt="ProfileImage">
                        </div>

                        <!-- メッセージ本文 -->
                        <p class="trade-chat__message-content">{{ $message->content }}</p>

                        <div class="trade-chat__buttons">

                            <!-- メッセージの編集および保存ボタン -->
                            <button class="trade-chat__button-edit" data-id="{{ $message->id }}">編集</button>
                            <button class="trade-chat__button-save" data-id="{{ $message->id }}" style="display:none;">保存</button>

                            <!-- メッセージの削除ボタン -->
                            <button class="trade-chat__button-delete" data-id="{{ $message->id }}">削除</button>
                        </div>

                        <!-- 送信した画像 -->
                        @if($message->image)
                            <img class="trade-chat__message-image" src="{{ asset('storage/' . $message->image) }}" alt="MessageImage">
                        @endif
                    </div>
                @else

                    <!-- 取引相手のメッセージ -->
                    <div class="trade-chat__message partners-message">

                        <!-- ユーザー情報 -->
                        <div class="trade-chat__user-info">
                            <img class="trade-chat__user-image"
                                src="{{ $message->user->profile_image
                                    ? asset('storage/' . $message->user->profile_image)
                                    : asset('images/default_user.png') }}"
                                alt="ProfileImage">
                            <p class="trade-chat__user-name">{{ $message->user->name }}</p>
                        </div>

                        <!-- メッセージ本文 -->
                        <p class="trade-chat__message-content">{{ $message->content }}</p>

                        <!-- 送信した画像 -->
                        @if($message->image)
                            <img class="trade-chat__message-image" src="{{ asset('storage/' . $message->image) }}" alt="MessageImage">
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <!-- チャット入力欄 -->
        <form class="trade-chat__message-form" action="{{ route('message.store', $item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="trade-chat__message-form-container">

                <!-- 画像プレビュー -->
                <img class="image-preview" id="imagePreview">
                <div class="trade-chat__message-form-inner">

                    <!-- メッセージ入力欄 -->
                    <div class="trade-chat__textarea-area">
                        @error('content')
                            <div class="trade-chat__error-message">{{ $message }}</div>
                        @enderror
                        @error('image')
                            <div class="trade-chat__error-message">{{ $message }}</div>
                        @enderror
                        <textarea class="trade-chat__textarea" id="chatTextarea" name="content" rows="1" placeholder="取引メッセージを記入してください">{{ old('content') }}</textarea>
                    </div>

                    <!-- 画像の追加ボタン -->
                    <div class="trade-chat__image-input-area">
                        <label class="trade-chat__label-image" for="imageInput">画像を追加</label>
                        <input class="trade-chat__input-image" id="imageInput" type="file" name="image" accept=".png,.jpeg">
                    </div>

                    <!-- メッセージ送信ボタン -->
                    <div class="trade-chat__button-area">
                        <label for="submitButton" class="trade-chat__label-submit">
                            <img class="trade-chat__image-submit" src="{{ asset('images/submit_logo.png') }}" alt="submit-image">
                        </label>
                        <button id="submitButton" class="trade-chat__button-submit" type="submit">送信</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- その他の取引一覧 -->
    <div class="other-trade">
        <h2 class="other-trade__heading">その他の取引</h2>
        <div class="other-trade__items">
            @foreach($tradeItems as $tradeItem)
                <a href="{{ route('items.trade', $tradeItem->id) }}" class="other-trade__link-item">{{ $tradeItem->name }}</a>
            @endforeach
        </div>
    </div>

    <!-- 取引相手の評価モーダル -->
    @if($item->status === 3 && !$hasReviewed)
        <div id="reviewModal" class="modal">
            <div class="review-form">
                <h2 class="review-form__heading">取引が完了しました。</h2>
                <form class="review-form__inner" id="reviewForm" method="POST" action="{{ route('reviews.store', $item->id) }}">
                    @csrf
                    <!-- 評価 -->
                    <p class="review-form__paragraph">今回の取引相手はどうでしたか？</p>
                    <div class="review-form__input-area">
                        <input class="review-form__input" type="hidden" name="rating" id="rating" value="{{ old('rating') }}">
                        @for ($i = 1; $i <= 5; $i++)
                            <img src="{{ asset('images/star_inactive.png') }}" class="star-img" data-value="{{ $i }}" id="star-{{ $i }}">
                        @endfor
                    </div>
                    @error('rating')
                        <p class="review-form__error-message--rating">{{ $message }}</p>
                    @enderror
                </form>

                <!-- 送信ボタン -->
                <div class="review-form__button-area">
                    <button class="review-form__button-submit" type="submit" form="reviewForm">送信する</button>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ============================
     *         共通DOM取得
     * ============================ */
    const textarea   = document.getElementById('chatTextarea');
    const imageInput = document.getElementById('imageInput');
    const preview    = document.getElementById('imagePreview');
    const formInner  = document.querySelector('.trade-chat__message-form-inner');
    const tradeChat  = document.querySelector('.trade-chat');
    const chat       = document.querySelector('.trade-chat__messages');
    const form       = document.querySelector('.trade-chat__message-form');

    const draftKey = `trade-chat-draft-user{{ $user->id }}-item{{ $item->id }}`;


    /* ============================
     *    チャット最下部スクロール
     * ============================ */
    function scrollToBottom() {
        if (!chat) return;
        chat.scrollTop = chat.scrollHeight;
    }

    // ---- 初期表示時は必ず最下部 ----
    scrollToBottom();


    /* =================================
     * textarea 自動リサイズ + 下書き保存
     * ================================= */
    const resizeTextarea = el => {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';

        // ---- フォーム全体の高さを取得 ----
        const currentFormHeight = form.offsetHeight;

        // ---- チャット領域の bottom を更新 ----
        chat.style.bottom = currentFormHeight + 'px';
    };

    textarea.addEventListener('input', () => {
        resizeTextarea(textarea);
        localStorage.setItem(draftKey, textarea.value);
    });

    // ---- 下書き復元 ----
    const savedDraft = localStorage.getItem(draftKey);

    if (savedDraft) {
        textarea.value = savedDraft;
        resizeTextarea(textarea);
    }

    resizeTextarea(textarea);


    /* ============================
     *  画像プレビュー + スクロール
     * ============================ */
    imageInput.addEventListener('change', () => {
        const file = imageInput.files[0];

        if (!file) {
            preview.style.display = 'none';
            preview.src = '';
            formInner.classList.remove('has-image');
            tradeChat.classList.remove('has-image');

            chat.style.bottom = form.offsetHeight + 'px';
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        formInner.classList.add('has-image');
        tradeChat.classList.add('has-image');

        // ---- 高さ再計算 ----
        chat.style.bottom = form.offsetHeight + 'px';

        setTimeout(scrollToBottom, 0);
    });


    /* ============================
     *         送信時処理
     * ============================ */
    form.addEventListener('submit', () => {
        localStorage.removeItem(draftKey);
        setTimeout(scrollToBottom, 0);
    });


    /* ============================
     *     メッセージ編集・削除
     * ============================ */
    document.querySelectorAll('.trade-chat__message').forEach(message => {

        const editBtn   = message.querySelector('.trade-chat__button-edit');
        const saveBtn   = message.querySelector('.trade-chat__button-save');
        const deleteBtn = message.querySelector('.trade-chat__button-delete');

        /* ---- 編集 ---- */
        if (editBtn && saveBtn) {
            editBtn.addEventListener('click', () => {
                const content = message.querySelector('.trade-chat__message-content');

                const textarea = document.createElement('textarea');
                textarea.value = content.innerText;
                textarea.rows = 1;

                textarea.classList.add(
                    'trade-chat__message-content',
                    'trade-chat__message-textarea'
                );

                // ---- 自動リサイズ処理 ----
                const autoResize = el => {
                    el.style.height = 'auto';
                    el.style.height = el.scrollHeight + 'px';
                };

                textarea.addEventListener('input', () => autoResize(textarea));

                // ---- 初期表示時も高さ調整 ----
                autoResize(textarea);

                content.replaceWith(textarea);

                requestAnimationFrame(() => {
                    autoResize(textarea);
                });

                editBtn.style.display = 'none';
                saveBtn.style.display = 'inline-block';

                textarea.focus();
            });

            saveBtn.addEventListener('click', async () => {
                const textarea  = message.querySelector('textarea');
                const messageId = saveBtn.dataset.id;

                const response = await fetch(`/messages/${messageId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ content: textarea.value }),
                });

                // ===== バリデーションエラー =====
                if (response.status === 422) {
                    const data = await response.json();

                    if (data.errors && data.errors.content) {
                        alert(data.errors.content[0]); // Requestのメッセージ
                    } else {
                        alert('入力内容にエラーがあります');
                    }
                    return;
                }

                // ===== その他エラー =====
                if (!response.ok) {
                    alert('更新に失敗しました');
                    return;
                }

                // ===== 成功時 =====
                const p = document.createElement('p');
                p.classList.add('trade-chat__message-content');
                p.innerText = textarea.value;

                textarea.replaceWith(p);
                saveBtn.style.display = 'none';
                editBtn.style.display = 'inline-block';
            });
        }

        /* ---- 削除 ---- */
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async () => {
                if (!confirm('このメッセージを削除しますか？')) return;

                const messageId = deleteBtn.dataset.id;
                const response = await fetch(`/messages/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                if (!response.ok) {
                    alert('削除に失敗しました');
                    return;
                }

                message.remove();
            });
        }
    });


    /* ============================
     *         評価モーダル
     * ============================ */
    const reviewModal = document.getElementById('reviewModal');

    if (reviewModal) {
        reviewModal.style.display = 'block';

        reviewModal.addEventListener('click', e => {
            if (!e.target.closest('.review-form')) {
                reviewModal.style.display = 'none';
            }
        });
    }


    /* ============================
     *           評価スター
     * ============================ */
    const stars = document.querySelectorAll('.star-img');
    const ratingInput = document.getElementById('rating');

    const onImg  = "{{ asset('images/star_active.png') }}";
    const offImg = "{{ asset('images/star_inactive.png') }}";

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.dataset.value;
            ratingInput.value = rating;

            stars.forEach(s => {
                s.src = s.dataset.value <= rating ? onImg : offImg;
            });
        });
    });

    chat.style.bottom = form.offsetHeight + 'px';

});
</script>
@endsection

