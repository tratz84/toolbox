<?php

if (ctx()->isExperimental()) {
    return new core\module\ModuleMeta('securemail', 'Secure Mail', 'Beveiligd berichten versturen');
}
