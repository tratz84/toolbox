<?php

namespace customer\forms;

use base\service\UserService;
use core\ObjectContainer;
use core\forms\DynamicSelectField;
use customer\service\CompanyService;
use customer\service\PersonService;

class UserCustomerSelectWidget extends DynamicSelectField {
    
    protected $customerSupport = true;
    
    protected  $usercustomerDeleted = false;
    
    
    
    public function __construct($name='usercustomer_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = t('Make your choice');
        if ($label == null) $label = t('User/Customer');
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
        
        
        hook_htmlscriptloader_enableGroup('user-customer-select-widget');
        $this->addContainerClass('usercustomer-select-widget');
    }
    
    
    public function setCustomerSupport($bln) { $this->customerSupport = $bln; }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $companyId = null;
        $personId = null;
        $userId = null;
        
        if (method_exists($obj, 'getCompanyId')) {
            $companyId = $obj->getCompanyId();
        }
        
        if (method_exists($obj, 'getPersonId')) {
            $personId = $obj->getPersonId();
        }
        
        if (method_exists($obj, 'getUserId')) {
            $userId = $obj->getUserId();
        }
        
        $widget_name = $this->getName();
        if (is_array($obj) && isset($obj[$widget_name])) {
            if (strpos($obj[$widget_name], 'company-') === 0) {
                $companyId = str_replace('company-', '', $obj[$widget_name]);
            }
            else if (strpos($obj[$widget_name], 'person-') === 0) {
                $personId = str_replace('person-', '', $obj[$widget_name]);
            }
            else if (strpos($obj[$widget_name], 'user-') === 0) {
                $userId = str_replace('user-', '', $obj[$widget_name]);
            }
        }
        
        if ($companyId) {
            $this->setValue('company-'.$companyId);
            
            $cs = ObjectContainer::getInstance()->get(CompanyService::class);
            
            $company = $cs->readCompany($companyId, ['record-only' => true]);
            
            if ($company == null || $company->getDeleted()) {
                $this->usercustomerDeleted = true;
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
                $this->usercustomerDeleted = true;
            }
            
            if ($person) {
                $this->setDefaultText( $person->getFullname() );
            }
            else {
                $this->setDefaultText( 'person-'.$personId );
            }
        }
        else if ($userId) {
            $this->setValue('user-'.$userId);
            
            $us = ObjectContainer::getInstance()->get(UserService::class);
            $user = $us->readUser($userId);
            
            if ($user) {
                $this->setDefaultText( (string)$user );
            }
            else {
                $this->usercustomerDeleted = true;
                $this->setDefaultText( 'user-'.$userId );
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
        
        if (method_exists($obj, 'setUserId')) {
            $obj->setUserId(0);
            if (strpos($v, 'user-') === 0) {
                $obj->setUserId( str_replace('user-', '', $v) );
            }
        }
    }
    
    
    
    public function render() {
        if ($this->usercustomerDeleted) {
            $this->addContainerClass('usercustomer-deleted');
        }
        
        if ($this->customerSupport == false) {
            $this->endpoint = '/?m=customer&c=usercustomer&a=select2&src=user';
        } else {
            $this->endpoint = '/?m=customer&c=usercustomer&a=select2';
        }
        
        $html = parent::render();
        
        
//         $i = ' <a href="javascript:void(0);" onclick="newCustomerPopup_Click();" class="fa fa-plus"></a>';
//         $html = str_replace('</select>', '</select>'.$i, $html);
        
        return $html;
    }
    
    
    
}


