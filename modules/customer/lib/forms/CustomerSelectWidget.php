<?php

namespace customer\forms;

use customer\service\CompanyService;
use customer\service\PersonService;
use core\ObjectContainer;
use core\forms\DynamicSelectField;
use core\exception\InvalidStateException;

class CustomerSelectWidget extends DynamicSelectField {
    
    protected $customerDeleted = false;
    
    protected $customerType = null;

    public function __construct($name='customer_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = t('Make your choice');
        if ($endpoint == null) $endpoint = '/?m=customer&c=customer&a=select2';
        if ($label == null) $label = t('Customer');
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
        
        // these are necessary in the popup when clicked on the '+'-anchor
        hook_htmlscriptloader_enableGroup('iban');
        hook_htmlscriptloader_enableGroup('select-company-list-edit');
        hook_htmlscriptloader_enableGroup('select-person-list-edit');
        
        // 
        hook_htmlscriptloader_enableGroup('customer-select-widget');
    }
    
    
    public function setCustomerType( $ct ) {
        if ($ct != 'company' && $ct != 'person') {
            throw new InvalidStateException('Invalid customerType');
        }
        
        $this->customerType = $ct;
        
        $this->setEndpoint('/?m=customer&c=customer&a=select2&customer_type='.$this->customerType);
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $companyId = null;
        $personId = null;
        $this->customerDeleted = false;
        
        $fieldFound = false;
        
        if (is_object($obj) && method_exists($obj, 'getCompanyId')) {
            $companyId = $obj->getCompanyId();
            $fieldFound = true;
        }
        
        if (is_object($obj) && method_exists($obj, 'getPersonId')) {
            $personId = $obj->getPersonId();
            $fieldFound = true;
        }
        
        if (is_array($obj) && isset($obj[ $this->getName() ])) {
            $val = $obj[ $this->getName() ];
            
            if (strpos($val, 'company-') === 0) {
                $companyId = str_replace('company-', '', $val);
                $fieldFound = true;
            }
            else if (strpos($val, 'person-') === 0) {
                $personId = str_replace('person-', '', $val);
                $fieldFound = true;
            }
        }
        
        // no person_id or company_id in $obj? => skip
        if ($fieldFound == false) {
            return;
        }
        
        // gets kinda messy ;) when widget name is 'person_id' => ignore companyId & visa versa 
        if ($this->name == 'company_id') $personId = null;
        if ($this->name == 'person_id') $companyId = null;
        
        
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
        if ($v === null) $v = '';
        
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
        
        $opts = array();
        if ($this->customerType) {
            $opts['customer_type'] = $this->customerType;
        }
        
        $i = ' <a href="javascript:void(0);" onclick="newCustomerPopup_Click( this, '.esc_json_attr($opts).' );" class="fa fa-plus"></a>';
        
        $html = str_replace('</select>', '</select>'.$i, $html);
        
        return $html;
    }
    
    
    
}


