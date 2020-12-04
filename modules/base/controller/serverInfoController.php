<?php



use base\util\ServerInfoContainer;
use core\controller\BaseController;

class serverInfoController extends BaseController {
    
    
    public function action_index() {
        
        $this->sic = object_container_create(ServerInfoContainer::class);
        
        // base info
        $this->sic->addInfo('Php version', PHP_VERSION);
        $this->sic->addInfo('Operating system', php_uname());
        $this->sic->addInfo('Memory limit', ini_get('memory_limit'));
        $this->sic->addInfo('Max execution time (Time limit)', ini_get('max_execution_time'));
        
        // wkhtmltopdf is used for archiving in webmail-mod
        if (toolbox_html2pdf_available()) {
            $this->sic->addInfo('wkhtmltopdf', 'Ok');
        } else {
            $this->sic->addInfo('wkhtmltopdf', 'Not available', 'Unable to create PDF-files from HTML');
        }
        
        // report PHP_FD_SETSIZE for max. open files + connections
        $this->sic->addInfo('PHP_FD_SETSIZE '.infopopup('max open files + connections'), PHP_FD_SETSIZE );
        
        // check php-curl
        $curl = extension_loaded('curl');
        $this->sic->addInfo('php-curl', $curl?'Ok':'Not loaded', 'extension not loaded');
        
        // check php-gd
        $gd = extension_loaded('gd');
        $this->sic->addInfo('php-gd', $gd?'Ok':'Not loaded', $gd?'':'extension not loaded');
        
        // check php-imagick
        $imagick = extension_loaded('imagick');
        $this->sic->addInfo('php-imagick', $imagick?'Ok':'Not loaded', $imagick?'':'extension not loaded');
        
        hook_eventbus_publish( $this->sic, 'base', 'ServerInfoContainer' );
        
        return $this->render();
    }
    
    
}

