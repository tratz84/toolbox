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
        $this->sic->addInfo('ROOT-dir', ROOT);
        
        if (function_exists('posix_getpwuid')) {
            $posixUserinfo = posix_getpwuid( posix_getuid() );
            $this->sic->addInfo('Current user', $posixUserinfo['name'] );
        }
        else {
            // TODO: WINNT support
        }
        
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
        $this->sic->addInfo('php-curl', $curl?'Ok':'Not loaded', $curl?'':'extension not loaded');
        
        // check php-gd
        $gd = extension_loaded('gd');
        $this->sic->addInfo('php-gd', $gd?'Ok':'Not loaded', $gd?'':'extension not loaded');
        
        // check php-imagick
        $imagick = extension_loaded('imagick');
        $this->sic->addInfo('php-imagick', $imagick?'Ok':'Not loaded', $imagick?'':'extension not loaded');
        
        // check php-imagick
        $ext_zip = extension_loaded('zip');
        $this->sic->addInfo('php-zip', $ext_zip?'Ok':'Not loaded', $ext_zip?'':'extension not loaded');
        
        // check php-soap
        $ext_soap = extension_loaded('soap');
        $this->sic->addInfo('php-soap', $ext_soap?'Ok':'Not loaded', $ext_soap?'':'extension not loaded');
        
        // check php-xml (Xls writer uses this)
        $ext_xml = extension_loaded('xml');
        $this->sic->addInfo('php-xml', $ext_xml?'Ok':'Not loaded', $ext_xml?'':'extension not loaded');
        
        hook_eventbus_publish( $this->sic, 'base', 'ServerInfoContainer' );
        
        return $this->render();
    }
    
    
}

