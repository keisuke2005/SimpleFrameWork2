<?php

namespace Backend\SsoProxy;
$c = new SsoProxyContainer;

// 以下名前空間を変更し、記述短縮
namespace Backend\Foundation\Bases;

// Zabbix代理認証用
Router::push(
    Route::def(
        Endpoint::def(HttpMethods::GET ,Path::def('/systemMonitor/zabbix/login')),
        ClassAction::def($c,'ProxyZabbix','login')
    ),
    Route::def(
        Endpoint::def(HttpMethods::all(),Path::def('/systemMonitor/*')),
        ClassAction::def($c,'ProxyZabbix','access')
    )
);