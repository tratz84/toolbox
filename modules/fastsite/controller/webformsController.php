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
    
    public function init() {

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
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $webform = $webformService->saveWebform($this->form);
                
                report_user_message('Wijzigingen opgeslagen');
                
                redirect('/?m=fastsite&c=webforms&a=edit&id='.$webform->getWebformId());
            }
        }
        
        
        $this->isNew = $this->webform->isNew();
        
        return $this->render();
    }
    
    public function action_load_widget() {

        $form = new WebformForm();
        
        $class = isset($this->class) ? $this->class : get_var('class');
        
        // lookup if requested widget exists
        $found = false;
        foreach($form->getWebformFieldTypes() as $it) {
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
                'class' => $class,
                'fieldtype' => $fieldtype,
                'validators' => $form->getWebformValidators()
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

