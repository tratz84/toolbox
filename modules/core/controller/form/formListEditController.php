<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\exception\SecurityException;
use core\forms\ListEditWidget;
use core\forms\ListEditDivWidget;

class formListEditController extends BaseController {
    
    
    
    public function action_index() {
        if (isset($_REQUEST['formClass']) == false) {
            throw new InvalidStateException('formClass must be set');
        }
        
        $formClassName = $_REQUEST['formClass'];
        
        // check if formClassName is instance of ListEditWidget
        $ref = new ReflectionClass( $formClassName );
        if ($ref->isSubclassOf( ListEditWidget::class ) == false && $ref->isSubclassOf( ListEditDivWidget::class ) == false) {
            // TODO: blacklist user?
            throw new SecurityException('Non-form instantiated');
        }
        
        $listEditWidget = new $formClassName();
        
        
        
        $this->setShowDecorator(false);
        
        print $listEditWidget->renderRow();
    }
    
    
}
