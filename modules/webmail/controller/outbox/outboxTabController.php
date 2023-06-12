<?php



use core\controller\BaseController;
use webmail\form\WebmailOutboxIndexTable;

class outboxTabController extends BaseController {
    
    public function action_index() {
        
        $this->wo_it = new WebmailOutboxIndexTable();
        $this->wo_it->setRenderLoad( false );
        $this->wo_it->setOption('autoloadNext', true);
        $this->wo_it->setOption('fixedHeader', true);
        
        
        if (isset($this->companyId) && (int)$this->companyId) {
            $this->wo_it->setCompanyId( (int)$this->companyId );
        }
        else if (isset($this->personId) && (int)$this->personId) {
            $this->wo_it->setPersonId( (int)$this->personId );
        }
        else if (isset($this->connectorUrl)) {
            $this->wo_it->setConnectorUrl( $this->connectorUrl );
        }
        else {
            return;
        }
        
        return $this->render();
    }
    
    
}


