<?php


use core\controller\BaseController;
use fastsite\service\WebformService;
use fastsite\model\Webform;
use fastsite\form\WebformForm;
use core\forms\TextField;
use core\forms\SelectField;
use core\forms\RadioField;
use core\forms\validator\EmailValidator;
use core\forms\validator\NotEmptyValidator;
use core\forms\validator\IbanValidator;
use core\forms\validator\NotFirstOptionValidator;
use core\forms\TextareaField;
use core\forms\EmailField;
use core\exception\InvalidStateException;

class webformsController extends BaseController {
    
    protected $fieldTypes = array();
    protected $validators = array();
    
    
    public function init() {
        $this->fieldTypes[] = array(
            'class' => TextField::class,
            'label' => 'Tekstregel'
        );
        $this->fieldTypes[] = array(
            'class' => TextareaField::class,
            'label' => 'Tekstveld (multi-line)'
        );
        $this->fieldTypes[] = array(
            'class' => EmailField::class,
            'label' => 'E-mail'
        );
        $this->fieldTypes[] = array(
            'class' => SelectField::class,
            'label' => 'Select-field'
        );
        $this->fieldTypes[] = array(
            'class' => RadioField::class,
            'label' => 'Radio buttons'
        );
        
        
        $this->validators[] = array(
            'class' => NotEmptyValidator::class,
            'label' => 'Waarde verplicht'
        );
        $this->validators[] = array(
            'class' => NotFirstOptionValidator::class,
            'label' => 'Eerste waarde niet toegestaan (radio/select veld)'
        );
        $this->validators[] = array(
            'class' => EmailValidator::class,
            'label' => 'E-mail validation'
        );
        $this->validators[] = array(
            'class' => IbanValidator::class,
            'label' => 'IBAN validation'
        );
    }
    
    
    public function action_index() {
        
        return $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $webformService = $this->oc->get(WebformService::class);
        
        $r = $webformService->searchForms($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    public function action_edit() {
        $webformService = $this->oc->get(WebformService::class);
        
        $webformId = get_var('id');
        
        if ($webformId) {
            $this->webform = $webformService->readWebform( $webformId );
        } else {
            $this->webform = new Webform();
        }
        
        $this->form = object_container_create(WebformForm::class);
        $this->form->bind( $this->webform );
        
        if (is_post()) {
            $this->form->bind($this->form);
            
            if ($this->form->validate()) {
//                 $webformService->saveWebform($this->form);
                
            }
        }
        
        
        $this->isNew = $this->webform->isNew();
        
        return $this->render();
    }
    
    public function action_load_widget() {
        
        $class = isset($this->class) ? $this->class : get_var('class');
        
        // lookup if requested widget exists
        $found = false;
        foreach($this->fieldTypes as $it) {
            if ($it['class'] == $class) {
                $found = true;
            }
        }
        
        if ($found == false) {
            throw new InvalidStateException('Field not found');
        }
        
        
        $fieldtype = substr($class, strrpos($class, '\\')+1);
        
        $f = module_file('fastsite', 'templates/webforms/fieldtype/'.$fieldtype.'.php');
        if (!$f) {
            $f = module_file('fastsite', 'templates/webforms/fieldtype/default.php');
        }
        
        $r = array();
        $r['success'] = true;
        $r['html'] = get_template($f, 
            array(
                'fieldtype' => $fieldtype,
                'validators' => $this->validators
            )
        );
        
        $this->json($r);
    }
    
    
    
    public function action_delete() {
        $webformService = $this->oc->get(WebformService::class);
        
        $webformService->deleteWebform( get_var('id') );
        
        redirect('/?m=fastsite&c=webforms');
    }
    
}

