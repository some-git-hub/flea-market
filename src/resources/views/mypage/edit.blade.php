@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage/edit.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">

    <!-- プロフィールの編集 -->
    <form class="profile-form__wrapper" action="{{ isset($user->address) ? route('mypage.update') : route('mypage.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @if(isset($user->address))
            @method('PUT')
        @endif
        <h1 class="profile-form__heading">
            プロフィール設定
        </h1>

        <!-- プロフィール画像 -->
        <div class="profile-form__container-image">
            <div class="profile-form__image-area">
                <img id="preview" class="profile-form__image" src="{{ $previewSrc }}">
            </div>
            <div class="profile-form__inner-image">
                <div class="profile-form__input-area-image">
                    <label for="profile_image" class="profile-form__input-label">
                        画像を選択する
                    </label>
                    <input type="file" name="profile_image" id="profile_image" accept=".png,.jpeg" class="profile-form__input-image">
                </div>
                @error('profile_image')
                <div class="profile-form__error-message-image">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <!-- ユーザー名 -->
        <div class="profile-form__container">
            <label class="profile-form__label">
                ユーザー名
            </label>
            <div class="profile-form__inner">
                <div class="profile-form__input-area">
                    <input class="profile-form__input" type="text" maxlength="20" name="name" value="{{ old('name', $user->name) }}">
                </div>
                @error('name')
                <div class="profile-form__error-message">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <!-- 郵便番号 -->
        <div class="profile-form__container">
            <label class="profile-form__label">
                郵便番号
            </label>
            <div class="profile-form__inner">
                <div class="profile-form__input-area">
                    <input class="profile-form__input" type="text" maxlength="8" name="postal_code" value="{{ old('postal_code', $user->address?->postal_code) }}">
                </div>
                @error('postal_code')
                <div class="profile-form__error-message">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <!-- 住所 -->
        <div class="profile-form__container">
            <label class="profile-form__label">
                住所
            </label>
            <div class="profile-form__inner">
                <div class="profile-form__input-area">
                    <input class="profile-form__input" type="text" maxlength="255" name="address" value="{{ old('address', $user->address?->address) }}">
                </div>
                @error('address')
                <div class="profile-form__error-message">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <!-- 建物名 -->
        <div class="profile-form__container">
            <label class="profile-form__label">
                建物名
            </label>
            <div class="profile-form__inner">
                <div class="profile-form__input-area">
                    <input class="profile-form__input" type="text" maxlength="255" name="building" value="{{ old('building', $user->address?->building) }}">
                </div>
                @error('building')
                <div class="profile-form__error-message">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <!-- 更新ボタン -->
        <div class="profile-form__button-area">
            <button type="submit" class="profile-form__button-submit">更新する</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    document.getElementById('profile_image').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection