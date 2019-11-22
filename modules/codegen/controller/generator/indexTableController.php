<?php

use core\controller\BaseController;
use codegen\form\IndexTableForm;


class indexTableController extends BaseController {


    public function action_index() {
        $modules = module_list();
        
        $this->list = array();
        foreach($modules as $key => $path) {
            $files = list_files($path.'/config/codegen');
            
            if ($files) foreach($files as $f) {
                if (strpos($f, 'indextable-controller-') !== 0 || endsWith($f, 'controller.php') == false) continue;
                
                $data = include $path.'/config/codegen/' . $f;
                
                $this->list[] = array(
                    'module_name' => $key,
                    'controller_name' => $data['controller_name'],
                    'file' => $f
                );
            }
        }
        
        
        return $this->render();
    }


	public function action_edit() {
        $this->form = new IndexTableForm();
        
        if (is_get() && get_var('fm')) {
            $f = module_file_safe(get_var('fm'), '/config/codegen', get_var('ff'));
            
            if ($f) {
                $data = include $f;
                $this->form->bind( $data );
            }
        }
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
//             var_export($_REQUEST);exit;
//             var_export($this->form->asArray());exit;
            
            if ($this->form->validate()) {
                
                // save
                $module_name = $this->form->getWidgetValue('module_name');
                $f = module_file($module_name, '/');
                if ($f === false) {
                    throw new InvalidStateException('Module not found');
                }
                if (file_exists($f . '/config/codegen') == false) {
                    if (mkdir($f . '/config/codegen', 0755, true) == false) {
                        throw new FileException('Unable to create save-dir');
                    }
                }
                
                $controller_name = $this->form->getWidgetValue('controller_name');
                $formfile = 'indextable-controller-'.slugify($controller_name).'.php';
                
                $data = $this->form->asArray();
                file_put_contents($f.'/config/codegen/'.$formfile, "<?php\n\nreturn ".var_export($data, true) . ";\n\n");
                
                
                report_user_message('IndexTable page saved');
                
                // TODO: redirect
                redirect('/?m=codegen&c=generator/indexTable&a=edit&fm='.urlencode($module_name).'&ff='.urlencode($formfile));
            }
            
        }
        
        $this->isNew = true;

		$this->render();
	}


	public function action_delete() {


	}


	
}

