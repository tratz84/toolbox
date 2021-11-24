<?php


namespace dataimport\validator;


use customer\service\CompanyService;

class CompanyFormValidator extends DataImportFormValidator {
    
    protected $companyGroups = null;
    
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
        
        // cashbackinvoice module enabled? => check company_group_id
        if (ctx()->isModuleEnabled('cashbackinvoice')) {
            // fetch groups
            if ($this->companyGroups == null) {
                $this->companyGroups = array();
                
                $cgService = object_container_get( '\\cashbackinvoice\\service\\CompanyGroupService' );
                foreach( $cgService->readAllGroups() as $g ) {
                    $this->companyGroups[ $g->getCompanyGroupId() ] = true;
                }
            }
            
            // validate
            $cn = $this->form->getWidgetValue('company_group_id');
            if ($cn && isset($this->companyGroups[$cn]) == false) {
                $this->addError( t('Company group not found') );
                return false;
            }
        }
        
        
        return true;
    }
    
    
    
}

