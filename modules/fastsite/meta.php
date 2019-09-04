<?php

// standalone version only
if (is_standalone_installation() == false) {
    return;
}

use core\Context;
use core\module\ModuleMeta;

$ctx = Context::getInstance();

if ($ctx->isExperimental()) {
    return new ModuleMeta('fastsiteModule', 'Fast site',   'Create websites');
}

