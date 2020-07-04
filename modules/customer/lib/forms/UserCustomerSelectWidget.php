<?php

namespace customer\forms;

use base\service\UserService;
use core\ObjectContainer;
use core\forms\DynamicSelectField;
use customer\service\CompanyService;
use customer\service\PersonService;

class UserCustomerSelectWidget extends DynamicSelectField {
    
    
    public function __construct($name='usercustomer_id', $defaultValue=null, $defaultText=null, $endpoint=null, $label=null) {
        
        if ($defaultText == null) $defaultText = t('Make your choice');
        if ($endpoint == null) $endpoint = '/?m=customer&c=usercustomer&a=select2';
        if ($label == null) $label = t('User/Customer');
        
        parent::__construct($name, $defaultValue, $defaultText, $endpoint, $label);
        
        
        hook_htmlscriptloader_enableGroup('user-customer-select-widget');
    }
    
    
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
        
        if (is_array($obj) && isset($obj['usercustomer_id'])) {
            if (strpos($obj['usercustomer_id'], 'company-') === 0) {
                $companyId = str_replace('company-', '', $obj['usercustomer_id']);
            }
            else if (strpos($obj['usercustomer_id'], 'person-') === 0) {
                $personId = str_replace('person-', '', $obj['usercustomer_id']);
            }
            else if (strpos($obj['usercustomer_id'], 'user-') === 0) {
                $personId = str_replace('user-', '', $obj['usercustomer_id']);
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
        else if ($userId) {
            $this->setValue('user-'.$userId);
            
            $us = ObjectContainer::getInstance()->get(UserService::class);
            $user = $ps->readUser($userId);
            
            $this->setDefaultText( (string)$user );
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
        $html = parent::render();
        
        
//         $i = ' <a href="javascript:void(0);" onclick="newCustomerPopup_Click();" class="fa fa-plus"></a>';
//         $html = str_replace('</select>', '</select>'.$i, $html);
        
        return $html;
    }
    
    
    
}


