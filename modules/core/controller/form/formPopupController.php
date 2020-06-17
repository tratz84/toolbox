<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\exception\SecurityException;
use core\forms\BaseForm;

class formPopupController extends BaseController {
    
    
    public function action_index() {
        
        if (isset($_REQUEST['formClass']) == false) {
            throw new InvalidStateException('formClass must be set');
        }
        
        $formClassName = $_REQUEST['formClass'];
        
        // check if formClassName is instance of BaseForm
        $ref = new ReflectionClass( $formClassName );
        if ($ref->isSubclassOf( BaseForm::class ) == false) {
            // TODO: blacklist user?
            throw new SecurityException('Non-form instantiated');
        }
        
        $this->form = new $formClassName();
        
        $fieldSet = $this->form->bind( $_REQUEST );
        
        $this->isNew = $fieldSet == 0 ? true : false;
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    
    public function action_validate() {
        if (isset($_REQUEST['formClass']) == false) {
            throw new InvalidStateException('formClass must be set');
        }
        
        $formClassName = $_REQUEST['formClass'];
        
        // check if formClassName is instance of BaseForm
        $ref = new ReflectionClass( $formClassName );
        if ($ref->isSubclassOf( BaseForm::class ) == false) {
            // TODO: blacklist user?
            throw new SecurityException('Non-form instantiated');
        }
        
        
        $form = new $formClassName();
        
        $form->bind( $_REQUEST );
        
        $arr = array();
        
        if ($form->validate()) {
            $arr['result'] = true;
        } else {
            $arr['result'] = false;
            $arr['errors'] = $form->getErrorsForJson();
        }
        
        $this->json($arr);
    }
    
    
}