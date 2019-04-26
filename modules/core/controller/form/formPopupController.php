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
        
        // TODO: check if formClassName ends with 'Form' ?
        
        $this->form = new $formClassName();
        if (is_a($this->form, BaseForm::class) == false) {
            throw new SecurityException('Non-form instantiated');
        }
        
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
        
        // TODO: check if formClassName ends with 'Form' ?
        
        $form = new $formClassName();
        if (is_a($form, BaseForm::class) == false) {
            throw new SecurityException('Non-form instantiated');
        }
        
        
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