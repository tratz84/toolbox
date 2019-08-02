<?php


use core\Context;
use core\module\ModuleMeta;

$ctx = Context::getInstance();

if ($ctx->isExperimental()) {
    return new ModuleMeta('fastsiteModule', 'Fast site',   'Create websites');
}

