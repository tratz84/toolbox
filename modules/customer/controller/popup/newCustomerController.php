<?php



use core\controller\BaseController;
use customer\forms\CompanyForm;
use customer\forms\PersonForm;
use customer\service\CompanyService;
use customer\service\PersonService;

class newCustomerController extends BaseController {
    
    
    
    public function action_index() {
        $this->companyForm = object_container_create(CompanyForm::class);
        $this->companyForm->disableSubmit();
        $this->companyForm->hideSubmitButtons();
        
        $this->personForm = object_container_create(PersonForm::class);
        $this->personForm->disableSubmit();
        $this->personForm->hideSubmitButtons();
        
        $this->showCompany = true;
        $this->showPerson = true;
        
        if (get_var('personOnly')) {
            $this->showCompany = false;
        }
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    
    public function action_save_company() {
        $companyService = object_container_create(CompanyService::class);
        
        $companyForm = object_container_create(CompanyForm::class);
        $companyForm->bind( $_REQUEST );
        
        
        if ($companyForm->validate() == false) {
            $r = array();
            $r['success'] = false;
            $r['error'] = true;
            $r['errors'] = array();
            
            foreach($companyForm->getErrors() as $key => $msg) {
                $r['errors'][] = [
                      'field'   => $key
                    , 'label'   => $companyForm->getWidget($key)->getLabel()
                    , 'message' => $msg
                ];
            }
            
            return $this->json( $r );
        }
        
        
        $companyId = $companyService->save($companyForm);
        
        return $this->json([
            'success'       => true,
            'company_id'    => $companyId,
            'customer_id'   => 'company-'.$companyId,
            'company_name'  => $companyForm->getWidgetValue('company_name'),
            'customer_name' => $companyForm->getWidgetValue('company_name')
        ]);
    }
    
    
    public function action_save_person() {
        $personService = object_container_create(PersonService::class);
        
        $personForm = object_container_create(PersonForm::class);
        $personForm->bind( $_REQUEST );
        
        
        if ($personForm->validate() == false) {
            $r = array();
            $r['success'] = false;
            $r['error'] = true;
            $r['errors'] = array();
            
            foreach($personForm->getErrors() as $key => $msg) {
                $r['errors'][] = [
                      'field'   => $key
                    , 'label'   => $personForm->getWidget($key)->getLabel()
                    , 'message' => $msg
                ];
            }
            
            return $this->json( $r );
        }
        
        
        $personId = $personService->save($personForm);
        
        $arr = $personForm->asArray();
        
        return $this->json([
            'success'       => true,
            'person_id'     => $personId,
            'customer_id'   => 'person-'.$personId,
            'person_name'   => format_personname( $arr ),
            'customer_name' => format_personname( $arr )
        ]);
    }
    
    
}





