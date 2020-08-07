<?php


use core\module\ModuleMeta;

$m = new ModuleMeta('invoice', 'Facturatie/Order module',   'Maken van facturen danwel orders', 40);
$m->addDependency('customer');

return $m;

