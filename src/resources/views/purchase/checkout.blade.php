@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase/checkout.css') }}" />
@endsection

@section('content')
<div class="all__wrapper">
    <h1 class="purchase-process__title">購入手続き</h1>
    <div class="purchase-info">
        <div class="item-info">
            <div class="item-info__image-area">
                <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}" class="item-image">
            </div>
            <div class="item-info__detail">
                <h2 class="item-info__name">
                    {{ $item->name }}
                </h2>
                <p class="item-info__price">
                    ￥<span class="item-info__price-number">{{ number_format($item->price) }}</span>
                </p>
            </div>
        </div>
        <div class="payment-method">
            <h2 class="payment-method__title">支払い方法</h2>
            @if($item->price > 300000)
            <p class="payment-method__note">※ この商品は30万円を超えるため、コンビニ支払いは選択できません。</p>
            @endif
            <div class="payment-method__select-area">
                <select class="payment-method__select" name="payment_method" id="payment_method_{{ $item->id }}">
                    <option value="" disabled {{ session("checkout_payment_method_{$user->id}_{$item->id}") ? '' : 'selected' }} hidden>選択してください</option>
                    @foreach (config('const.payment.methods') as $key => $label)
                    <option value="{{ $key }}" class="payment-method__option" {{ session("checkout_payment_method_{$user->id}_{$item->id}") == $key ? 'selected' : '' }} @if($key == 1 && $item->price > 300000) disabled @endif>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
            @error('payment_method')
            <div class="purchase-info__error-message">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="delivery-address">
            <div class="delivery-address__inner">
                <h2 class="delivery-address__title">
                    配達先
                </h2>
                <a href="{{ route('address.edit', $item->id) }}" class="delivery-address__link-address-edit">
                    変更する
                </a>
            </div>
            <div class="delivery-address__contents">
                <p class="delivery-address__postal-code">〒{{ session("checkout_postal_code_{$user->id}_{$item->id}", $user->address?->postal_code) }}</p>
                <p class="delivery-address__address-building">
                    <span class="delivery-address__address">{{ session("checkout_address_{$user->id}_{$item->id}", $user->address?->address) }}</span>
                    <span class="delivery-address__building">{{ session("checkout_building_{$user->id}_{$item->id}", $user->address?->building) }}</span>
                </p>
            </div>
            @error('delivery')
            <div class="purchase-info__error-message">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="confirm-table">
        <table class="confirm-table__wrapper">
            <tr class="confirm-table__row-price">
                <th class="confirm-table__label">
                    商品代金
                </th>
                <td class="confirm-table__value">
                    <span class="confirm-table__price-yen">￥</span>{{ number_format($item->price) }}
                </td>
            </tr>
            <tr class="confirm-table__row-payment-method">
                <th class="confirm-table__label">
                    支払い方法
                </th>
                <td class="confirm-table__value" id="payment_method_text">未選択</td>
            </tr>
        </table>
        <form action="{{ route('purchase.store', $item->id) }}" method="post" class="purchase-form">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="hidden" name="payment_method" id="payment_method_hidden" value="{{ old('payment_method', session("checkout_payment_method_{$user->id}_{$item->id}", 0)) }}">
            <input type="hidden" name="postal_code" value="{{ old('postal_code', session("checkout_postal_code_{$user->id}_{$item->id}", $user->address?->postal_code)) }}">
            <input type="hidden" name="address" value="{{ old('address', session("checkout_address_{$user->id}_{$item->id}", $user->address?->address)) }}">
            <input type="hidden" name="building" value="{{ old('building', session("checkout_building_{$user->id}_{$item->id}", $user->address?->building)) }}">
            @if(Auth::check() && Auth::id() === $item->user_id)
                <button class="purchase-form__button-submit" disabled>購入不可</button>
                <p class="purchase-form__note">※ 自分の商品は購入できません</p>
            @elseif($item->status === 2 || $item->status === 3)
                <button class="purchase-form__button-submit" disabled>Sold</button>
            @elseif($item->status === 1)
                <button class="purchase-form__button-submit" disabled>取引中</button>
                <p class="purchase-form__note">※ 購入者からの入金待ちのため、現在は購入できません</p>
            @else
                <button class="purchase-form__button-submit" type="submit">購入する</button>
            @endif
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
const select = document.getElementById('payment_method_{{ $item->id }}');
const hiddenInput = document.querySelector('input[name="payment_method"]');
const textDisplay = document.getElementById('payment_method_text');
const labels = @json(config('const.payment.methods'));

select.addEventListener('change', function() {
    textDisplay.textContent = labels[this.value] || '未選択';
    hiddenInput.value = this.value;

    fetch("{{ route('purchase.savePaymentMethod', $item->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ payment_method: this.value })
    });
});

window.addEventListener('DOMContentLoaded', () => {
    const savedValue = hiddenInput.value;
    if (savedValue && labels[savedValue]) {
        textDisplay.textContent = labels[savedValue];
    }
});
</script>
@endsection