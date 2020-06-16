<?= "<?php" ?>


namespace <?= $namespace ?>;


class <?= $classname ?> extends \core\forms\ListFormWidget {
    
    public function __construct() {
        parent::__construct(<?= var_export($formClass, true) ?>, 'objectList');
        
        $this->codegen();
    }
    
    public function codegen() {
    
    }
    
}

