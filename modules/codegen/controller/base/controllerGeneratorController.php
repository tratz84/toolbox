<?php



use core\controller\BaseController;
use codegen\form\ControllerGeneratorForm;

class controllerGeneratorController extends BaseController {
    
    
    public function action_index() {
        
        $this->form = new ControllerGeneratorForm();
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                // generate! :)
                $g = new \codegen\generator\ControllerGenerator();
                $g->setModuleName( $this->form->getWidgetValue('module_name') );
                $g->setControllerName( $this->form->getWidgetValue('controller_name') );
                
                $default_actions = explode("\n", $this->form->getWidgetValue('default_actions'));
                foreach($default_actions as $a) {
                    $g->addAction($a);
                }
                
                $g->generate();
                
                report_user_message('Controller generated');
                redirect('/?m=codegen&c=menu');
            }
        }
        
        
        
        return $this->render();
    }
    
    
}