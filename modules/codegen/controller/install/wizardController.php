<?php


use core\controller\BaseController;
use codegen\form\install\ConfigCreatorForm;

class wizardController extends BaseController {
    
    public function init() {
        if (is_installation_mode() == false) {
            die('Not in installation mode');
        }
    }
    
    
    public function action_index() {
        
        $this->form = new ConfigCreatorForm();
        
        if (is_get()) {
            $this->form->getWidget('db_host')->setValue( 'localhost' );
            $this->form->getWidget('api_key')->setValue( strtoupper(md5(rand().rand().rand().rand().rand().rand())) );
            $this->form->getWidget('base_href')->setValue( substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/')+1) );
            $this->form->getWidget('data_dir')->setValue( ROOT . DIRECTORY_SEPARATOR . 'data' );
        }
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            if ($this->form->validate()) {
                
                // generate config-local.php & create tables
                $this->form->doInstall();
            }
        }
        
        
        return $this->render();
    }
    
}