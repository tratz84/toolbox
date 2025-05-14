<?php


use core\module\ModuleMeta;

if (ctx()->isExperimental() || is_standalone_installation()) {
    return new ModuleMeta('mollie', 'Mollie',   'Afhandeling betalingen');
}

