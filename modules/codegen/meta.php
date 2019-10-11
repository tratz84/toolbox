<?php


use core\Context;
use core\module\ModuleMeta;

$ctx = Context::getInstance();

if ($ctx->isExperimental()) {
    return new ModuleMeta('codegenModule', 'Code generator',   'Code generator for views & forms');
}

