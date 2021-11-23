<?php


use core\controller\BaseController;
use dataimport\container\DataImportFormContainer;

class formSelectController extends BaseController {
    
    
    
    public function action_index() {
        $this->difContainer = object_container_create( DataImportFormContainer::class );
        
        
        return $this->render();
    }
    
    
}

