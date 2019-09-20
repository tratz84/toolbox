<?php


use core\controller\BaseController;
use fastsite\service\WebformService;
use fastsite\model\Webform;
use fastsite\form\WebformForm;
use core\forms\TextField;

class webformsController extends BaseController {
    
    protected $inputTypes = array();
    
    
    public function init() {
        $this->inputTypes[] = array(
            'class' => TextField::class,
            'name' => 'Textfield'
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
    
    
    public function action_delete() {
        $webformService = $this->oc->get(WebformService::class);
        
        $webformService->deleteWebform( get_var('id') );
        
        redirect('/?m=fastsite&c=webforms');
    }
    
}

