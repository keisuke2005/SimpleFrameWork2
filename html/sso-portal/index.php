<?php

namespace Backend\SsoPortal;

require_once("core/SsoPortal.php");
require_once("core/SsoPortalContainer.php");

$c = new SsoPortalContainer;
SsoPortal::routing($c->instance("SsoPortal"));
