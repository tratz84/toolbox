<?php


use core\Context;
use core\module\ModuleMeta;

$ctx = Context::getInstance();

if ($ctx->isExperimental()) {
    return new ModuleMeta('filesyncModule', 'Filesync',   'Backup & delen bestanden');
}

