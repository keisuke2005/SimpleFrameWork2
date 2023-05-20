<?php

namespace Backend\SsoPortal;
$c = new SsoPortalContainer;

// 以下名前空間を変更し、記述短縮
namespace Backend\Foundation\Bases;

// 共通API
Router::push(
    Route::def(
        Endpoint::def(HttpMethods::GET ,Path::def('/api/portal/info')),
        ClassAction::def($c,'Portal','getInfo')
    )
);

