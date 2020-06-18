<?php

use core\module\ModuleMeta;


$m = new ModuleMeta('projectModule', 'Projecten module',   'Tijdsregistratie voor projecten');
$m->addDependency( 'customer');

return $m;
