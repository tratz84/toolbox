<?php

namespace customer\forms;

use customer\service\CompanyService;
use customer\service\PersonService;
use core\ObjectContainer;
use core\forms\DynamicSelectField;
use invoice\model\Offer;

class CustomerSelectWidget extends DynamicSelectField {
    
    protected $customerDeleted = false;
    

    public function __construct($name='customer_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = t('Make your choice');
        if ($endpoint == null) $endpoint = '/?m=customer&c=customer&a=select2';
        if ($label == null) $label = t('Customer');
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
        
        
        hook_htmlscriptloader_enableGroup('iban');
        hook_htmlscriptloader_enableGroup('customer-select-widget');
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $companyId = null;
        $personId = null;
        $this->customerDeleted = false;
        
        if (method_exists($obj, 'getCompanyId')) {
            $companyId = $obj->getCompanyId();
        }
        
        if (method_exists($obj, 'getPersonId')) {
            $personId = $obj->getPersonId();
        }
        
        if (is_array($obj) && isset($obj['customer_id'])) {
            if (strpos($obj['customer_id'], 'company-') === 0) {
                $companyId = str_replace('company-', '', $obj['customer_id']);
            }
            else if (strpos($obj['customer_id'], 'person-') === 0) {
                $personId = str_replace('person-', '', $obj['customer_id']);
            }
        }
        
        if ($companyId) {
            $this->setValue('company-'.$companyId);
            
            $cs = ObjectContainer::getInstance()->get(CompanyService::class);
            $company = $cs->readCompany($companyId, ['record-only' => true]);
            
            if ($company == null || $company->getDeleted()) {
                $this->customerDeleted = true;
            }
            if ($company) {
                $this->setDefaultText( $company->getCompanyName() );
            }
            else {
                $this->setDefaultText( 'company-'.$companyId );
            }
        }
        else if ($personId) {
            $this->setValue('person-'.$personId);
            
            $ps = ObjectContainer::getInstance()->get(PersonService::class);
            $person = $ps->readPerson($personId);
            if ($person == null || $person->getDeleted()) {
                $this->customerDeleted = true;
            }
            
            if ($person) {
                $this->setDefaultText( $person->getFullname() );
            }
            else {
                $this->setDefaultText( 'person-'.$personId );
            }
        } else {
            $this->setDefaultText( t('Make your choice') );
        }
    }
    
    
    
    public function fill($obj, $fields=array()) {
        $v = $this->getValue();
        
        if (method_exists($obj, 'setCompanyId')) {
            $obj->setCompanyId(0);
            if (strpos($v, 'company-') === 0) {
                $obj->setCompanyId( str_replace('company-', '', $v) );
            }
        }
        
        if (method_exists($obj, 'setPersonId')) {
            $obj->setPersonId(0);
            if (strpos($v, 'person-') === 0) {
                $obj->setPersonId( str_replace('person-', '', $v) );
            }
        }
    }
    
    
    
    public function render() {
        if ($this->customerDeleted) {
            $this->addContainerClass('customer-deleted');
        }
        
        $html = parent::render();
        
        
        $i = ' <a href="javascript:void(0);" onclick="newCustomerPopup_Click();" class="fa fa-plus"></a>';
        
        $html = str_replace('</select>', '</select>'.$i, $html);
        
        return $html;
    }
    
    
    
}


