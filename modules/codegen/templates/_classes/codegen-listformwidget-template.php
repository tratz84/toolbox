<?= "<?php" ?>


namespace <?= $namespace ?>;


class ListInvoiceLineWidget extends \core\forms\ListFormWidget {
    
    public function __construct() {
        parent::__construct('<?= $formClass ?>', 'objectList');
        
        $this->codegen();
    }
    
    public function codegen() {
    
    }
    
}

