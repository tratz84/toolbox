<?php


use core\controller\BaseController;
use core\exception\FileException;
use codegen\form\FormGeneratorForm;
use core\exception\InvalidStateException;
use core\forms\CodegenBaseForm;

class formgeneratorController extends BaseController {
    
    
    public function action_index() {
        $this->form = new FormGeneratorForm();
        
        if (is_get() && get_var('fm') && get_var('ff')) {
            $form_module = get_var('fm');
            $form_file = get_var('ff');
            
            $codegen_path = module_file($form_module, '/config/codegen');
            $fullpath = realpath($codegen_path. '/' . $form_file);
            
            if ($fullpath === false) {
                throw new FileException('File not found');
            }
            if (strpos($fullpath, $codegen_path) !== 0) {
                throw new FileException('Invalid location');
            }
            
            $formdata = include $fullpath;
            $this->form->bind($formdata);
        }
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            $this->form->validate();
            
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
            
            $form = slugify($_REQUEST['form_name']);
            $formfile = 'form-'.$form.'.php';
            file_put_contents($f.'/config/codegen/'.$formfile, "<?php\n\nreturn ".var_export($_REQUEST, true) . ";\n\n");
            
            redirect( '/?m=codegen&c=formgenerator&fm='.urlencode($module_name).'&ff='.urlencode($formfile) );
        }
        
        return $this->render();
    }
    
    
    public function action_list() {
        
        $modules = module_list();
        $this->forms = array();
        foreach($modules as $modulename => $path) {
            $files = list_files($path . '/config/codegen/');
            if ($files) foreach($files as $f) {
                if (strpos($f, 'form-') === 0 && strpos($f, '.php') !== false) {
                    $this->forms[] = array(
                        'module' => $modulename,
                        'path' => realpath($path . '/config/codegen/' . $f),
                        'file' => $f
                    );
                }
            }
        }
        
        return $this->render();
    }
    
    
    
    public function action_select_widget() {
        $form = new FormGeneratorForm();
        $this->formWidgets = $form->getFormWidgets();
        
        $this->setShowDecorator( false );
        
        return $this->render();
    }
    
    
    public function action_widget_properties() {

        $form = new FormGeneratorForm();
        $editorClass = $form->getEditorClass( get_var('class') );
        
        if ($editorClass == null)
            die('Widget not found');
        
        
        $this->form = new $editorClass();
        $this->form->bind( $_REQUEST );
        
        
        $this->setShowDecorator( false );
        
        return $this->render();
    }
    
    public function action_example_form() {
        $data = @json_decode($_REQUEST['json_treedata']);
        
        $form = \core\forms\CodegenBaseForm::createForm( $data );
        
        print $form->render();
    }
    
    
    
    public function action_test() {
        $d = <<<DATA
[{"type":"container","text":"container","data":{"type":"container","class":"core\\\\forms\\\\WidgetContainer","label":"container","name":"container"},"children":[{"type":"widget","text":"firstname: Firstname","data":{"class":"core\\\\forms\\\\TextField","label":"Firstname","type":"widget","name":"firstname"}},{"type":"widget","text":"lastname: Lastname","data":{"class":"core\\\\forms\\\\CheckboxField","editor":"codegen\\\\form\\\\widgetoptions\\\\CheckboxOptionsForm","label":"Lastname","type":"widget","name":"lastname"}}]},{"type":"widget","text":"Color picker","data":{"class":"core\\\\forms\\\\ColorPickerField","label":"Color picker","type":"widget"}},{"type":"widget","text":"Checkbox","data":{"class":"core\\\\forms\\\\CheckboxField","editor":"codegen\\\\form\\\\widgetoptions\\\\CheckboxOptionsForm","label":"Checkbox","type":"widget"}}]
DATA;
//         print $d;exit;
        $x = json_decode( $d );

//         var_export( $x );
        
        $form = \core\forms\CodegenBaseForm::createForm( $x );
        
    }
    
    
    
}

