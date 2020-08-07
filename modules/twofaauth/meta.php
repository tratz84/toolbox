<?php

// disallow module for demo-environment
if (ctx()->getContextName() == 'demo') {
    return;
}

return new core\module\ModuleMeta('twofaauth', '2-factorauth', '');
