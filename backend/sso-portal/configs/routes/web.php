<?php

namespace Backend\SsoPortal;
$c = new SsoPortalContainer;

// 以下名前空間を変更し、記述短縮
namespace Backend\Foundation\Bases;

// ルート定義
Router::push(
    // ポータル
    Route::def(
        Endpoint::def(HttpMethods::GET ,Path::def('/index')),
        StaticPageAction::def(Path::def('/webroot/index.html'))
    )->name("pt-index")
);

// スコープ定義
Router::scopes(
    Scope::def("Admin")->allow([
        "pt-index"
    ])
);