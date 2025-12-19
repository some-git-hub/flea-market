@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase/address/edit.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">
    <form class="delivery-form__wrapper" action="{{ route('address.update', $item->id) }}" method="post">
        @csrf
        @method('PUT')
        <h1 class="delivery-form__heading">
            住所の変更
        </h1>
        <div class="delivery-form__container">
            <div class="delivery-form__inner">
                <label class="delivery-form__label">
                    郵便番号
                </label>
                <div class="delivery-form__input-area">
                    <input class="delivery-form__input" type="text" maxlength="8" name="postal_code" value="{{ old('postal_code', session("checkout_postal_code_{$user->id}_{$item->id}", $user->address?->postal_code)) }}">
                </div>
                @error('postal_code')
                <div class="delivery-form__error-message">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        <div class="delivery-form__container">
            <div class="delivery-form__inner">
                <label class="delivery-form__label">
                    住所
                </label>
                <div class="delivery-form__input-area">
                    <input class="delivery-form__input" type="text" maxlength="255" name="address" value="{{ old('address', session("checkout_address_{$user->id}_{$item->id}", $user->address?->address)) }}">
                </div>
                @error('address')
                <div class="delivery-form__error-message">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        <div class="delivery-form__container">
            <div class="delivery-form__inner">
                <label class="delivery-form__label">
                    建物名
                </label>
                <div class="delivery-form__input-area">
                    <input class="delivery-form__input" type="text" maxlength="255" name="building" value="{{ old('building', session("checkout_building_{$user->id}_{$item->id}", $user->address?->building)) }}">
                </div>
                @error('building')
                <div class="delivery-form__error-message">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        <div class="address-edit__button-area">
            <button type="submit" class="address-edit__button-submit">更新する</button>
        </div>
    </form>
</div>
@endsection