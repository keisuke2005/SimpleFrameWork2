<?php

namespace Backend\SsoAuth;

class Config
{
    const LOG_PATH = "/var/log/ra/opemng_ap.log";
    const APPLCATION_DIRECTORY = "/sso-auth";
    const URI_PREFIX = "/sso-auth";

    const DB_TYPE = 'pgsql';
    const DB_HOST = '192.168.56.122';
    const DB_PORT = '5432';
    const DB_NAME = 'sso';
    const DB_USER = 'ssoadmin';
    const DB_PSWD = 'ssoadmin123!';

    const AUTHORIZATION_CODE_EXPIRES = 300;
    const ACCESS_TOKEN_EXPIRES = 3600;
    const REFRESH_TOKEN_EXPIRES = 2592000;
}