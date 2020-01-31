<?php

use core\controller\BaseController;

class tabOverviewController extends BaseController {
    
    
    
    public function action_index() {
        
        if (isset($this->companyId) && $this->companyId)
            $this->params['company_id'] = $this->companyId;
        if (isset($this->personId) && $this->personId)
            $this->params['person_id'] = $this->personId;
        
        
        if (count($this->params)) {
            $this->params['exploded'] = true;
            $this->setShowDecorator(false);
            $this->render();
        }
    }
    
}

