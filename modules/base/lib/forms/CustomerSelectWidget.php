<?php

namespace base\forms;

use base\service\CompanyService;
use base\service\PersonService;
use core\ObjectContainer;
use core\forms\DynamicSelectField;
use invoice\model\Offer;

class CustomerSelectWidget extends DynamicSelectField {
    

    public function __construct($name='customer_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = 'Maak uw keuze';
        if ($endpoint == null) $endpoint = '/?m=base&c=customer&a=select2';
        if ($label == null) $label = 'Klant';
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $companyId = null;
        $personId = null;
        
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
            $name = $cs->getCompanyName($companyId);
            
            $this->setDefaultText( $name );
        }
        else if ($personId) {
            $this->setValue('person-'.$personId);
            
            $ps = ObjectContainer::getInstance()->get(PersonService::class);
            $fullname = $ps->getFullname($personId);
            
            $this->setDefaultText( $fullname );
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
}

