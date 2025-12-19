<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">

            <!-- トップページ遷移リンク -->
            <div class="header-logo">
                <a href="{{ route('items.index') }}" class="header-logo__link-index">
                    <img class="header-logo__image" src="{{ asset('images/logo.svg') }}" alt="Logo">
                </a>
            </div>

            <!-- 認証関連および取引チャット画面では表示しない要素 -->
            @unless (in_array(Route::currentRouteName(), ['login', 'register', 'verification.notice', 'items.trade']))

            <!-- 商品検索欄 -->
            <form method="GET" action="{{ route('items.index') }}" class="search-box">
                @if(!empty($tab) && $tab !== 'recommend')
                <input type="hidden" name="tab" value="{{ $tab }}">
                @endif
                <input class="search-box__input" id="search-box" type="text" name="keyword" value="{{ $keyword ?? '' }}" placeholder="なにをお探しですか？">
            </form>

            <!-- ナビゲーションリンク -->
            <div class="header-nav">

                <!-- ログイン・ログアウト -->
                @auth
                <form method="post" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button class="header-nav__button-logout nav-btn" type="submit">ログアウト</button>
                </form>
                @else
                <form method="get" action="{{ route('login') }}" class="login-form">
                    <button class="header-nav__button-login nav-btn" type="submit">ログイン</button>
                </form>
                @endauth

                <!-- マイページ遷移リンク -->
                <div class="header-nav__link-area">
                    <a href="{{ route('mypage.profile') }}" class="header-nav__link-mypage nav-btn">マイページ</a>
                </div>

                <!-- 出品ページ遷移リンク -->
                <a href="{{ route('items.create') }}" class="header-nav__link-create nav-btn">出品</a>
            </div>
            @endunless
        </div>
    </header>

    <main>
        @yield('content')
        @yield('js')
    </main>
</body>
</html>