<?php

// only available in debug-mode
if (!DEBUG)
    return;

$ctx = \core\Context::getInstance();

$ctx->enableModule('codegen');

