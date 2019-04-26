<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\exception\SecurityException;
use core\forms\ListEditWidget;

class formListEditController extends BaseController {
    
    
    
    public function action_index() {
        if (isset($_REQUEST['formClass']) == false) {
            throw new InvalidStateException('formClass must be set');
        }
        
        $formClassName = $_REQUEST['formClass'];
        
        // TODO: check if formClassName ends with 'Form' ?
        
        $listEditWidget = new $formClassName();
        if (is_a($listEditWidget, ListEditWidget::class) == false) {
            throw new SecurityException('Non-form instantiated');
        }
        
        
        
        $this->setShowDecorator(false);
        
        print $listEditWidget->renderRow();
    }
    
    
}
