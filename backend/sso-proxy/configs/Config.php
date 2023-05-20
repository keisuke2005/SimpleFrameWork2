<?php

namespace Backend\SsoProxy;

class Config
{
    const LOG_PATH = "/var/log/ra/opemng_ap.log";
    const MESSAGE_PATH = __DIR__."/message.ini";

    const APPLCATION_DIRECTORY = "/sso-proxy";
    const URI_PREFIX = "/sso-proxy";
    const LOGIN_URL = "/sso-auth/login.html";


    //const SYSTEM_MONNITOR_DOMAIN = 'https://153.127.41.38';
    


    const SYSTEM_MONNITOR_SCHEME = 'https://';
    const SYSTEM_MONNITOR_DOMAIN = '153.127.41.38';
    const SYSTEM_MONNITOR_PORT = '';
    const SYSTEM_MONNITOR_BASEURI = '/sso-proxy/systemMonitor';
    const SYSTEM_MONITOR_LOGIN_URL = '/zabbix/index.php';
    const SYSTEM_MONITOR_LOGIN_USER = 'Admin';
    const SYSTEM_MONITOR_LOGIN_PSWD = 'p@ssw0rd';


}