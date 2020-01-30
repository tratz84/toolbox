<?php


use core\controller\BaseController;

class usercapabilityController extends BaseController {
    
    
    
    public function action_index() {
        $modules = module_list();
        
        $this->capabilities = array();
        foreach($modules as $moduleName => $path) {
            $f = module_file($moduleName, '/config/usercapabilities.php');
            if ($f) {
                $this->capabilities[] = array(
                    'name' => $moduleName
                );
            }
        }
        
        
        return $this->render();
    }
    
    
    public function action_edit() {
        
        $this->form = new \codegen\form\ModuleUserCapabilityForm();
        
        
        if (is_get()) {
            if (get_var('mod')) {
                $this->form->getWidget('module_name')->setValue( get_var('mod') );
                
                $data = array();
                $data['module_name'] = get_var('mod');
                $f = module_file(get_var('mod'), '/config/usercapabilities.php');
                if ($f) {
                    $data['capabilities'] = include $f;
                }
                
                $this->form->bind($data);
            }
        }
        
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            
            if ($this->form->validate()) {
                
                $module_name = $this->form->getWidgetValue('module_name');
                $formdata = $this->form->asArray();
                
                codegen_save_config($module_name, 'usercapabilities.php', $formdata['capabilities']);
                
                report_user_message('Changes saved');
                redirect('/?m=codegen&c=config/usercapability&a=edit&mod='.$module_name);
            }
            
        }
        
        
        return $this->render();
    }
    
}
