<?php

use core\Context;
use core\module\ModuleMeta;

$ctx = Context::getInstance();

if ($ctx->isExperimental()) {
    return new ModuleMeta('projectModule', 'Projecten module',   'Tijdsregistratie voor projecten');
}

