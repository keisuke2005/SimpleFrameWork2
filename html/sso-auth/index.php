<?php


namespace Backend\SsoAuth;

require_once("core/SsoAuth.php");
require_once("core/SsoAuthContainer.php");

$c = new SsoAuthContainer;
SsoAuth::routing($c->instance("SsoAuth"));