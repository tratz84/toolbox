<?php


namespace dataimport\validator;


use customer\service\CompanyService;

class CompanyFormValidator extends DataImportFormValidator {
    
    
    public function validate() {
        $cn = $this->form->getWidgetValue('company_name');
        
        if ($cn == '') {
            return true;
        }
        
        $companyService = object_container_get( CompanyService::class );
        $r = $companyService->search(0, 1, ['eq_company_name' => $cn]);
        if ($r->getRowCount() > 0) {
            $this->addError( t('Duplicate company name') );
            
            return false;
        }
        
        return true;
    }
    
    
    
}

