<?php

namespace Backend\SsoProxy;

require_once("core/SsoProxy.php");
require_once("core/SsoProxyContainer.php");

$c = new SsoProxyContainer;
SsoProxy::routing($c->instance("SsoProxy"));