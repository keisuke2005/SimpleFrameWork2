<?php

namespace Backend\SsoAuth;
$c = new SsoAuthContainer;

// 以下名前空間を変更し、記述短縮
namespace Backend\Foundation\Bases;
Router::push(
    Route::def(
        Endpoint::def(HttpMethods::POST ,Path::def('/api/authenticate')),
        ClassAction::def($c,'Authenticate','authenticate')
    ),
    Route::def(
        Endpoint::def(HttpMethods::all() ,Path::def('/api/verify/code/authorization')),
        ClassAction::def($c,'Authenticate','verifyCodeAuthorization')
    ),
    Route::def(
        Endpoint::def(HttpMethods::all() ,Path::def('/api/verify/token/access')),
        ClassAction::def($c,'Authenticate','verifyTokenAccess')
    ),
    Route::def(
        Endpoint::def(HttpMethods::all() ,Path::def('/api/verify/token/refresh')),
        ClassAction::def($c,'Authenticate','verifyTokenRefresh')
    ),
);
