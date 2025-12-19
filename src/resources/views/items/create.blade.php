@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/create.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">
    <form action="{{ route('items.store') }}" class="create-form" method="post" enctype="multipart/form-data">
        @csrf
        <h1 class="create-form__heading">
            商品の出品
        </h1>
        <div class="item-image__container">
            <h2 class="item-image__title">
                商品画像
            </h2>
            <div class="item-image__inner">
                <div class="item-image__input-area">
                    <label for="item_image" class="item-image__input-label" id="item_image_label">
                        画像を選択する
                    </label>
                    <input type="file" name="item_image" id="item_image" class="item-image__input" accept=".png,.jpeg">
                </div>
                <div class="item-image__preview" id="item_image_preview"></div>
                <button type="button" id="item_image_clear" class="item-image__button-clear">
                    画像を削除する
                </button>
                @error('item_image')
                <div class="create-form__error-message-image">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        <div class="create-form__wrapper">
            <h2 class="item-detail__title">
                商品の詳細
            </h2>
            <div class="item-detail__container">
                <h3 class="item-category__title">
                    カテゴリー
                </h3>
                <div class="item-category__inner">
                    <div class="item-category__checkbox-area">
                        @foreach($categories as $category)
                        <input type="checkbox" id="category-{{ $category->id }}" class="item-category__checkbox" name="category[]" value="{{ $category->id }}"
                            {{ in_array($category->id, old('category', isset($item) ? $item->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                        <label for="category-{{ $category->id }}" class="item-category__label">
                            {{ $category->name }}
                        </label>
                        @endforeach
                    </div>
                    @error('category')
                    <div class="create-form__error-message">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="item-detail__container">
                <h3 class="item-condition__title">
                    商品の状態
                </h3>
                <div class="item-condition__inner">
                    <div class="item-condition__select-area">
                        <select name="condition" class="item-condition__select">
                            <option value="" disabled selected hidden>選択してください</option>
                            @foreach (config('const.item.conditions') as $key => $label)
                            <option value="{{ $key }}" class="item-condition__option" {{ old('condition', $item->condition ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @error('condition')
                    <div class="create-form__error-message">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="create-form__wrapper">
            <h2 class="item-detail__title">
                商品名と説明
            </h2>
            <div class="item-detail__container">
                <h3 class="item-name__title">
                    商品名
                </h3>
                <div class="item-name__inner">
                    <div class="item-name__input-area">
                        <input type="text" maxlength="255" name="name" class="item-name__input" value="{{ old('name', $item->name ?? '') }}">
                    </div>
                    @error('name')
                    <div class="create-form__error-message">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="item-detail__container">
                <h3 class="item-brand__title">
                    ブランド名
                </h3>
                <div class="item-brand__inner">
                    <div class="item-brand__input-area">
                        <input type="text" maxlength="255" name="brand" class="item-brand__input" value="{{ old('brand', $item->brand ?? '') }}">
                    </div>
                    @error('brand')
                    <div class="create-form__error-message">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="item-detail__container">
                <h3 class="item-description__title">
                    商品の説明
                </h3>
                <div class="item-description__inner">
                    <div class="item-description__input-area">
                        <textarea maxlength="255" name="description" class="item-description__input">{{ old('description', $item->description ?? '') }}</textarea>
                    </div>
                    @error('description')
                    <div class="create-form__error-message">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="item-detail__container">
                <h3 class="item-price__title">
                    販売価格
                </h3>
                <div class="item-price__inner">
                    <div class="item-price__input-area">
                        <input type="text" name="price" class="item-price__input" value="{{ old('price', $item->price ?? '') }}">
                    </div>
                    @error('price')
                    <div class="create-form__error-message">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="create-form__button-area">
            <button type="submit" class="create-form__button-submit">出品する</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const input    = document.getElementById('item_image');
    const preview  = document.getElementById('item_image_preview');
    const label    = document.getElementById('item_image_label');
    const clearBtn = document.getElementById('item_image_clear');

    input.addEventListener('change', function(event) {
        const file = event.target.files[0];

        if (file) {
            label.style.display = 'none';
            preview.innerHTML = '';

            const img = document.createElement('img');
            img.src             = URL.createObjectURL(file);
            img.alt             = "選択された画像";
            img.style.maxWidth  = "200px";
            img.style.maxHeight = "200px";
            img.style.display   = "block";
            img.style.marginTop = "10px";

            preview.appendChild(img);

            clearBtn.style.display = "inline-block";
        }
    });

    clearBtn.addEventListener('click', function() {
        input.value = "";
        preview.innerHTML = "";
        label.style.display = 'inline-block';
        clearBtn.style.display = "none";
    });
});
</script>
@endsection