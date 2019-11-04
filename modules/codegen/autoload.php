<?php

// only available in debug-mode
if (!DEBUG)
    return;

$ctx = \core\Context::getInstance();

$ctx->enableModule('codegen');


hook_register_javascript('jstree', '/lib/jstree/jstree.min.js');
hook_register_css('jstree', '/lib/jstree/themes/default/style.min.css');

