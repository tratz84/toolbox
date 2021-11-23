<?php


namespace dataimport\container;


use core\exception\InvalidStateException;

class DataImportFormContainer {
    
    protected $list = array();
    
    
    public function __construct() {
        $this->addForm( t('Companies'), \customer\forms\CompanyForm::class, \customer\service\CompanyService::class, 'save' );
        $this->addForm( t('Persons'), \customer\forms\PersonForm::class, \customer\service\PersonService::class, 'save' );
        
        hook_eventbus_publish( $this, 'dataimport', 'construct' );
    }
    
    
    public function addForm( $description, $formClass, $serviceClass, $saveFunc ) {
        
        $uid = md5( $formClass . '/' . $serviceClass . '/' . $saveFunc );
        
        $this->list[$uid] = array(
            'uid'          => $uid,
            'description'  => $description,
            'formClass'    => $formClass,
            'serviceClass' => $serviceClass,
            'saveFunc'     => $saveFunc
        );
    }
    
    public function getForm( $uid ) {
        
        if (isset($this->list[$uid]) == false) {
            throw new InvalidStateException( 'Form not found' );
        }
        
        return $this->list[$uid];
    }
    
    public function getList() {
        return $this->list;
    }
    
}

