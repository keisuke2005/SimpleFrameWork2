<?php

namespace Backend\SsoAuth;
$c = new SsoAuthContainer;

// 以下名前空間を変更し、記述短縮
namespace Backend\Foundation\Bases;

// 画面リソース
Router::push(
    // ログイン画面
    Route::def(
        Endpoint::def(HttpMethods::GET ,Path::def('/login')),
        StaticPageAction::def(Path::def('/webroot/login.html'))
    ),
);