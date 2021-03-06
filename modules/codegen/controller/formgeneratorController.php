<?php


use core\controller\BaseController;
use core\exception\FileException;
use codegen\form\FormGeneratorForm;
use core\exception\InvalidStateException;
use core\forms\CodegenBaseForm;

class formgeneratorController extends BaseController {
    
    /** @var FormGeneratorForm */
    protected $form;
    
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
                
                $form = slugify($this->form->getWidgetValue('form_name'));
                $formfile = 'form-'.$form.'.php';
                
                $data = $this->form->asArray();
                file_put_contents($f.'/config/codegen/'.$formfile, "<?php\n\nreturn ".var_export($data, true) . ";\n\n");
                
                $generator = new codegen\generator\FormGenerator();
                if ($generator->loadData( $module_name, $formfile )) {
                    $generator->generate();
                }
                
                report_user_message(t('Changes saved'));
                redirect( '/?m=codegen&c=formgenerator&fm='.urlencode($module_name).'&ff='.urlencode($formfile) );
            }
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
                    $data = include realpath($path . '/config/codegen/' . $f);
                    $this->forms[] = array(
                        'module' => $modulename,
                        'form_name' => $data['form_name'],
                        'path' => realpath($path . '/config/codegen/' . $f),
                        'file' => $f,
                        'short_description' => isset($data['short_description']) ? $data['short_description'] : ''
                    );
                }
            }
        }
        
        usort($this->forms, function($f1, $f2) {
            // first sort on module-name
            $c = strcmp($f1['module'], $f2['module']);
            if ($c != 0)
                return $c;
            
            // sort on form-name
            return strcmp($f1['form_name'], $f2['form_name']);
        });
        
        return $this->render();
    }
    
    
    
    public function action_select_widget() {
        
        $this->formWidgets = \codegen\generator\GeneratorHelper::getWidgets();
        
        $this->setShowDecorator( false );
        
        return $this->render();
    }
    
    
    public function action_widget_properties() {

        $form = new FormGeneratorForm();
        $editorClass = $form->getEditorClass( get_var('class') );
        
        if ($editorClass == null) {
            print 'Widget not found';
            print "<br/><input type=\"button\" onclick=\"delete_selected_widget();\" value=\"Delete\" />";
            exit;
        }
        
        
        $this->form = new $editorClass();
        $this->form->bind( $_REQUEST );
        
        
        $this->setShowDecorator( false );
        
        return $this->render();
    }
    
    public function action_example_form() {
        $data = @json_decode($_REQUEST['json_treedata']);
        
        try {
            $form = \core\forms\CodegenBaseForm::createForm( $data );
            
            print $form->render();
        } catch (\Exception $ex) {
            print "Error: " . $ex->getMessage();
        }
    }
    
    
    public function action_delete() {
        
        $generator = new codegen\generator\FormGenerator();
        $generator->delete( get_var('fm'), get_var('ff') );
        
        
        redirect('/?m=codegen&c=formgenerator&a=list');
    }
    
    
    
}

