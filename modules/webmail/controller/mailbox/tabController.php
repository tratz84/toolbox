<?php


use base\model\Company;
use base\model\Person;
use base\service\CompanyService;
use core\controller\BaseController;

class tabController extends BaseController {
    
    
    public function action_index() {
        
        // Company?
        if (isset($this->companyId) && $this->companyId) {
            $mailtabSettings = object_meta_get(Company::class, $this->companyId, 'mailtab-settings');
            
            if (!$mailtabSettings) {
                $mailtabSettings = array();
                $mailtabSettings['email'] = array();
                
                $companyService = object_container_get(CompanyService::class);
                $company = $companyService->readCompany( $this->companyId );
                
                foreach($company->getEmailList() as $e) {
                    $mailtabSettings['email'][] = $e->getEmailAddress();
                }
            }
        }

        // TODO: Person?
        if (isset($this->personId) && $this->personId) {
            $mailtabSettings = object_meta_get(Person::class, $this->personId, 'mailtab-settings');
            
        }
        
        $this->mailtabSettings = $mailtabSettings;
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    
    public function action_settings() {
        
        
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
}

