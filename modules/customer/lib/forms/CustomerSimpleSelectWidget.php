<?php

namespace customer\forms;

use core\ObjectContainer;
use core\forms\SelectField;
use customer\service\CompanyService;
use customer\service\CustomerService;
use customer\service\PersonService;

class CustomerSimpleSelectWidget extends SelectField {
    
    protected $customerDeleted = false;
    
    
    public function __construct($name, $value=null, $optionItems=array(), $label=null, $opts=array()) {
        
        if ($label == null) $label = t('Customer');
        
        // no options given? => load default
        if (!$optionItems || count($optionItems) == 0) {
            $customerService = object_container_get( CustomerService::class );
            $customers = $customerService->readAllCustomers();
            $optionItems[''] = t('Make your choice');
            
            foreach($customers as $cust) {
                $optionItems[ $cust['customer_id'] ] = $cust['name'];
            }
        }
        
        
        
        parent::__construct($name, $value, $optionItems, $label, $opts);
        
        hook_htmlscriptloader_enableGroup('iban');
        hook_htmlscriptloader_enableGroup('customer-select-widget');
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $companyId = null;
        $personId = null;
        $this->customerDeleted = false;
        
        if (is_object($obj) && method_exists($obj, 'getCompanyId')) {
            $companyId = $obj->getCompanyId();
        }
        
        if (is_object($obj) && method_exists($obj, 'getPersonId')) {
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
        }
        else if ($personId) {
            $this->setValue('person-'.$personId);
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
        $html = parent::render();
        
        $i = ' <a href="javascript:void(0);" onclick="newCustomerPopup_Click( this );" class="fa fa-plus"></a>';
        
        $html = str_replace('</select>', '</select>'.$i, $html);
        
        return $html;
    }
    
    
    
}


