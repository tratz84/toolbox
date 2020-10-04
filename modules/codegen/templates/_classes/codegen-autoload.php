<?= "<?php" ?>




ctx()->enableModule(<?= var_export($module_code, true) ?>);

hook_loader(__DIR__.'/hook/');



