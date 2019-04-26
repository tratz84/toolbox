<?php



use core\module\ModuleMeta;

if (is_standalone_installation()) {
    $m = new ModuleMeta('admin', 'Admin functionaliteit', 'Exception-reporting');
    
    return $m;
}
