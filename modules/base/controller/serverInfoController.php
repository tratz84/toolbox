<?php



use base\util\ServerInfoContainer;
use core\controller\BaseController;

class serverInfoController extends BaseController {
    
    
    public function action_index() {
        
        $this->sic = object_container_create(ServerInfoContainer::class);
        $this->sic->addInfo('Php version', PHP_VERSION);
        
        if (toolbox_html2pdf_available()) {
            $this->sic->addInfo('wkhtmltopdf', 'Ok');
        } else {
            $this->sic->addInfo('wkhtmltopdf', 'Not available', 'Unable to create PDF-files from HTML');
        }
        
        hook_eventbus_publish( $this->sic, 'base', 'ServerInfoContainer' );
        
        return $this->render();
    }
    
    
}

