<?php

namespace Backend\SsoPortal;

class Config
{
    const LOG_PATH = "/var/log/ra/opemng_ap.log";
    const MESSAGE_PATH = __DIR__."/message.ini";

    const APPLCATION_DIRECTORY = "/sso-portal";
    const URI_PREFIX = "/sso-portal";
    const LOGIN_URL = "/sso-auth/login";

}