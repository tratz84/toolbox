<?php



use core\controller\BaseController;
use codegen\form\MenuGeneratorForm;
use codegen\form\MenuGeneratorItemForm;

class menugeneratorController extends BaseController {
    
    
    
    public function action_index() {
        $modules = module_list();
        
        
        $this->list = array();
        foreach($modules as $key => $path) {
            if (file_exists($path.'/config/codegen/menu.php') == false) continue;
            
            $this->list[] = array(
                'module_name' => $key
            );
        }
        
        
        return $this->render();
    }
    
    
    public function action_edit() {
        $this->form = new MenuGeneratorForm();
        
        if (is_get() && get_var('mod')) {
            $f = module_file_safe(get_var('mod'), '/config/codegen', 'menu.php');
            
            if ($f) {
                $data = include $f;
                $this->form->bind( $data );
            }
        }
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
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
                
                $formfile = 'menu.php';
                
                $data = $this->form->asArray();
                $data['menu'] = json_decode( $data['treedata'], true );
                file_put_contents($f.'/config/codegen/'.$formfile, "<?php\n\nreturn ".var_export($data, true) . ";\n\n");
                
                redirect('/?m=codegen&c=menugenerator&a=edit&mod='.urlencode($module_name));
            }
        }
        
        
        return $this->render();
    }
    
    
    public function action_menu_properties() {
        $this->form = new MenuGeneratorItemForm();
        $this->form->bind($_REQUEST);
        
        $this->setShowDecorator( false );
        
        return $this->render();
    }
    
    
    
    public function action_delete() {
        
    }
    
    
}
