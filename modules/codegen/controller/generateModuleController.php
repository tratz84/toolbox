<?php


use core\controller\BaseController;
use codegen\form\GenerateModuleForm;

class generateModuleController extends BaseController {
    
    
    public function action_index() {
        $this->form = new GenerateModuleForm();
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $this->generateModule($this->form);
                report_user_message('Module generated');
                redirect('/?m=codegen&c=menu');
            }
        }
        
        return $this->render();
    }
    
    
    protected function generateModule($form) {
        
        $module_dir = $form->getWidgetValue('module_dir');
        $module_code = $form->getWidgetValue('module_code');
        $module_name = $form->getWidgetValue('module_name');
        $module_desc = $form->getWidgetValue('module_desc');
        
        mkdir($module_dir . '/' . $module_code);
        mkdir($module_dir . '/' . $module_code . '/config');
        mkdir($module_dir . '/' . $module_code . '/controller');
        mkdir($module_dir . '/' . $module_code . '/lib');
        mkdir($module_dir . '/' . $module_code . '/templates');
        
        file_put_contents($module_dir . '/' . $module_code . '/autoload.php', "<?php\n\n");
        
        file_put_contents($module_dir . '/' . $module_code . '/meta.php', "<?php\n\nreturn new core\\module\\ModuleMeta(".var_export($module_code, true).", ".var_export($module_name, true).", ".var_export($module_desc, true).");\n");
        
    }
    
    
}
